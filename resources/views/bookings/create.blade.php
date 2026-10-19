@extends('layouts.app')

@section('title', 'Form Pemesanan - ' . $car->brand . ' ' . $car->model)

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10"
     x-data="{
        startDate: '{{ old('start_date', now()->toDateString()) }}',
        endDate: '{{ old('end_date', now()->toDateString()) }}',
        withDriver: {{ old('driver_service') ? 'true' : 'false' }},
        pricePerDay: {{ $car->price_per_day }},
        driverDailyRate: {{ $driverDailyRate }},

        get durationDays() {
            if (!this.startDate || !this.endDate) return 1;
            const start = new Date(this.startDate);
            const end = new Date(this.endDate);
            if (end < start) return 1;
            const diffTime = Math.abs(end - start);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            return diffDays > 0 ? diffDays : 1;
        },

        get carRentalFee() {
            return this.durationDays * this.pricePerDay;
        },

        get driverFee() {
            return this.withDriver ? (this.durationDays * this.driverDailyRate) : 0;
        },

        get totalPrice() {
            return this.carRentalFee + this.driverFee;
        },

        formatRupiah(amount) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(amount);
        }
     }">

    <!-- Page Title & Steps -->
    <div class="mb-8">
        <a href="{{ route('cars.show', $car) }}" class="inline-flex items-center space-x-1.5 text-xs text-slate-500 hover:text-blue-600 mb-2">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Detail Mobil</span>
        </a>
        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Formulir Pemesanan Rental Mobil</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">Lengkapi rincian tanggal, opsi layanan driver, dan alamat pengantaran unit.</p>
    </div>

    <!-- Busy Schedule Warning if any -->
    @if($busySchedules->isNotEmpty())
        <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-2xl">
            <div class="flex items-start space-x-3">
                <i class="fa-solid fa-triangle-exclamation text-amber-600 text-lg mt-0.5"></i>
                <div class="text-xs text-amber-900">
                    <strong class="font-bold block mb-1">Perhatian: Mobil ini telah terbooking pada rentang tanggal berikut:</strong>
                    <div class="flex flex-wrap gap-2 mt-1">
                        @foreach($busySchedules as $schedule)
                            <span class="px-2 py-0.5 bg-amber-200/70 text-amber-900 rounded font-medium">
                                {{ \Carbon\Carbon::parse($schedule->start_date)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($schedule->end_date)->format('d M Y') }}
                            </span>
                        @endforeach
                    </div>
                    <span class="block text-amber-700 mt-1">Harap memilih rentang tanggal di luar jadwal terisi di atas agar booking Anda dapat disetujui.</span>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Left: Form Input -->
        <div class="lg:col-span-7 bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
            <form action="{{ route('bookings.store') }}" method="POST" id="bookingForm" class="space-y-6">
                @csrf
                <input type="hidden" name="car_id" value="{{ $car->id }}">

                <!-- 1. Rentang Tanggal Sewa -->
                <div>
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-3 flex items-center space-x-2">
                        <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs">1</span>
                        <span>Rentang Tanggal Penyewaan</span>
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="start_date" class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Mulai Sewa <span class="text-red-500">*</span></label>
                            <input type="date" name="start_date" id="start_date"
                                   min="{{ date('Y-m-d') }}"
                                   x-model="startDate"
                                   required
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
                        </div>

                        <div>
                            <label for="end_date" class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Selesai Sewa <span class="text-red-500">*</span></label>
                            <input type="date" name="end_date" id="end_date"
                                   :min="startDate"
                                   x-model="endDate"
                                   required
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
                        </div>
                    </div>
                </div>

                <!-- 2. Opsi Layanan Driver -->
                <div class="pt-2 border-t border-slate-100">
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-3 flex items-center space-x-2">
                        <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs">2</span>
                        <span>Opsi Layanan Driver</span>
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Lepas Kunci -->
                        <label class="cursor-pointer border-2 rounded-2xl p-4 transition flex flex-col justify-between"
                               :class="!withDriver ? 'border-blue-600 bg-blue-50/50' : 'border-slate-200 hover:border-slate-300'">
                            <div class="flex items-center justify-between mb-2">
                                <div class="font-bold text-sm text-slate-900 flex items-center space-x-2">
                                    <i class="fa-solid fa-key text-blue-600"></i>
                                    <span>Tanpa Driver (Lepas Kunci)</span>
                                </div>
                                <input type="radio" name="driver_service" value="0"
                                       @click="withDriver = false"
                                       :checked="!withDriver"
                                       class="text-blue-600 focus:ring-blue-500">
                            </div>
                            <p class="text-xs text-slate-500">Anda mengemudi sendiri secara mandiri. Bebas biaya tambahan.</p>
                            <span class="mt-3 text-xs font-bold text-emerald-600">GRATIS (Rp 0)</span>
                        </label>

                        <!-- Dengan Driver -->
                        <label class="cursor-pointer border-2 rounded-2xl p-4 transition flex flex-col justify-between"
                               :class="withDriver ? 'border-blue-600 bg-blue-50/50' : 'border-slate-200 hover:border-slate-300'">
                            <div class="flex items-center justify-between mb-2">
                                <div class="font-bold text-sm text-slate-900 flex items-center space-x-2">
                                    <i class="fa-solid fa-user-tie text-blue-600"></i>
                                    <span>Dengan Driver Profesional</span>
                                </div>
                                <input type="radio" name="driver_service" value="1"
                                       @click="withDriver = true"
                                       :checked="withDriver"
                                       class="text-blue-600 focus:ring-blue-500">
                            </div>
                            <p class="text-xs text-slate-500">Driver berpengalaman, ramah, dan menguasai rute jalan.</p>
                            <span class="mt-3 text-xs font-bold text-blue-600">+ Rp 150.000 / hari</span>
                        </label>
                    </div>
                </div>

                <!-- 3. Alamat Pengantaran Mobil -->
                <div class="pt-2 border-t border-slate-100">
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-3 flex items-center space-x-2">
                        <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs">3</span>
                        <span>Alamat Lengkap Pengantaran (Delivery)</span>
                    </h2>

                    <div>
                        <label for="delivery_address" class="block text-xs font-semibold text-slate-700 mb-1">
                            Alamat Tujuan Pengiriman Unit Mobil <span class="text-red-500">*</span>
                        </label>
                        <textarea name="delivery_address" id="delivery_address" rows="3" required
                                  class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition"
                                  placeholder="Contoh: Jl. Kemang Raya No. 12, RT 02 / RW 05, Kel. Bangka, Mampang Prapatan, Jakarta Selatan (Patokan: Depan Minimarket)">{{ old('delivery_address', auth()->user()->address) }}</textarea>
                        <p class="text-[11px] text-slate-400 mt-1">
                            <i class="fa-solid fa-circle-info text-blue-500 mr-1"></i> Driver rental akan mengantarkan mobil tepat ke alamat ini sebelum jadwal mulai sewa.
                        </p>
                    </div>
                </div>

                <!-- 4. Catatan Tambahan -->
                <div class="pt-2 border-t border-slate-100">
                    <label for="notes" class="block text-xs font-semibold text-slate-700 mb-1">Catatan Tambahan (Opsional)</label>
                    <textarea name="notes" id="notes" rows="2"
                              class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition"
                              placeholder="Misal: Jam pengantaran yang diinginkan (contoh: 08:00 pagi), kebutuhan child car seat, dll.">{{ old('notes') }}</textarea>
                </div>

                <!-- Submit Button -->
                <div class="pt-4 border-t border-slate-100">
                    <button type="submit" class="w-full py-4 px-6 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl shadow-lg shadow-blue-600/30 transition text-base flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-lock"></i>
                        <span>Konfirmasi Booking Sekarang</span>
                    </button>
                    <p class="text-center text-[11px] text-slate-400 mt-2">
                        Status booking awal akan menjadi <strong>Pending / Menunggu Konfirmasi</strong> hingga diverifikasi oleh Admin.
                    </p>
                </div>
            </form>
        </div>

        <!-- Right: Real-time Price Breakdown & Selected Car Summary -->
        <div class="lg:col-span-5 space-y-6">
            <!-- Selected Car Card -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Mobil Pilihan Anda</h3>

                <div class="flex items-center space-x-4 pb-4 border-b border-slate-100">
                    <img src="{{ $car->image_url }}"
                         alt="{{ $car->brand }} {{ $car->model }}"
                         class="w-20 h-16 rounded-xl object-cover border border-slate-200">
                    <div>
                        <h4 class="font-black text-slate-900 text-sm">{{ $car->brand }} {{ $car->model }}</h4>
                        <div class="text-xs text-slate-500 font-mono">{{ $car->license_plate }}</div>
                        <div class="text-xs text-blue-600 font-bold mt-0.5">{{ $car->formatted_price }} / hari</div>
                    </div>
                </div>

                <!-- Real-time Cost Calculation Box -->
                <div class="mt-4 space-y-3 text-xs">
                    <div class="flex justify-between items-center text-slate-600">
                        <span>Durasi Sewa:</span>
                        <span class="font-bold text-slate-900"><span x-text="durationDays"></span> Hari</span>
                    </div>

                    <div class="flex justify-between items-center text-slate-600">
                        <span>Biaya Sewa Unit:</span>
                        <span class="font-bold text-slate-900" x-text="formatRupiah(carRentalFee)"></span>
                    </div>

                    <div class="flex justify-between items-center text-slate-600">
                        <span>Layanan Driver:</span>
                        <span class="font-bold" :class="withDriver ? 'text-blue-600' : 'text-slate-500'" x-text="formatRupiah(driverFee)"></span>
                    </div>

                    <div class="flex justify-between items-center text-slate-600">
                        <span>Biaya Pengantaran Unit:</span>
                        <span class="font-bold text-emerald-600">GRATIS (Termasuk)</span>
                    </div>

                    <div class="pt-3 border-t-2 border-dashed border-slate-200 flex justify-between items-center">
                        <div>
                            <span class="block text-xs font-semibold text-slate-500">Total Estimasi Biaya</span>
                            <span class="text-[10px] text-slate-400">Termasuk mobil + driver</span>
                        </div>
                        <div class="text-xl font-black text-blue-600" x-text="formatRupiah(totalPrice)"></div>
                    </div>
                </div>
            </div>

            <!-- Customer Identity Summary -->
            <div class="bg-slate-100 rounded-2xl p-5 border border-slate-200 text-xs space-y-2">
                <div class="font-bold text-slate-800 flex items-center space-x-2">
                    <i class="fa-solid fa-address-card text-blue-600"></i>
                    <span>Data Pemesan Terdaftar:</span>
                </div>
                <div class="text-slate-600 space-y-1 pt-1">
                    <div><strong>Nama:</strong> {{ auth()->user()->name }}</div>
                    <div><strong>Email:</strong> {{ auth()->user()->email }}</div>
                    <div><strong>WhatsApp:</strong> {{ auth()->user()->phone_number ?: '-' }}</div>
                    <div><strong>NIK KTP:</strong> {{ auth()->user()->nik_ktp ?: '-' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
