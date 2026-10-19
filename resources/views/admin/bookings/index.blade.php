@extends('layouts.app')

@section('title', 'Kelola Transaksi Booking - Admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('admin.dashboard') }}" class="text-xs text-slate-400 hover:text-blue-600">Admin</a>
                <span class="text-xs text-slate-300">/</span>
                <span class="text-xs font-semibold text-slate-600">Transaksi Booking</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight mt-1">Daftar Transaksi & Alur Sewa</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Kelola verifikasi pemesanan, pengiriman unit mobil ke customer, dan perhitungan denda.</p>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="flex items-center space-x-2 overflow-x-auto pb-3 mb-6 text-xs">
        <a href="{{ route('admin.bookings.index') }}"
           class="px-3.5 py-2 rounded-xl font-bold transition whitespace-nowrap {{ !request('status') ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            Semua ({{ $statusCounts['all'] }})
        </a>
        <a href="{{ route('admin.bookings.index', ['status' => 'pending']) }}"
           class="px-3.5 py-2 rounded-xl font-bold transition whitespace-nowrap {{ request('status') == 'pending' ? 'bg-amber-500 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            Pending ({{ $statusCounts['pending'] }})
        </a>
        <a href="{{ route('admin.bookings.index', ['status' => 'approved']) }}"
           class="px-3.5 py-2 rounded-xl font-bold transition whitespace-nowrap {{ request('status') == 'approved' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            Siap Dikirim ({{ $statusCounts['approved'] }})
        </a>
        <a href="{{ route('admin.bookings.index', ['status' => 'delivering']) }}"
           class="px-3.5 py-2 rounded-xl font-bold transition whitespace-nowrap {{ request('status') == 'delivering' ? 'bg-purple-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            On Delivery ({{ $statusCounts['delivering'] }})
        </a>
        <a href="{{ route('admin.bookings.index', ['status' => 'in_use']) }}"
           class="px-3.5 py-2 rounded-xl font-bold transition whitespace-nowrap {{ request('status') == 'in_use' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            In Use ({{ $statusCounts['in_use'] }})
        </a>
        <a href="{{ route('admin.bookings.index', ['status' => 'returned']) }}"
           class="px-3.5 py-2 rounded-xl font-bold transition whitespace-nowrap {{ request('status') == 'returned' ? 'bg-yellow-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            Dikembalikan ({{ $statusCounts['returned'] }})
        </a>
        <a href="{{ route('admin.bookings.index', ['status' => 'completed']) }}"
           class="px-3.5 py-2 rounded-xl font-bold transition whitespace-nowrap {{ request('status') == 'completed' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            Selesai ({{ $statusCounts['completed'] }})
        </a>
        <a href="{{ route('admin.bookings.index', ['status' => 'cancelled']) }}"
           class="px-3.5 py-2 rounded-xl font-bold transition whitespace-nowrap {{ request('status') == 'cancelled' ? 'bg-slate-700 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            Dibatalkan ({{ $statusCounts['cancelled'] }})
        </a>
    </div>

    <!-- Search Form -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm mb-6">
        <form action="{{ route('admin.bookings.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <div class="flex-1 relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari kode booking, nama customer, email, nomor HP, atau mobil..."
                       class="w-full pl-8 pr-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition">
                Cari Transaksi
            </button>

            @if(request('search'))
                <a href="{{ route('admin.bookings.index', request('status') ? ['status' => request('status')] : []) }}"
                   class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold flex items-center justify-center">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Bookings Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-100 text-slate-500 uppercase tracking-wider font-bold">
                        <th class="py-3 px-4">Kode & Customer</th>
                        <th class="py-3 px-4">Mobil & Driver</th>
                        <th class="py-3 px-4">Jadwal Sewa</th>
                        <th class="py-3 px-4">Alamat Pengantaran</th>
                        <th class="py-3 px-4">Total Biaya</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Aksi Workflow</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($bookings as $booking)
                        <tr class="hover:bg-slate-50/60 transition">
                            <!-- Kode & Customer -->
                            <td class="py-3.5 px-4">
                                <a href="{{ route('admin.bookings.show', $booking) }}" class="font-mono font-bold text-blue-600 hover:underline">
                                    {{ $booking->booking_code }}
                                </a>
                                <div class="font-semibold text-slate-900 mt-0.5">{{ $booking->user->name }}</div>
                                <div class="text-[11px] text-slate-400">NIK: {{ $booking->user->nik_ktp ?: '-' }}</div>
                                <div class="text-[11px] text-slate-400">WA: {{ $booking->user->phone_number ?: '-' }}</div>
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

                            <!-- Alamat Pengantaran -->
                            <td class="py-3.5 px-4 max-w-xs">
                                <div class="text-slate-600 truncate" title="{{ $booking->delivery_address }}">
                                    <i class="fa-solid fa-location-dot text-blue-500 mr-1"></i>
                                    {{ $booking->delivery_address }}
                                </div>
                            </td>

                            <!-- Total Biaya -->
                            <td class="py-3.5 px-4">
                                <div class="font-black text-slate-900">
                                    Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                                </div>
                                @if($booking->late_penalty_fee > 0)
                                    <div class="text-[10px] text-red-500 font-semibold">+ Denda Rp {{ number_format($booking->late_penalty_fee, 0, ',', '.') }}</div>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded-full font-bold border {{ $booking->status_badge_class }}">
                                    {{ $booking->status_label }}
                                </span>
                            </td>

                            <!-- Workflow Buttons -->
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
                                    @elseif($booking->status === 'in_use')
                                        <form action="{{ route('admin.bookings.return', $booking) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1.5 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg font-bold shadow-sm" title="Mobil Dikembalikan">
                                                Kembali
                                            </button>
                                        </form>
                                    @elseif($booking->status === 'returned')
                                        <a href="{{ route('admin.bookings.show', $booking) }}" class="px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold shadow-sm" title="Selesaikan & Cek Denda">
                                            Selesai & Denda
                                        </a>
                                    @endif

                                    <a href="{{ route('admin.bookings.show', $booking) }}"
                                       class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg" title="Detail Lengkap">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">
                                Tidak ada data transaksi yang ditemukan.
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
