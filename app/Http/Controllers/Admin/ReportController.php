<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Car;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Helper to resolve start_date, end_date, and readable label for filters.
     */
    protected function resolvePeriod(Request $request): array
    {
        $period = $request->input('period', 'month');
        $customStart = $request->input('start_date');
        $customEnd = $request->input('end_date');

        if ($period === 'custom' && $customStart && $customEnd) {
            $startDate = Carbon::parse($customStart)->startOfDay();
            $endDate = Carbon::parse($customEnd)->endOfDay();
            $label = $startDate->translatedFormat('d M Y') . ' s/d ' . $endDate->translatedFormat('d M Y');
        } elseif ($period === 'today') {
            $startDate = Carbon::today()->startOfDay();
            $endDate = Carbon::today()->endOfDay();
            $label = 'Hari Ini (' . $startDate->translatedFormat('d M Y') . ')';
        } elseif ($period === 'week') {
            $startDate = Carbon::now()->startOfWeek();
            $endDate = Carbon::now()->endOfWeek();
            $label = 'Minggu Ini (' . $startDate->translatedFormat('d M') . ' - ' . $endDate->translatedFormat('d M Y') . ')';
        } elseif ($period === 'year') {
            $startDate = Carbon::now()->startOfYear();
            $endDate = Carbon::now()->endOfYear();
            $label = 'Tahun Ini (' . $startDate->format('Y') . ')';
        } else {
            // Default: 'month'
            $period = 'month';
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
            $label = 'Bulan Ini (' . $startDate->translatedFormat('F Y') . ')';
        }

        return [
            'period' => $period,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'custom_start' => $customStart ?: $startDate->toDateString(),
            'custom_end' => $customEnd ?: $endDate->toDateString(),
            'label' => $label,
        ];
    }

    /**
     * Main Overview for Reports & Analytics.
     */
    public function index(Request $request): View
    {
        $activeTab = $request->input('tab', 'transactions');

        if ($activeTab === 'vehicles') {
            return $this->vehicles($request);
        }

        return $this->transactions($request);
    }

    /**
     * Laporan Transaksi Masuk & Keluar.
     */
    public function transactions(Request $request): View
    {
        $dateFilter = $this->resolvePeriod($request);

        $query = Booking::with(['user', 'car'])
            ->whereBetween('created_at', [$dateFilter['start_date'], $dateFilter['end_date']]);

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Summary Calculations
        $totalRentalFee = (clone $query)->sum('car_rental_fee');
        $totalDriverFee = (clone $query)->sum('driver_fee');
        $totalLatePenalty = (clone $query)->sum('late_penalty_fee');
        $totalOverall = (clone $query)->sum('total_price');

        $countTotal = (clone $query)->count();
        $countCompleted = (clone $query)->where('status', Booking::STATUS_COMPLETED)->count();
        $countActive = (clone $query)->whereIn('status', [
            Booking::STATUS_APPROVED,
            Booking::STATUS_DELIVERING,
            Booking::STATUS_IN_USE,
        ])->count();
        $countCancelled = (clone $query)->whereIn('status', [
            Booking::STATUS_CANCELLED,
            Booking::STATUS_REJECTED,
        ])->count();

        $bookings = $query->latest('created_at')->paginate(20)->withQueryString();

        return view('admin.reports.transactions', compact(
            'dateFilter',
            'bookings',
            'totalRentalFee',
            'totalDriverFee',
            'totalLatePenalty',
            'totalOverall',
            'countTotal',
            'countCompleted',
            'countActive',
            'countCancelled'
        ));
    }

    /**
     * Export Laporan Transaksi ke Excel (CSV UTF-8 BOM).
     */
    public function exportTransactionsExcel(Request $request)
    {
        $dateFilter = $this->resolvePeriod($request);

        $query = Booking::with(['user', 'car'])
            ->whereBetween('created_at', [$dateFilter['start_date'], $dateFilter['end_date']]);

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $bookings = $query->latest('created_at')->get();

        $filename = 'Laporan-Transaksi-' . date('Ymd-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($bookings, $dateFilter) {
            $output = fopen('php://output', 'w');
            // Write UTF-8 BOM so Excel opens accented & special chars properly
            fputs($output, "\xEF\xBB\xBF");

            // Header titles
            fputcsv($output, ['AUTORENT INDONESIA - LAPORAN TRANSAKSI PENYEWAAN MOBIL']);
            fputcsv($output, ['Periode:', $dateFilter['label']]);
            fputcsv($output, ['Dicetak Pada:', now()->translatedFormat('d F Y, H:i') . ' WIB']);
            fputcsv($output, []); // Empty row

            // Table Header
            fputcsv($output, [
                'No',
                'Kode Booking',
                'Tanggal Booking',
                'Nama Customer',
                'Nomor Telepon / WA',
                'NIK KTP',
                'Armada Mobil',
                'Plat Nomor',
                'Tanggal Mulai',
                'Tanggal Selesai',
                'Durasi (Hari)',
                'Layanan Driver',
                'Biaya Sewa Mobil (Rp)',
                'Biaya Driver (Rp)',
                'Denda Terlambat (Rp)',
                'Total Biaya (Rp)',
                'Status Transaksi',
                'Alamat Pengantaran',
            ]);

            $no = 1;
            $sumRental = 0;
            $sumDriver = 0;
            $sumPenalty = 0;
            $sumTotal = 0;

            foreach ($bookings as $b) {
                $sumRental += $b->car_rental_fee;
                $sumDriver += $b->driver_fee;
                $sumPenalty += $b->late_penalty_fee;
                $sumTotal += $b->total_price;

                fputcsv($output, [
                    $no++,
                    $b->booking_code,
                    $b->created_at ? $b->created_at->format('d/m/Y H:i') : '-',
                    $b->user?->name ?: '-',
                    $b->user?->phone_number ?: '-',
                    "'" . ($b->user?->nik_ktp ?: '-'),
                    $b->car ? "{$b->car->brand} {$b->car->model}" : '-',
                    $b->car?->license_plate ?: '-',
                    $b->start_date ? $b->start_date->format('d/m/Y') : '-',
                    $b->end_date ? $b->end_date->format('d/m/Y') : '-',
                    $b->duration_days,
                    $b->driver_service ? 'Dengan Driver' : 'Lepas Kunci',
                    $b->car_rental_fee,
                    $b->driver_fee,
                    $b->late_penalty_fee,
                    $b->total_price,
                    $b->status_label,
                    $b->delivery_address,
                ]);
            }

            // Totals Row
            fputcsv($output, []);
            fputcsv($output, [
                'TOTAL REKAPITULASI',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                $sumRental,
                $sumDriver,
                $sumPenalty,
                $sumTotal,
                '',
                '',
            ]);

            fclose($output);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Cetak PDF Laporan Transaksi (Printable layout).
     */
    public function printTransactionsPdf(Request $request): View
    {
        $dateFilter = $this->resolvePeriod($request);

        $query = Booking::with(['user', 'car'])
            ->whereBetween('created_at', [$dateFilter['start_date'], $dateFilter['end_date']]);

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $bookings = $query->latest('created_at')->get();

        $totalRentalFee = $bookings->sum('car_rental_fee');
        $totalDriverFee = $bookings->sum('driver_fee');
        $totalLatePenalty = $bookings->sum('late_penalty_fee');
        $totalOverall = $bookings->sum('total_price');

        return view('admin.reports.print_transactions', compact(
            'dateFilter',
            'bookings',
            'totalRentalFee',
            'totalDriverFee',
            'totalLatePenalty',
            'totalOverall'
        ));
    }

    /**
     * Laporan Penggunaan Kendaraan & Performa Armada.
     */
    public function vehicles(Request $request): View
    {
        $dateFilter = $this->resolvePeriod($request);

        $cars = Car::with(['bookings' => function ($q) use ($dateFilter) {
            $q->whereBetween('created_at', [$dateFilter['start_date'], $dateFilter['end_date']]);
        }])->get();

        $vehicleStats = $cars->map(function ($car) {
            $bookings = $car->bookings;

            $totalFrequency = $bookings->count();
            $totalRevenue = $bookings->sum('total_price');
            $rentalFee = $bookings->sum('car_rental_fee');
            $totalDays = $bookings->sum('duration_days');

            // Find last returned/completed booking to get last condition check
            $lastReturned = Booking::where('car_id', $car->id)
                ->whereIn('status', [Booking::STATUS_COMPLETED, Booking::STATUS_RETURNED])
                ->latest('updated_at')
                ->first();

            $lastCondition = $lastReturned && $lastReturned->notes
                ? $lastReturned->notes
                : ($lastReturned ? 'Pemeriksaan selesai, kondisi normal.' : 'Belum ada catatan pengembalian.');

            return (object) [
                'id' => $car->id,
                'brand' => $car->brand,
                'model' => $car->model,
                'full_name' => $car->full_name,
                'license_plate' => $car->license_plate,
                'capacity' => $car->capacity,
                'price_per_day' => $car->price_per_day,
                'status' => $car->status,
                'image_url' => $car->image_url,
                'rental_frequency' => $totalFrequency,
                'total_revenue' => $totalRevenue,
                'rental_fee' => $rentalFee,
                'total_days' => $totalDays,
                'last_condition' => $lastCondition,
                'last_returned_date' => $lastReturned?->end_date ? $lastReturned->end_date->format('d M Y') : '-',
            ];
        });

        // Most rented car (highest frequency, fallback highest revenue)
        $mostRentedCar = $vehicleStats->sortByDesc('rental_frequency')->first();
        if ($mostRentedCar && $mostRentedCar->rental_frequency === 0) {
            $mostRentedCar = null;
        }

        // Highest revenue car
        $topRevenueCar = $vehicleStats->sortByDesc('total_revenue')->first();
        if ($topRevenueCar && $topRevenueCar->total_revenue == 0) {
            $topRevenueCar = null;
        }

        $totalFleetRevenue = $vehicleStats->sum('total_revenue');
        $totalFleetTrips = $vehicleStats->sum('rental_frequency');
        $totalDaysRented = $vehicleStats->sum('total_days');

        return view('admin.reports.vehicles', compact(
            'dateFilter',
            'vehicleStats',
            'mostRentedCar',
            'topRevenueCar',
            'totalFleetRevenue',
            'totalFleetTrips',
            'totalDaysRented'
        ));
    }

    /**
     * Export Laporan Penggunaan Kendaraan ke Excel (CSV).
     */
    public function exportVehiclesExcel(Request $request)
    {
        $dateFilter = $this->resolvePeriod($request);

        $cars = Car::with(['bookings' => function ($q) use ($dateFilter) {
            $q->whereBetween('created_at', [$dateFilter['start_date'], $dateFilter['end_date']]);
        }])->get();

        $filename = 'Laporan-Penggunaan-Kendaraan-' . date('Ymd-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($cars, $dateFilter) {
            $output = fopen('php://output', 'w');
            fputs($output, "\xEF\xBB\xBF");

            fputcsv($output, ['AUTORENT INDONESIA - LAPORAN PENGGUNAAN & PERFORMA ARMADA']);
            fputcsv($output, ['Periode:', $dateFilter['label']]);
            fputcsv($output, ['Dicetak Pada:', now()->translatedFormat('d F Y, H:i') . ' WIB']);
            fputcsv($output, []);

            fputcsv($output, [
                'No',
                'Merek & Tipe Mobil',
                'Nomor Plat',
                'Kapasitas (Kursi)',
                'Tarif Sewa/Hari (Rp)',
                'Status Armada',
                'Frekuensi Sewa (Kali)',
                'Total Hari Tersewa',
                'Total Pendapatan Unit (Rp)',
                'Catatan Kondisi Terakhir Saat Dikembalikan',
            ]);

            $no = 1;
            $sumFreq = 0;
            $sumDays = 0;
            $sumRevenue = 0;

            foreach ($cars as $car) {
                $bookings = $car->bookings;
                $freq = $bookings->count();
                $revenue = $bookings->sum('total_price');
                $days = $bookings->sum('duration_days');

                $lastReturned = Booking::where('car_id', $car->id)
                    ->whereIn('status', [Booking::STATUS_COMPLETED, Booking::STATUS_RETURNED])
                    ->latest('updated_at')
                    ->first();

                $condition = $lastReturned && $lastReturned->notes
                    ? $lastReturned->notes
                    : ($lastReturned ? 'Pemeriksaan selesai, unit baik.' : 'Belum ada riwayat pengembalian.');

                $sumFreq += $freq;
                $sumDays += $days;
                $sumRevenue += $revenue;

                fputcsv($output, [
                    $no++,
                    "{$car->brand} {$car->model}",
                    $car->license_plate,
                    $car->capacity,
                    $car->price_per_day,
                    ucfirst($car->status),
                    $freq,
                    $days,
                    $revenue,
                    $condition,
                ]);
            }

            fputcsv($output, []);
            fputcsv($output, [
                'TOTAL KESELURUHAN',
                '',
                '',
                '',
                '',
                '',
                $sumFreq,
                $sumDays,
                $sumRevenue,
                '',
            ]);

            fclose($output);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Cetak PDF Laporan Penggunaan Kendaraan.
     */
    public function printVehiclesPdf(Request $request): View
    {
        $dateFilter = $this->resolvePeriod($request);

        $cars = Car::with(['bookings' => function ($q) use ($dateFilter) {
            $q->whereBetween('created_at', [$dateFilter['start_date'], $dateFilter['end_date']]);
        }])->get();

        $vehicleStats = $cars->map(function ($car) {
            $bookings = $car->bookings;

            $lastReturned = Booking::where('car_id', $car->id)
                ->whereIn('status', [Booking::STATUS_COMPLETED, Booking::STATUS_RETURNED])
                ->latest('updated_at')
                ->first();

            return (object) [
                'full_name' => $car->full_name,
                'license_plate' => $car->license_plate,
                'capacity' => $car->capacity,
                'price_per_day' => $car->price_per_day,
                'status' => $car->status,
                'rental_frequency' => $bookings->count(),
                'total_revenue' => $bookings->sum('total_price'),
                'total_days' => $bookings->sum('duration_days'),
                'last_condition' => $lastReturned && $lastReturned->notes ? $lastReturned->notes : 'Kondisi normal / prima',
            ];
        });

        $mostRentedCar = $vehicleStats->sortByDesc('rental_frequency')->first();
        if ($mostRentedCar && $mostRentedCar->rental_frequency === 0) {
            $mostRentedCar = null;
        }

        $totalFleetRevenue = $vehicleStats->sum('total_revenue');
        $totalFleetTrips = $vehicleStats->sum('rental_frequency');

        return view('admin.reports.print_vehicles', compact(
            'dateFilter',
            'vehicleStats',
            'mostRentedCar',
            'totalFleetRevenue',
            'totalFleetTrips'
        ));
    }
}
