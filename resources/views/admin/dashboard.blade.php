@extends('layouts.app')

@section('title', 'Admin Dashboard - Rental Mobil')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-700">Panel Administrator</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight mt-1">Dashboard Pengelolaan Rental</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Pantau performa armada, konfirmasi booking masuk, dan kelola proses pengantaran.</p>
        </div>

        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.cars.create') }}" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-sm flex items-center space-x-2">
                <i class="fa-solid fa-plus"></i>
                <span>Tambah Mobil Baru</span>
            </a>
            <a href="{{ route('admin.bookings.index') }}" class="px-4 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-bold transition shadow-sm flex items-center space-x-2">
                <i class="fa-solid fa-list-check"></i>
                <span>Kelola Transaksi</span>
            </a>
        </div>
    </div>

    <!-- Metrics Cards Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Card 1: Pending Requests -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm relative overflow-hidden group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Perlu Konfirmasi</span>
                <span class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm font-bold shadow-inner">
                    <i class="fa-solid fa-bell"></i>
                </span>
            </div>
            <div class="text-3xl font-black text-slate-900">{{ $pendingBookings }}</div>
            <div class="text-xs text-amber-600 font-semibold mt-1 flex items-center space-x-1">
                <span>Booking Menunggu Approval</span>
            </div>
        </div>

        <!-- Card 2: Active Rentals -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Sewa Berjalan</span>
                <span class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-sm font-bold shadow-inner">
                    <i class="fa-solid fa-car-on"></i>
                </span>
            </div>
            <div class="text-3xl font-black text-slate-900">{{ $activeBookings }}</div>
            <div class="text-xs text-purple-600 font-semibold mt-1">
                <span>Siap Kirim / Diantar / In Use</span>
            </div>
        </div>

        <!-- Card 3: Fleet Status -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Ketersediaan Unit</span>
                <span class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold shadow-inner">
                    <i class="fa-solid fa-gauge"></i>
                </span>
            </div>
            <div class="text-3xl font-black text-emerald-600">{{ $availableCars }} <span class="text-sm font-normal text-slate-400">/ {{ $totalCars }} Unit</span></div>
            <div class="text-xs text-slate-500 mt-1">
                {{ $rentedCars }} Sedang Disewa • {{ $maintenanceCars }} Maintenance
            </div>
        </div>

        <!-- Card 4: Revenue Completed -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Pendapatan</span>
                <span class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold shadow-inner">
                    <i class="fa-solid fa-wallet"></i>
                </span>
            </div>
            <div class="text-2xl font-black text-blue-600">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
            <div class="text-xs text-emerald-600 font-semibold mt-1">
                {{ $completedBookings }} Transaksi Selesai
            </div>
        </div>
    </div>

    <!-- Recent Booking Requests & Workflow Section -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-8">
        <div class="p-5 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-black text-slate-900">Transaksi Masuk Terbaru</h2>
                <p class="text-xs text-slate-500 mt-0.5">Daftar booking yang memerlukan tindakan verifikasi atau update status pengantaran.</p>
            </div>
            <a href="{{ route('admin.bookings.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700 flex items-center space-x-1">
                <span>Lihat Semua Transaksi</span>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-100 text-slate-500 uppercase tracking-wider font-bold">
                        <th class="py-3 px-4">Kode & Customer</th>
                        <th class="py-3 px-4">Mobil & Layanan</th>
                        <th class="py-3 px-4">Jadwal Sewa</th>
                        <th class="py-3 px-4">Total Biaya</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Tindakan Workflow</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentBookings as $booking)
                        <tr class="hover:bg-slate-50/60 transition">
                            <!-- Kode & Customer -->
                            <td class="py-3.5 px-4">
                                <a href="{{ route('admin.bookings.show', $booking) }}" class="font-mono font-bold text-blue-600 hover:underline">
                                    {{ $booking->booking_code }}
                                </a>
                                <div class="font-semibold text-slate-900 text-xs mt-0.5">{{ $booking->user->name }}</div>
                                <div class="text-[11px] text-slate-400">{{ $booking->user->phone_number ?: $booking->user->email }}</div>
                            </td>

                            <!-- Mobil & Driver -->
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-800">{{ $booking->car->brand }} {{ $booking->car->model }}</div>
                                <div class="font-mono text-[11px] text-slate-400">{{ $booking->car->license_plate }}</div>
                                <span class="inline-block mt-0.5 text-[10px] px-1.5 py-0.5 rounded {{ $booking->driver_service ? 'bg-blue-50 text-blue-700 font-semibold' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $booking->driver_service ? '+ Driver' : 'Lepas Kunci' }}
                                </span>
                            </td>

                            <!-- Jadwal -->
                            <td class="py-3.5 px-4">
                                <div class="font-semibold text-slate-800">
                                    {{ $booking->start_date->format('d M Y') }} s/d {{ $booking->end_date->format('d M Y') }}
                                </div>
                                <div class="text-[11px] text-slate-400">{{ $booking->duration_days }} Hari</div>
                            </td>

                            <!-- Total Biaya -->
                            <td class="py-3.5 px-4 font-bold text-slate-900">
                                Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                            </td>

                            <!-- Status Badge -->
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded-full font-bold border {{ $booking->status_badge_class }}">
                                    {{ $booking->status_label }}
                                </span>
                            </td>

                            <!-- Actions -->
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end space-x-1.5">
                                    @if($booking->status === 'pending')
                                        <form action="{{ route('admin.bookings.approve', $booking) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold shadow-sm" title="Konfirmasi & Setujui Booking">
                                                Setujui
                                            </button>
                                        </form>
                                    @elseif($booking->status === 'approved')
                                        <a href="{{ route('admin.bookings.show', $booking) }}" class="px-2.5 py-1.5 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-bold shadow-sm" title="Kirim Unit Mobil & Input Driver">
                                            Kirim Mobil
                                        </a>
                                    @elseif($booking->status === 'delivering')
                                        <form action="{{ route('admin.bookings.in-use', $booking) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-bold shadow-sm" title="Mobil Diterima Customer">
                                                Diterima
                                            </button>
                                        </form>
                                    @elseif(in_array($booking->status, ['in_use', 'returned']))
                                        <a href="{{ route('admin.bookings.show', $booking) }}" class="px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold shadow-sm" title="Selesaikan & Cek Denda">
                                            Selesai & Denda
                                        </a>
                                    @endif

                                    <a href="{{ route('admin.bookings.show', $booking) }}" class="p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg">
                                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">
                                Belum ada data transaksi pemesanan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
