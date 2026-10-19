@extends('layouts.app')

@section('title', 'Laporan Penggunaan Kendaraan - Admin')

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
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight mt-1">Laporan Penggunaan Kendaraan & Armada</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Statistik frekuensi sewa, unit terlaris (Most Rented Car), total pendapatan per armada, dan riwayat kondisi unit.
            </p>
        </div>

        <!-- Export Buttons -->
        <div class="flex items-center space-x-2.5">
            <a href="{{ route('admin.reports.vehicles.pdf', request()->query()) }}" target="_blank"
               class="px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition shadow-sm flex items-center space-x-2">
                <i class="fa-solid fa-file-pdf"></i>
                <span>Cetak PDF / Print Laporan</span>
            </a>
            <a href="{{ route('admin.reports.vehicles.excel', request()->query()) }}"
               class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-sm flex items-center space-x-2">
                <i class="fa-solid fa-file-excel"></i>
                <span>Export Excel</span>
            </a>
        </div>
    </div>

    <!-- Tab Menu Laporan -->
    <div class="flex items-center space-x-3 border-b border-slate-200 mb-6">
        <a href="{{ route('admin.reports.transactions') }}"
           class="pb-3 border-b-2 font-bold text-xs sm:text-sm flex items-center space-x-2 transition border-transparent text-slate-500 hover:text-slate-800">
            <i class="fa-solid fa-money-bill-transfer"></i>
            <span>Laporan Transaksi Keuangan</span>
        </a>
        <a href="{{ route('admin.reports.vehicles') }}"
           class="pb-3 border-b-2 font-bold text-xs sm:text-sm flex items-center space-x-2 transition border-blue-600 text-blue-600">
            <i class="fa-solid fa-car-side"></i>
            <span>Laporan Penggunaan Kendaraan</span>
        </a>
    </div>

    <!-- Filter Rentang Waktu Form -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm mb-6">
        <form action="{{ route('admin.reports.vehicles') }}" method="GET" class="space-y-4">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
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

                <div class="text-xs text-slate-500">
                    Periode: <strong class="text-slate-800">{{ $dateFilter['label'] }}</strong>
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
                    <a href="{{ route('admin.reports.vehicles') }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Highlights Grid (Most Rented Car & Top Fleet Stats) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Most Rented Car Banner -->
        <div class="md:col-span-1 bg-gradient-to-br from-amber-500 to-amber-600 text-white rounded-2xl p-6 shadow-md relative overflow-hidden flex flex-col justify-between">
            <div>
                <div class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full bg-white/20 text-white text-xs font-black uppercase tracking-wider mb-3">
                    <i class="fa-solid fa-crown text-yellow-200"></i>
                    <span>Most Rented Car</span>
                </div>

                @if($mostRentedCar)
                    <h3 class="text-xl font-black text-white">{{ $mostRentedCar->full_name }}</h3>
                    <div class="text-xs font-mono text-amber-100 mt-0.5">{{ $mostRentedCar->license_plate }}</div>

                    <div class="mt-4 flex items-center space-x-4">
                        <div class="w-20 h-16 rounded-xl overflow-hidden bg-black/20 border border-white/20">
                            <img src="{{ $mostRentedCar->image_url }}" alt="{{ $mostRentedCar->full_name }}" class="w-full h-full object-cover">
                        </div>
                        <div>
                            <div class="text-2xl font-black text-white">{{ $mostRentedCar->rental_frequency }}x</div>
                            <div class="text-[11px] text-amber-100 font-medium">Frekuensi Penyewaan</div>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-amber-100 mt-2">Belum ada aktivitas penyewaan armada pada rentang waktu ini.</p>
                @endif
            </div>

            <div class="mt-4 pt-3 border-t border-white/20 text-[11px] text-amber-100 flex items-center justify-between">
                <span>Total Hari: <strong>{{ $mostRentedCar ? $mostRentedCar->total_days : 0 }} Hari</strong></span>
                <span>Pendapatan: <strong>Rp {{ number_format($mostRentedCar ? $mostRentedCar->total_revenue : 0, 0, ',', '.') }}</strong></span>
            </div>
        </div>

        <!-- Top Revenue Car -->
        <div class="md:col-span-1 bg-gradient-to-br from-blue-600 to-indigo-700 text-white rounded-2xl p-6 shadow-md relative overflow-hidden flex flex-col justify-between">
            <div>
                <div class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full bg-white/20 text-white text-xs font-black uppercase tracking-wider mb-3">
                    <i class="fa-solid fa-sack-dollar text-emerald-300"></i>
                    <span>Top Revenue Vehicle</span>
                </div>

                @if($topRevenueCar)
                    <h3 class="text-xl font-black text-white">{{ $topRevenueCar->full_name }}</h3>
                    <div class="text-xs font-mono text-blue-200 mt-0.5">{{ $topRevenueCar->license_plate }}</div>

                    <div class="mt-4 flex items-center space-x-4">
                        <div class="w-20 h-16 rounded-xl overflow-hidden bg-black/20 border border-white/20">
                            <img src="{{ $topRevenueCar->image_url }}" alt="{{ $topRevenueCar->full_name }}" class="w-full h-full object-cover">
                        </div>
                        <div>
                            <div class="text-2xl font-black text-white">Rp {{ number_format($topRevenueCar->total_revenue, 0, ',', '.') }}</div>
                            <div class="text-[11px] text-blue-200 font-medium">Kontribusi Pendapatan</div>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-blue-200 mt-2">Belum ada pendapatan unit tercatat pada rentang waktu ini.</p>
                @endif
            </div>

            <div class="mt-4 pt-3 border-t border-white/20 text-[11px] text-blue-200 flex items-center justify-between">
                <span>Frekuensi: <strong>{{ $topRevenueCar ? $topRevenueCar->rental_frequency : 0 }} Kali Sewa</strong></span>
                <span>Tarif: <strong>Rp {{ number_format($topRevenueCar ? $topRevenueCar->price_per_day : 0, 0, ',', '.') }}/hari</strong></span>
            </div>
        </div>

        <!-- Total Fleet Performance Stats -->
        <div class="md:col-span-1 bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex flex-col justify-between">
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-3">Ringkasan Utilitas Armada</span>

                <div class="space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center space-x-2 text-xs text-slate-600">
                            <i class="fa-solid fa-car text-blue-500"></i>
                            <span>Total Unit Terdaftar</span>
                        </div>
                        <span class="font-black text-slate-900 text-base">{{ $vehicleStats->count() }} Mobil</span>
                    </div>

                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center space-x-2 text-xs text-slate-600">
                            <i class="fa-solid fa-route text-indigo-500"></i>
                            <span>Total Frekuensi Sewa</span>
                        </div>
                        <span class="font-black text-indigo-600 text-base">{{ $totalFleetTrips }} Transaksi</span>
                    </div>

                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center space-x-2 text-xs text-slate-600">
                            <i class="fa-solid fa-calendar-check text-purple-500"></i>
                            <span>Total Hari Operasional</span>
                        </div>
                        <span class="font-black text-purple-600 text-base">{{ $totalDaysRented }} Hari</span>
                    </div>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500">Total Pendapatan Armada:</span>
                <span class="text-base font-black text-emerald-600">Rp {{ number_format($totalFleetRevenue, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <!-- Tabel Detail Statistik Penggunaan Kendaraan -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-black text-slate-900">Tabel Kinerja & Riwayat Kondisi Fisik Armada</h2>
                <p class="text-xs text-slate-500 mt-0.5">Statistik penggunaan per unit kendaraan mobil rental.</p>
            </div>
            <div class="text-xs font-semibold text-slate-600">
                Total: {{ $vehicleStats->count() }} Unit Kendaraan
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-100 text-slate-500 uppercase tracking-wider font-bold">
                        <th class="py-3 px-4">Armada Kendaraan</th>
                        <th class="py-3 px-4">Tarif & Kapasitas</th>
                        <th class="py-3 px-4 text-center">Status Armada</th>
                        <th class="py-3 px-4 text-center">Frekuensi Sewa</th>
                        <th class="py-3 px-4 text-center">Hari Terpakai</th>
                        <th class="py-3 px-4 text-right">Total Pendapatan</th>
                        <th class="py-3 px-4">Catatan Kondisi Terakhir Saat Dikembalikan</th>
                        <th class="py-3 px-4 text-center">Kelola</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($vehicleStats as $stat)
                        <tr class="hover:bg-slate-50/60 transition">
                            <!-- Armada Kendaraan -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center space-x-3">
                                    <img src="{{ $stat->image_url }}" alt="{{ $stat->full_name }}"
                                         class="w-16 h-12 rounded-xl object-cover border border-slate-200 flex-shrink-0">
                                    <div>
                                        <div class="font-bold text-slate-900 text-sm flex items-center space-x-1.5">
                                            <span>{{ $stat->brand }} {{ $stat->model }}</span>
                                            @if($mostRentedCar && $mostRentedCar->id === $stat->id)
                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-black bg-amber-100 text-amber-800" title="Mobil Terlaris">
                                                    ★ Terlaris
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-slate-500 font-mono text-[11px] mt-0.5">{{ $stat->license_plate }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Tarif & Kapasitas -->
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-800">Rp {{ number_format($stat->price_per_day, 0, ',', '.') }}/hari</div>
                                <div class="text-[11px] text-slate-500 mt-0.5">
                                    <i class="fa-solid fa-user-group text-slate-400 mr-1"></i>{{ $stat->capacity }} Penumpang
                                </div>
                            </td>

                            <!-- Status Armada -->
                            <td class="py-3.5 px-4 text-center">
                                @if($stat->status === 'available')
                                    <span class="px-2.5 py-1 rounded-full font-bold text-[11px] bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Tersedia
                                    </span>
                                @elseif($stat->status === 'rented')
                                    <span class="px-2.5 py-1 rounded-full font-bold text-[11px] bg-purple-50 text-purple-700 border border-purple-200">
                                        Sedang Disewa
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full font-bold text-[11px] bg-amber-50 text-amber-700 border border-amber-200">
                                        Maintenance
                                    </span>
                                @endif
                            </td>

                            <!-- Frekuensi Sewa -->
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2.5 py-1 rounded-xl text-xs font-black {{ $stat->rental_frequency > 0 ? 'bg-blue-50 text-blue-700' : 'text-slate-400' }}">
                                    {{ $stat->rental_frequency }}x
                                </span>
                            </td>

                            <!-- Hari Terpakai -->
                            <td class="py-3.5 px-4 text-center font-bold text-slate-700">
                                {{ $stat->total_days }} Hari
                            </td>

                            <!-- Total Pendapatan Unit -->
                            <td class="py-3.5 px-4 text-right font-black text-slate-900">
                                Rp {{ number_format($stat->total_revenue, 0, ',', '.') }}
                            </td>

                            <!-- Catatan Kondisi Terakhir -->
                            <td class="py-3.5 px-4 max-w-xs">
                                <div class="text-[11px] text-slate-600 leading-snug line-clamp-2" title="{{ $stat->last_condition }}">
                                    <i class="fa-solid fa-clipboard-check text-slate-400 mr-1"></i>
                                    {{ $stat->last_condition }}
                                </div>
                                @if($stat->last_returned_date !== '-')
                                    <div class="text-[10px] text-slate-400 mt-0.5">Cek terakhir: {{ $stat->last_returned_date }}</div>
                                @endif
                            </td>

                            <!-- Kelola -->
                            <td class="py-3.5 px-4 text-center">
                                <a href="{{ route('admin.cars.edit', $stat->id) }}"
                                   class="p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg inline-block transition" title="Edit Mobil">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400">
                                Belum ada unit armada mobil terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
