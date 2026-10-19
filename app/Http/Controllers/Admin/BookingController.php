<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompleteBookingRequest;
use App\Models\Booking;
use App\Models\Car;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    /**
     * Display a listing of bookings with optional status filter.
     */
    public function index(Request $request): View
    {
        $query = Booking::with(['user', 'car'])->latest();

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('booking_code', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('phone_number', 'like', "%{$search}%");
                  })
                  ->orWhereHas('car', function ($cq) use ($search) {
                      $cq->where('brand', 'like', "%{$search}%")
                         ->orWhere('model', 'like', "%{$search}%")
                         ->orWhere('license_plate', 'like', "%{$search}%");
                  });
            });
        }

        $bookings = $query->paginate(12)->withQueryString();

        $statusCounts = [
            'all' => Booking::count(),
            Booking::STATUS_PENDING => Booking::where('status', Booking::STATUS_PENDING)->count(),
            Booking::STATUS_APPROVED => Booking::where('status', Booking::STATUS_APPROVED)->count(),
            Booking::STATUS_DELIVERING => Booking::where('status', Booking::STATUS_DELIVERING)->count(),
            Booking::STATUS_IN_USE => Booking::where('status', Booking::STATUS_IN_USE)->count(),
            Booking::STATUS_RETURNED => Booking::where('status', Booking::STATUS_RETURNED)->count(),
            Booking::STATUS_COMPLETED => Booking::where('status', Booking::STATUS_COMPLETED)->count(),
            Booking::STATUS_CANCELLED => Booking::where('status', Booking::STATUS_CANCELLED)->count(),
        ];

        return view('admin.bookings.index', compact('bookings', 'statusCounts'));
    }

    /**
     * Show booking details, customer information, delivery location, and action forms.
     */
    public function show(Booking $booking): View
    {
        $booking->load(['user', 'car']);

        return view('admin.bookings.show', compact('booking'));
    }

    /**
     * Step 1 Admin: Confirm / Approve Booking
     * Changes status from 'pending' to 'approved', sets car status to 'rented'.
     */
    public function approve(Booking $booking): RedirectResponse
    {
        if ($booking->status !== Booking::STATUS_PENDING) {
            return back()->with('error', 'Hanya pesanan berstatus Pending yang dapat disetujui.');
        }

        $booking->update([
            'status' => Booking::STATUS_APPROVED,
        ]);

        $booking->car->update([
            'status' => 'rented',
        ]);

        \App\Services\NotificationService::sendToUser(
            user: $booking->user_id,
            title: 'Status Pesanan Diperbarui',
            message: "Status pesanan Anda (#{$booking->booking_code}) telah diperbarui menjadi Disetujui / Siap Dikirim!",
            url: route('customer.bookings.show', $booking),
            type: 'status_approved',
            icon: 'fa-circle-check',
            data: ['booking_code' => $booking->booking_code, 'status' => 'approved']
        );

        return back()->with('success', "Pesanan #{$booking->booking_code} berhasil disetujui! Status: Siap Dikirim.");
    }

    /**
     * Admin: Reject Booking
     */
    public function reject(Request $request, Booking $booking): RedirectResponse
    {
        if ($booking->status !== Booking::STATUS_PENDING) {
            return back()->with('error', 'Hanya pesanan berstatus Pending yang dapat ditolak.');
        }

        $reason = $request->input('reason', 'Ditolak oleh admin.');
        $booking->update([
            'status' => Booking::STATUS_REJECTED,
            'notes' => ($booking->notes ? $booking->notes . ' | ' : '') . 'Alasan Penolakan: ' . $reason,
        ]);

        \App\Services\NotificationService::sendToUser(
            user: $booking->user_id,
            title: 'Status Pesanan Diperbarui',
            message: "Status pesanan Anda (#{$booking->booking_code}) telah diperbarui menjadi Ditolak.",
            url: route('customer.bookings.show', $booking),
            type: 'status_rejected',
            icon: 'fa-circle-xmark',
            data: ['booking_code' => $booking->booking_code, 'status' => 'rejected']
        );

        return back()->with('success', "Pesanan #{$booking->booking_code} telah ditolak.");
    }

    /**
     * Step 2 Admin: Send Car (On Delivery / Dispatch with Courier & ETA)
     */
    public function deliver(Request $request, Booking $booking): RedirectResponse
    {
        if ($booking->status !== Booking::STATUS_APPROVED) {
            return back()->with('error', 'Pesanan harus disetujui terlebih dahulu sebelum dikirim.');
        }

        $validated = $request->validate([
            'courier_name' => ['required', 'string', 'max:100'],
            'courier_phone' => ['required', 'string', 'max:25'],
            'estimated_duration_minutes' => ['required', 'integer', 'min:5', 'max:720'],
            'office_origin_address' => ['nullable', 'string', 'max:500'],
        ], [
            'courier_name.required' => 'Nama driver/kurir pengantar wajib diisi.',
            'courier_phone.required' => 'Nomor WhatsApp/kontak driver pengantar wajib diisi.',
            'estimated_duration_minutes.required' => 'Estimasi durasi pengantaran (menit) wajib diisi.',
            'estimated_duration_minutes.min' => 'Estimasi durasi minimal 5 menit.',
        ]);

        $durationMinutes = (int) $validated['estimated_duration_minutes'];
        $dispatchTime = now();
        $estimatedArrivalTime = now()->addMinutes($durationMinutes);

        $booking->update([
            'courier_name' => $validated['courier_name'],
            'courier_phone' => $validated['courier_phone'],
            'dispatch_time' => $dispatchTime,
            'estimated_duration_minutes' => $durationMinutes,
            'estimated_arrival_time' => $estimatedArrivalTime,
            'office_origin_address' => $validated['office_origin_address'] ?: 'Kantor Pusat AutoRent, Jl. Merdeka No. 1, Gambir, Jakarta Pusat',
            'status' => Booking::STATUS_DELIVERING,
        ]);

        $etaClock = $estimatedArrivalTime->translatedFormat('H:i') . ' WIB';

        \App\Services\NotificationService::sendToUser(
            user: $booking->user_id,
            title: 'Status Pesanan Diperbarui',
            message: "Status pesanan Anda (#{$booking->booking_code}) telah diperbarui menjadi On Delivery! Driver {$validated['courier_name']} sedang dalam perjalanan ke alamat Anda.",
            url: route('customer.bookings.show', $booking),
            type: 'status_delivering',
            icon: 'fa-truck-fast',
            data: ['booking_code' => $booking->booking_code, 'status' => 'delivering']
        );

        return back()->with('success', "Mobil untuk booking #{$booking->booking_code} diberangkatkan bersama Driver {$validated['courier_name']}! Estimasi tiba: {$etaClock} ({$durationMinutes} menit).");
    }

    /**
     * Step 3 Admin: Customer received car (In Use)
     */
    public function inUse(Booking $booking): RedirectResponse
    {
        if ($booking->status !== Booking::STATUS_DELIVERING) {
            return back()->with('error', 'Mobil harus berstatus Dalam Pengiriman terlebih dahulu.');
        }

        $booking->update([
            'status' => Booking::STATUS_IN_USE,
        ]);

        \App\Services\NotificationService::sendToUser(
            user: $booking->user_id,
            title: 'Status Pesanan Diperbarui',
            message: "Status pesanan Anda (#{$booking->booking_code}) telah diperbarui menjadi Sedang Digunakan (In Use). Selamat menikmati perjalanan!",
            url: route('customer.bookings.show', $booking),
            type: 'status_in_use',
            icon: 'fa-car-side',
            data: ['booking_code' => $booking->booking_code, 'status' => 'in_use']
        );

        return back()->with('success', "Mobil telah diterima oleh customer. Status berubah menjadi Sedang Digunakan (In Use).");
    }

    /**
     * Step 4 Admin: Car Returned / Picked Up
     */
    public function returnCar(Booking $booking): RedirectResponse
    {
        if ($booking->status !== Booking::STATUS_IN_USE) {
            return back()->with('error', 'Hanya pesanan yang sedang digunakan yang dapat ditandai sebagai dikembalikan.');
        }

        $booking->update([
            'status' => Booking::STATUS_RETURNED,
        ]);

        \App\Services\NotificationService::sendToUser(
            user: $booking->user_id,
            title: 'Status Pesanan Diperbarui',
            message: "Status pesanan Anda (#{$booking->booking_code}) telah diperbarui menjadi Dikembalikan. Petugas sedang melakukan cek fisik unit.",
            url: route('customer.bookings.show', $booking),
            type: 'status_returned',
            icon: 'fa-arrow-rotate-left',
            data: ['booking_code' => $booking->booking_code, 'status' => 'returned']
        );

        return back()->with('success', "Mobil telah dikembalikan/diambil kembali. Silakan lakukan pengecekan fisik mobil dan input denda jika ada.");
    }

    /**
     * Step 5 Admin: Complete Rental & Calculate Penalty
     * Admin checks physical condition & returns penalty, updates total price,
     * marks booking completed, and car status automatically returns to 'available'.
     */
    public function complete(CompleteBookingRequest $request, Booking $booking): RedirectResponse
    {
        if (!in_array($booking->status, [Booking::STATUS_IN_USE, Booking::STATUS_RETURNED])) {
            return back()->with('error', 'Pesanan belum dalam tahap penyelesaian atau pengembalian.');
        }

        $latePenalty = (float) $request->input('late_penalty_fee', 0);
        $conditionNotes = $request->input('notes');

        $newTotalPrice = (float) $booking->car_rental_fee + (float) $booking->driver_fee + $latePenalty;

        $existingNotes = $booking->notes ? $booking->notes . "\n" : '';
        $finalNotes = $existingNotes . ($conditionNotes ? "Pemeriksaan Selesai: {$conditionNotes}" : "Pemeriksaan Selesai tanpa keluhan fisik.");

        $booking->update([
            'late_penalty_fee' => $latePenalty,
            'total_price' => $newTotalPrice,
            'status' => Booking::STATUS_COMPLETED,
            'notes' => $finalNotes,
        ]);

        // Car status automatically returns to available
        $booking->car->update([
            'status' => 'available',
        ]);

        \App\Services\NotificationService::sendToUser(
            user: $booking->user_id,
            title: 'Status Pesanan Diperbarui',
            message: "Status pesanan Anda (#{$booking->booking_code}) telah diperbarui menjadi Selesai (Completed). Terima kasih telah menggunakan layanan AutoRent!",
            url: route('customer.bookings.show', $booking),
            type: 'status_completed',
            icon: 'fa-award',
            data: ['booking_code' => $booking->booking_code, 'status' => 'completed']
        );

        $message = "Transaksi #{$booking->booking_code} dinyatakan Selesai (Completed)! Status mobil otomatis kembali Tersedia (Available).";
        if ($latePenalty > 0) {
            $message .= " Denda sebesar Rp " . number_format($latePenalty, 0, ',', '.') . " telah ditambahkan ke tagihan.";
        }

        return redirect()->route('admin.bookings.show', $booking)->with('success', $message);
    }
}
