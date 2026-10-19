@extends('layouts.app')

@section('title', 'Laporan Transaksi - Admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="{ customDateOpen: '{{ $dateFilter['period'] }}' === 'custom' }">
    <!-- Header & Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('admin.dashboard') }}" class="text-xs text-slate-400 hover:text-blue-600">Admin</a>
                <span class="text-xs text-slate-300">/</span>
                <span class="text-xs font-semibold text-slate-600">Laporan & Rekapitulasi</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight mt-1">Laporan Transaksi Masuk & Keluar</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Rekapitulasi keuangan, pendapatan sewa harian, denda keterlambatan, dan status transaksi.
            </p>
        </div>

        <!-- Export Buttons -->
        <div class="flex items-center space-x-2.5">
            <a href="{{ route('admin.reports.transactions.pdf', request()->query()) }}" target="_blank"
               class="px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition shadow-sm flex items-center space-x-2">
                <i class="fa-solid fa-file-pdf"></i>
                <span>Cetak PDF</span>
            </a>
            <a href="{{ route('admin.reports.transactions.excel', request()->query()) }}"
               class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-sm flex items-center space-x-2">
                <i class="fa-solid fa-file-excel"></i>
                <span>Export Excel</span>
            </a>
        </div>
    </div>

    <!-- Tab Menu Laporan -->
    <div class="flex items-center space-x-3 border-b border-slate-200 mb-6">
        <a href="{{ route('admin.reports.transactions') }}"
           class="pb-3 border-b-2 font-bold text-xs sm:text-sm flex items-center space-x-2 transition border-blue-600 text-blue-600">
            <i class="fa-solid fa-money-bill-transfer"></i>
            <span>Laporan Transaksi Keuangan</span>
        </a>
        <a href="{{ route('admin.reports.vehicles') }}"
           class="pb-3 border-b-2 font-bold text-xs sm:text-sm flex items-center space-x-2 transition border-transparent text-slate-500 hover:text-slate-800">
            <i class="fa-solid fa-car-side"></i>
            <span>Laporan Penggunaan Kendaraan</span>
        </a>
    </div>

    <!-- Filter Rentang Waktu & Status Form -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm mb-6">
        <form action="{{ route('admin.reports.transactions') }}" method="GET" class="space-y-4">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <!-- Period Buttons -->
                <div class="flex items-center space-x-2 flex-wrap gap-y-2">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider mr-1">Rentang:</span>

                    <button type="submit" name="period" value="today"
                            class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $dateFilter['period'] === 'today' ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Harian (Hari Ini)
                    </button>

                    <button type="submit" name="period" value="week"
                            class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $dateFilter['period'] === 'week' ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Mingguan
                    </button>

                    <button type="submit" name="period" value="month"
                            class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $dateFilter['period'] === 'month' ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Bulanan
                    </button>

                    <button type="submit" name="period" value="year"
                            class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $dateFilter['period'] === 'year' ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Tahunan
                    </button>

                    <button type="button" @click="customDateOpen = !customDateOpen"
                            class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center space-x-1 {{ $dateFilter['period'] === 'custom' ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        <i class="fa-solid fa-calendar-days text-[11px]"></i>
                        <span>Pilih Tanggal Kustom</span>
                    </button>
                </div>

                <!-- Status Filter Dropdown -->
                <div class="flex items-center space-x-2">
                    <label for="status" class="text-xs font-bold text-slate-500 uppercase tracking-wider">Status:</label>
                    <select name="status" id="status" onchange="this.form.submit()"
                            class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Status</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai (Completed)</option>
                        <option value="in_use" {{ request('status') === 'in_use' ? 'selected' : '' }}>Sedang Digunakan</option>
                        <option value="delivering" {{ request('status') === 'delivering' ? 'selected' : '' }}>On Delivery</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
            </div>

            <!-- Custom Date Range Picker Container -->
            <div x-show="customDateOpen" x-cloak class="pt-3 border-t border-slate-100 flex flex-col sm:flex-row items-center gap-3">
                <input type="hidden" name="period" value="custom">

                <div class="flex items-center space-x-2 w-full sm:w-auto">
                    <span class="text-xs font-bold text-slate-500">Dari:</span>
                    <input type="date" name="start_date" value="{{ $dateFilter['custom_start'] }}"
                           class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="flex items-center space-x-2 w-full sm:w-auto">
                    <span class="text-xs font-bold text-slate-500">Sampai:</span>
                    <input type="date" name="end_date" value="{{ $dateFilter['custom_end'] }}"
                           class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="flex items-center space-x-2">
                    <button type="submit" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition">
                        Terapkan Filter
                    </button>
                    <a href="{{ route('admin.reports.transactions') }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold">
                        Reset
                    </a>
                </div>
            </div>
        </form>

        <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
            <div>
                Periode Aktif: <strong class="text-slate-800">{{ $dateFilter['label'] }}</strong>
            </div>
            <div>
                Ditemukan: <strong class="text-blue-600">{{ $bookings->total() }} Transaksi</strong>
            </div>
        </div>
    </div>

    <!-- Ringkasan Total (Summary Financial Cards) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Pendapatan Sewa Mobil -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm relative overflow-hidden">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Pendapatan Sewa Mobil</span>
            <div class="text-xl sm:text-2xl font-black text-slate-900">
                Rp {{ number_format($totalRentalFee, 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-slate-500 mt-1 flex items-center space-x-1">
                <i class="fa-solid fa-car text-blue-500"></i>
                <span>Tarif sewa unit murni</span>
            </div>
        </div>

        <!-- Pendapatan Driver -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm relative overflow-hidden">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Pendapatan Jasa Driver</span>
            <div class="text-xl sm:text-2xl font-black text-blue-600">
                Rp {{ number_format($totalDriverFee, 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-slate-500 mt-1 flex items-center space-x-1">
                <i class="fa-solid fa-user-tie text-blue-500"></i>
                <span>Layanan sopir profesional</span>
            </div>
        </div>

        <!-- Denda Keterlambatan -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm relative overflow-hidden">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Denda Keterlambatan</span>
            <div class="text-xl sm:text-2xl font-black text-amber-600">
                Rp {{ number_format($totalLatePenalty, 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-slate-500 mt-1 flex items-center space-x-1">
                <i class="fa-solid fa-clock-rotate-left text-amber-500"></i>
                <span>Penalti waktu & fisik</span>
            </div>
        </div>

        <!-- Total Keseluruhan -->
        <div class="bg-gradient-to-br from-blue-600 to-indigo-700 text-white rounded-2xl p-5 shadow-md relative overflow-hidden">
            <span class="text-xs font-bold text-blue-100 uppercase tracking-wider block mb-1">Total Keseluruhan</span>
            <div class="text-xl sm:text-2xl font-black text-white">
                Rp {{ number_format($totalOverall, 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-blue-100 mt-1 flex items-center space-x-1">
                <i class="fa-solid fa-circle-check"></i>
                <span>{{ $countCompleted }} Selesai • {{ $countActive }} Berjalan</span>
            </div>
        </div>
    </div>

    <!-- Tabel Detail Transaksi -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-black text-slate-900">Tabel Rekapitulasi Detail Transaksi</h2>
                <p class="text-xs text-slate-500 mt-0.5">Rincian invoice per transaksi penyewaan armada.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-100 text-slate-500 uppercase tracking-wider font-bold">
                        <th class="py-3 px-4">No & Kode</th>
                        <th class="py-3 px-4">Customer</th>
                        <th class="py-3 px-4">Armada Mobil</th>
                        <th class="py-3 px-4">Jadwal Sewa</th>
                        <th class="py-3 px-4">Layanan</th>
                        <th class="py-3 px-4 text-right">Sewa Mobil</th>
                        <th class="py-3 px-4 text-right">Denda</th>
                        <th class="py-3 px-4 text-right">Total Biaya</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($bookings as $index => $booking)
                        <tr class="hover:bg-slate-50/60 transition">
                            <!-- No & Kode -->
                            <td class="py-3.5 px-4">
                                <div class="text-slate-400 font-mono text-[11px]">#{{ $bookings->firstItem() + $index }}</div>
                                <a href="{{ route('admin.bookings.show', $booking) }}" class="font-mono font-bold text-blue-600 hover:underline">
                                    {{ $booking->booking_code }}
                                </a>
                                <div class="text-[10px] text-slate-400">{{ $booking->created_at ? $booking->created_at->format('d/m/y H:i') : '-' }}</div>
                            </td>

                            <!-- Customer -->
                            <td class="py-3.5 px-4">
                                <div class="font-semibold text-slate-900">{{ $booking->user?->name ?: 'Customer Terhapus' }}</div>
                                <div class="text-[11px] text-slate-400">{{ $booking->user?->phone_number ?: '-' }}</div>
                                <div class="text-[10px] text-slate-400">NIK: {{ $booking->user?->nik_ktp ?: '-' }}</div>
                            </td>

                            <!-- Mobil -->
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-800">{{ $booking->car?->brand }} {{ $booking->car?->model }}</div>
                                <div class="font-mono text-[11px] text-slate-500">{{ $booking->car?->license_plate }}</div>
                            </td>

                            <!-- Jadwal -->
                            <td class="py-3.5 px-4">
                                <div class="font-medium text-slate-800">
                                    {{ $booking->start_date ? $booking->start_date->format('d/m/Y') : '-' }}
                                    <span class="text-slate-400">s/d</span>
                                    {{ $booking->end_date ? $booking->end_date->format('d/m/Y') : '-' }}
                                </div>
                                <div class="text-[11px] text-slate-500 font-semibold">{{ $booking->duration_days }} Hari</div>
                            </td>

                            <!-- Layanan Driver / Lepas Kunci -->
                            <td class="py-3.5 px-4">
                                @if($booking->driver_service)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                        <i class="fa-solid fa-user-tie mr-1"></i> + Driver
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">
                                        Lepas Kunci
                                    </span>
                                @endif
                            </td>

                            <!-- Rincian Biaya -->
                            <td class="py-3.5 px-4 text-right font-medium text-slate-700">
                                Rp {{ number_format($booking->car_rental_fee, 0, ',', '.') }}
                            </td>

                            <!-- Denda -->
                            <td class="py-3.5 px-4 text-right">
                                @if($booking->late_penalty_fee > 0)
                                    <span class="font-bold text-amber-600">Rp {{ number_format($booking->late_penalty_fee, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>

                            <!-- Total Biaya -->
                            <td class="py-3.5 px-4 text-right font-black text-slate-900">
                                Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                            </td>

                            <!-- Status -->
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold border {{ $booking->status_badge_class }}">
                                    {{ $booking->status_label }}
                                </span>
                            </td>

                            <!-- Aksi -->
                            <td class="py-3.5 px-4 text-center">
                                <a href="{{ route('admin.bookings.show', $booking) }}"
                                   class="p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg inline-block transition" title="Lihat Detail Transaksi">
                                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-12 text-center text-slate-400">
                                <i class="fa-solid fa-receipt text-3xl mb-2 text-slate-300 block"></i>
                                Tidak ada transaksi yang ditemukan untuk filter periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $bookings->links() }}
        </div>
    </div>
</div>
@endsection
