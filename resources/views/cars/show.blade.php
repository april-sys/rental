@extends('layouts.app')

@section('title', $car->brand . ' ' . $car->model . ' - Detail Mobil')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- Breadcrumb -->
    <nav class="flex items-center space-x-2 text-xs text-slate-500 mb-6">
        <a href="{{ route('home') }}" class="hover:text-blue-600">Beranda</a>
        <span>/</span>
        <a href="{{ route('home') }}#katalog" class="hover:text-blue-600">Armada</a>
        <span>/</span>
        <span class="text-slate-800 font-semibold">{{ $car->brand }} {{ $car->model }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Left: Image & Specs -->
        <div class="lg:col-span-7 space-y-6">
            <div class="relative bg-white rounded-2xl overflow-hidden border border-slate-200 shadow-md">
                <img src="{{ $car->image_url }}"
                     alt="{{ $car->brand }} {{ $car->model }}"
                     class="w-full h-80 sm:h-96 object-cover">

                <div class="absolute top-4 right-4">
                    @if($car->status === 'available')
                        <span class="px-3 py-1.5 text-xs font-bold rounded-full bg-emerald-500 text-white shadow-md">
                            Tersedia Siap Jalan
                        </span>
                    @elseif($car->status === 'rented')
                        <span class="px-3 py-1.5 text-xs font-bold rounded-full bg-amber-500 text-white shadow-md">
                            Sedang Digunakan
                        </span>
                    @else
                        <span class="px-3 py-1.5 text-xs font-bold rounded-full bg-slate-500 text-white shadow-md">
                            Dalam Maintenance
                        </span>
                    @endif
                </div>
            </div>

            <!-- Description Box -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                <h3 class="text-base font-bold text-slate-900 mb-3 flex items-center space-x-2">
                    <i class="fa-solid fa-circle-info text-blue-600"></i>
                    <span>Deskripsi Kendaraan</span>
                </h3>
                <p class="text-sm text-slate-600 leading-relaxed whitespace-pre-line">
                    {{ $car->description ?: 'Kendaraan dalam kondisi prima, servis rutin berkala, AC dingin, interior wangi dan bersih, siap untuk perjalanan dinas maupun liburan keluarga Anda.' }}
                </p>

                <!-- Rental Inclusions -->
                <div class="mt-6 pt-6 border-t border-slate-100">
                    <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-3">Fasilitas & Keunggulan</h4>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs text-slate-600">
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-check text-emerald-500"></i>
                            <span>Unit Bersih & Higienis</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-check text-emerald-500"></i>
                            <span>Layanan Antar ke Alamat</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-check text-emerald-500"></i>
                            <span>Asuransi Kendaraan</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-check text-emerald-500"></i>
                            <span>Opsi Dengan Driver</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-check text-emerald-500"></i>
                            <span>Bantuan Darurat 24 Jam</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-check text-emerald-500"></i>
                            <span>Transmisi Nyaman</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Booking Action & Details -->
        <div class="lg:col-span-5 space-y-6">
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-md">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div>
                        <span class="text-xs text-slate-400 font-semibold uppercase">{{ $car->brand }}</span>
                        <h1 class="text-xl font-black text-slate-900">{{ $car->brand }} {{ $car->model }}</h1>
                    </div>
                    <span class="font-mono text-xs bg-slate-100 text-slate-700 px-2.5 py-1 rounded-lg font-bold border border-slate-200">
                        {{ $car->license_plate }}
                    </span>
                </div>

                <!-- Price Highlight -->
                <div class="py-5 border-b border-slate-100">
                    <div class="text-xs text-slate-400">Tarif Sewa Harian:</div>
                    <div class="text-3xl font-black text-blue-600 mt-1">
                        Rp {{ number_format($car->price_per_day, 0, ',', '.') }}
                        <span class="text-xs text-slate-400 font-normal">/ hari</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1 flex items-center space-x-1">
                        <i class="fa-solid fa-truck-ramp-box text-blue-500"></i>
                        <span>Sudah termasuk pengantaran ke alamat customer</span>
                    </p>
                </div>

                <!-- Specs Grid -->
                <div class="grid grid-cols-2 gap-3 py-4 border-b border-slate-100 text-xs">
                    <div class="bg-slate-50 p-3 rounded-xl">
                        <span class="text-slate-400 block mb-0.5">Kapasitas Kursi</span>
                        <span class="font-bold text-slate-800 flex items-center space-x-1">
                            <i class="fa-solid fa-user-group text-blue-500"></i>
                            <span>{{ $car->capacity }} Penumpang</span>
                        </span>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-xl">
                        <span class="text-slate-400 block mb-0.5">Layanan Driver</span>
                        <span class="font-bold text-slate-800 flex items-center space-x-1">
                            <i class="fa-solid fa-user-tie text-blue-500"></i>
                            <span>Opsional (+Rp 150rb/hr)</span>
                        </span>
                    </div>
                </div>

                <!-- Busy Schedules Check -->
                @if($activeBookings->isNotEmpty())
                    <div class="my-4 p-3.5 bg-amber-50 border border-amber-200 rounded-xl text-xs">
                        <div class="font-bold text-amber-900 flex items-center space-x-1.5 mb-2">
                            <i class="fa-solid fa-calendar-xmark text-amber-700"></i>
                            <span>Jadwal Sudah Terbooking (Tidak Tersedia):</span>
                        </div>
                        <ul class="space-y-1 text-amber-800">
                            @foreach($activeBookings as $booked)
                                <li class="flex items-center space-x-1">
                                    <i class="fa-solid fa-ban text-[10px] text-amber-600"></i>
                                    <span>{{ \Carbon\Carbon::parse($booked->start_date)->translatedFormat('d M Y') }} s/d {{ \Carbon\Carbon::parse($booked->end_date)->translatedFormat('d M Y') }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- CTA Button -->
                <div class="pt-4">
                    @if($car->status !== 'maintenance')
                        <a href="{{ route('bookings.create', $car) }}"
                           class="w-full py-3.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-600/30 transition text-sm flex items-center justify-center space-x-2">
                            <i class="fa-solid fa-calendar-check"></i>
                            <span>Sewa Mobil Ini Sekarang</span>
                        </a>
                    @else
                        <button disabled class="w-full py-3.5 px-4 bg-slate-200 text-slate-400 font-bold rounded-xl text-sm cursor-not-allowed">
                            Mobil Sedang Dalam Perbaikan
                        </button>
                    @endif
                    <p class="text-center text-[11px] text-slate-400 mt-2">
                        Pemesanan membutuhkan login akun customer untuk verifikasi identitas
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
