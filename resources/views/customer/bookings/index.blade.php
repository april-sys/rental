@extends('layouts.app')

@section('title', 'Riwayat Pesanan & Tracking Saya')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Pesanan & Tracking Saya</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Pantau status konfirmasi, pengiriman armada, dan riwayat rental mobil Anda.</p>
        </div>
        <a href="{{ route('home') }}#katalog" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-sm transition">
            <i class="fa-solid fa-plus"></i>
            <span>Sewa Mobil Baru</span>
        </a>
    </div>

    <!-- Status Filters -->
    <div class="flex items-center space-x-2 overflow-x-auto pb-3 mb-6 text-xs">
        <a href="{{ route('customer.bookings') }}"
           class="px-3.5 py-2 rounded-xl font-bold transition whitespace-nowrap {{ !request('status') ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            Semua Pesanan
        </a>
        <a href="{{ route('customer.bookings', ['status' => 'pending']) }}"
           class="px-3.5 py-2 rounded-xl font-bold transition whitespace-nowrap {{ request('status') == 'pending' ? 'bg-amber-500 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            Pending
        </a>
        <a href="{{ route('customer.bookings', ['status' => 'approved']) }}"
           class="px-3.5 py-2 rounded-xl font-bold transition whitespace-nowrap {{ request('status') == 'approved' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            Siap Dikirim
        </a>
        <a href="{{ route('customer.bookings', ['status' => 'delivering']) }}"
           class="px-3.5 py-2 rounded-xl font-bold transition whitespace-nowrap {{ request('status') == 'delivering' ? 'bg-purple-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            Sedang Dikirim
        </a>
        <a href="{{ route('customer.bookings', ['status' => 'in_use']) }}"
           class="px-3.5 py-2 rounded-xl font-bold transition whitespace-nowrap {{ request('status') == 'in_use' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            Sedang Digunakan
        </a>
        <a href="{{ route('customer.bookings', ['status' => 'completed']) }}"
           class="px-3.5 py-2 rounded-xl font-bold transition whitespace-nowrap {{ request('status') == 'completed' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            Selesai
        </a>
    </div>

    <!-- Booking List Cards -->
    <div class="space-y-4">
        @forelse($bookings as $booking)
            <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200 shadow-sm hover:shadow-md transition">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                    <div class="flex items-center space-x-3">
                        <span class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-receipt"></i>
                        </span>
                        <div>
                            <div class="text-xs text-slate-400">Kode Booking:</div>
                            <span class="font-mono text-base font-black text-slate-900">{{ $booking->booking_code }}</span>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $booking->status_badge_class }}">
                            {{ $booking->status_label }}
                        </span>

                        <span class="text-xs text-slate-400">
                            Dipesan {{ $booking->created_at->format('d M Y, H:i') }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-6 py-4 items-center">
                    <!-- Car Info -->
                    <div class="md:col-span-4 flex items-center space-x-4">
                        <img src="{{ $booking->car->image_url }}"
                             alt="{{ $booking->car->brand }}"
                             class="w-24 h-16 rounded-xl object-cover border border-slate-200">
                        <div>
                            <h3 class="font-bold text-slate-900 text-sm">{{ $booking->car->brand }} {{ $booking->car->model }}</h3>
                            <div class="text-xs text-slate-500 font-mono">{{ $booking->car->license_plate }}</div>
                            <div class="text-xs text-slate-500 mt-1">
                                {{ $booking->driver_service ? 'Dengan Driver (+Rp 150rb/hr)' : 'Tanpa Driver (Lepas Kunci)' }}
                            </div>
                        </div>
                    </div>

                    <!-- Rental Dates -->
                    <div class="md:col-span-4 text-xs space-y-1">
                        <div class="text-slate-400">Periode Sewa ({{ $booking->duration_days }} Hari):</div>
                        <div class="font-bold text-slate-800 text-sm">
                            {{ $booking->start_date->format('d M Y') }} s/d {{ $booking->end_date->format('d M Y') }}
                        </div>
                        <div class="text-slate-500 text-[11px] truncate">
                            <i class="fa-solid fa-location-dot text-blue-500 mr-1"></i> Antar: {{ $booking->delivery_address }}
                        </div>
                    </div>

                    <!-- Total Price & Actions -->
                    <div class="md:col-span-4 flex flex-col sm:flex-row md:flex-col lg:flex-row items-start md:items-end lg:items-center justify-between gap-3 border-t md:border-t-0 pt-3 md:pt-0">
                        <div>
                            <div class="text-xs text-slate-400">Total Biaya:</div>
                            <div class="text-lg font-black text-blue-600">
                                Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                            </div>
                        </div>

                        <div class="flex items-center space-x-2">
                            <a href="{{ route('customer.bookings.show', $booking) }}"
                               class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-sm flex items-center space-x-1.5">
                                <i class="fa-solid fa-eye"></i>
                                <span>Detail & Tracking</span>
                            </a>

                            @if($booking->canCancel())
                                <form action="{{ route('customer.bookings.cancel', $booking) }}" method="POST"
                                      onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')">
                                    @csrf
                                    <button type="submit" class="px-3 py-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl text-xs font-semibold transition border border-red-200">
                                        Batalkan
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-16 bg-white rounded-2xl border border-slate-200 p-8">
                <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                    <i class="fa-solid fa-calendar-xmark text-2xl"></i>
                </div>
                <h3 class="text-base font-bold text-slate-700">Belum ada riwayat pesanan</h3>
                <p class="text-xs text-slate-400 mt-1">Anda belum melakukan penyewaan mobil. Silakan jelajahi armada kami sekarang.</p>
                <a href="{{ route('home') }}#katalog" class="inline-block mt-4 px-5 py-2.5 bg-blue-600 text-white rounded-xl text-xs font-bold shadow-sm">
                    Pilih Mobil Sekarang
                </a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $bookings->links() }}
    </div>
</div>
@endsection
