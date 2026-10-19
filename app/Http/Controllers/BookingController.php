<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Car;
use App\Services\RentalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function __construct(
        protected RentalService $rentalService
    ) {}

    /**
     * Show the booking form for a car.
     */
    public function create(Car $car): View|RedirectResponse
    {
        if ($car->status === 'maintenance') {
            return redirect()->route('cars.show', $car)
                ->with('error', 'Mobil ini saat ini sedang dalam pemeliharaan (maintenance) dan tidak dapat dibooking.');
        }

        // Active bookings for schedule reference
        $busySchedules = $car->bookings()
            ->whereIn('status', ['approved', 'delivering', 'in_use'])
            ->where('end_date', '>=', now()->toDateString())
            ->orderBy('start_date')
            ->get(['start_date', 'end_date']);

        $driverDailyRate = RentalService::DRIVER_DAILY_RATE;

        return view('bookings.create', compact('car', 'busySchedules', 'driverDailyRate'));
    }

    /**
     * Store customer booking request.
     */
    public function store(StoreBookingRequest $request): RedirectResponse
    {
        $car = Car::findOrFail($request->car_id);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $withDriver = $request->boolean('driver_service');

        // 1. Validasi Ketersediaan Mobil (Overlapping Schedule Check)
        if (!$car->isAvailableForDates($startDate, $endDate)) {
            return back()
                ->withInput()
                ->withErrors([
                    'start_date' => 'Maaf! Mobil ini sudah terpesan/digunakan pada rentang tanggal tersebut. Silakan pilih rentang tanggal lain atau mobil lain.',
                ]);
        }

        // 2. Kalkulasi Biaya Otomatis melalui RentalService
        $fees = $this->rentalService->calculateFees($car, $startDate, $endDate, $withDriver);
        $bookingCode = $this->rentalService->generateBookingCode();

        // 3. Simpan Booking ke Database
        $booking = Booking::create([
            'booking_code' => $bookingCode,
            'user_id' => Auth::id(),
            'car_id' => $car->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'duration_days' => $fees['duration_days'],
            'driver_service' => $withDriver,
            'delivery_address' => $request->input('delivery_address'),
            'car_rental_fee' => $fees['car_rental_fee'],
            'driver_fee' => $fees['driver_fee'],
            'late_penalty_fee' => 0,
            'total_price' => $fees['total_price'],
            'status' => Booking::STATUS_PENDING,
            'notes' => $request->input('notes'),
        ]);

        // Kirim Notifikasi Real-time ke Dashboard Admin
        \App\Services\NotificationService::sendToAdmin(
            title: 'Booking Baru Masuk',
            message: "Booking Baru #{$booking->booking_code} telah masuk dari " . Auth::user()->name . " ({$car->brand} {$car->model})!",
            url: route('admin.bookings.show', $booking),
            type: 'new_booking',
            icon: 'fa-calendar-plus',
            data: ['booking_code' => $booking->booking_code, 'booking_id' => $booking->id]
        );

        return redirect()->route('customer.bookings.show', $booking)
            ->with('success', "Booking #{$booking->booking_code} berhasil diajukan! Menunggu verifikasi dari Admin.");
    }

    /**
     * Customer bookings history and tracking list.
     */
    public function index(Request $request): View
    {
        $query = Auth::user()->bookings()->with('car')->latest();

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $bookings = $query->paginate(10)->withQueryString();

        return view('customer.bookings.index', compact('bookings'));
    }

    /**
     * Customer booking detail & status tracking timeline.
     */
    public function show(Booking $booking): View|RedirectResponse
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak. Anda tidak memiliki akses ke pesanan ini.');
        }

        $booking->load(['car', 'user']);

        return view('customer.bookings.show', compact('booking'));
    }

    /**
     * Cancel booking by customer (only if pending).
     */
    public function cancel(Booking $booking): RedirectResponse
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        if (!$booking->canCancel()) {
            return back()->with('error', 'Pesanan tidak dapat dibatalkan karena sudah dalam proses pengiriman atau konfirmasi.');
        }

        $booking->update([
            'status' => Booking::STATUS_CANCELLED,
            'notes' => ($booking->notes ? $booking->notes . ' | ' : '') . 'Dibatalkan oleh pelanggan pada ' . now()->format('d M Y H:i'),
        ]);

        // Notifikasi ke Admin
        \App\Services\NotificationService::sendToAdmin(
            title: 'Pesanan Dibatalkan',
            message: "Pesanan #{$booking->booking_code} telah dibatalkan oleh pelanggan " . Auth::user()->name . ".",
            url: route('admin.bookings.show', $booking),
            type: 'booking_cancelled',
            icon: 'fa-ban',
            data: ['booking_code' => $booking->booking_code, 'booking_id' => $booking->id]
        );

        return back()->with('success', "Pesanan #{$booking->booking_code} berhasil dibatalkan.");
    }
}
