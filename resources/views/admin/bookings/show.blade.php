@extends('layouts.app')

@section('title', 'Kelola Booking #' . $booking->booking_code . ' - Admin')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- Header & Breadcrumb -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="{{ route('admin.bookings.index') }}" class="inline-flex items-center space-x-1.5 text-xs text-slate-500 hover:text-blue-600 mb-2">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali ke Daftar Transaksi</span>
            </a>
            <div class="flex items-center space-x-3">
                <h1 class="text-2xl font-black text-slate-900 font-mono tracking-tight">{{ $booking->booking_code }}</h1>
                <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $booking->status_badge_class }}">
                    {{ $booking->status_label }}
                </span>
            </div>
        </div>

        <div class="text-right text-xs text-slate-400">
            Dibuat: {{ $booking->created_at->format('d M Y, H:i') }} WIB
        </div>
    </div>

    <!-- MAIN WORKFLOW ACTION PANEL (The Controller / Action Center) -->
    <div class="bg-gradient-to-r from-blue-900 to-indigo-900 text-white rounded-3xl p-6 sm:p-8 shadow-xl mb-8 relative overflow-hidden">
        <div class="relative z-10">
            <div class="flex items-center space-x-2 text-xs font-bold uppercase tracking-wider text-blue-300 mb-2">
                <i class="fa-solid fa-bolt text-amber-400"></i>
                <span>Pusat Kendali Alur Kerja Rental (Workflow Action Center)</span>
            </div>

            <!-- Current Stage Guidance -->
            <div class="mb-6">
                <h2 class="text-xl font-black text-white">
                    @if($booking->status === 'pending')
                        Langkah 1: Verifikasi & Konfirmasi Booking
                    @elseif($booking->status === 'approved')
                        Langkah 2: Pengiriman Mobil ke Alamat Customer
                    @elseif($booking->status === 'delivering')
                        Langkah 3: Konfirmasi Serah Terima Mobil ke Customer
                    @elseif($booking->status === 'in_use')
                        Langkah 4: Mobil Sedang Digunakan Customer
                    @elseif($booking->status === 'returned')
                        Langkah 5: Pengecekan Kondisi & Kalkulasi Denda Pengembalian
                    @elseif($booking->status === 'completed')
                        Transaksi Selesai (Completed)
                    @elseif($booking->status === 'rejected')
                        Transaksi Ditolak
                    @elseif($booking->status === 'cancelled')
                        Transaksi Dibatalkan Customer
                    @endif
                </h2>
                <p class="text-xs sm:text-sm text-blue-200 mt-1 max-w-2xl leading-relaxed">
                    @if($booking->status === 'pending')
                        Periksa identitas pemesan, rentang tanggal sewa, dan ketersediaan unit. Klik tombol "Setujui Booking" untuk mengonfirmasi jadwal (status mobil akan otomatis ditandai booked).
                    @elseif($booking->status === 'approved')
                        Mobil sudah disetujui dan siap dikirim. Saat kurir/driver berangkat mengantarkan armada ke alamat tujuan, klik "Kirim Mobil (On Delivery)".
                    @elseif($booking->status === 'delivering')
                        Armada sedang dalam perjalanan. Setelah customer menerima mobil dan menandatangani berita acara, klik "Mobil Diterima Customer (In Use)".
                    @elseif($booking->status === 'in_use')
                        Customer sedang menggunakan mobil. Setelah masa sewa usai dan unit diantar/dijemput kembali, Anda dapat menandai pengembalian dan menghitung denda jika ada.
                    @elseif($booking->status === 'returned')
                        Mobil telah diterima kembali. Lakukan inspeksi fisik (bodi, mesin, kebersihan) dan cek ketepatan waktu. Input nominal denda jika ada keterlambatan atau kerusakan.
                    @elseif($booking->status === 'completed')
                        Seluruh alur sewa telah selesai. Biaya final dan denda (jika ada) telah tercatat. Status mobil otomatis kembali Tersedia (Available) untuk customer lain.
                    @endif
                </p>
            </div>

            <!-- Action Buttons based on status -->
            <div class="flex flex-wrap gap-3 items-center pt-2 border-t border-blue-800/80">
                <!-- Status: PENDING -->
                @if($booking->status === 'pending')
                    <form action="{{ route('admin.bookings.approve', $booking) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-5 py-3 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl text-xs font-bold transition shadow-lg flex items-center space-x-2">
                            <i class="fa-solid fa-circle-check"></i>
                            <span>Confirm Booking (Setujui & Jadwalkan)</span>
                        </button>
                    </form>

                    <!-- Reject Button / Dropdown -->
                    <div x-data="{ showRejectModal: false }">
                        <button type="button" @click="showRejectModal = true" class="px-4 py-3 bg-red-600/80 hover:bg-red-600 text-white rounded-xl text-xs font-bold transition flex items-center space-x-2">
                            <i class="fa-solid fa-ban"></i>
                            <span>Tolak Booking</span>
                        </button>

                        <!-- Reject Modal -->
                        <div x-show="showRejectModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-sm">
                            <div class="bg-white text-slate-800 rounded-2xl max-w-md w-full p-6 shadow-2xl">
                                <h3 class="text-base font-bold text-slate-900 mb-2">Tolak Permintaan Booking</h3>
                                <p class="text-xs text-slate-500 mb-4">Berikan alasan penolakan untuk catatan customer.</p>

                                <form action="{{ route('admin.bookings.reject', $booking) }}" method="POST">
                                    @csrf
                                    <textarea name="reason" rows="3" required placeholder="Contoh: Jadwal bertabrakan dengan jadwal servis rutin armada..."
                                              class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-red-500 mb-4"></textarea>

                                    <div class="flex justify-end space-x-2">
                                        <button type="button" @click="showRejectModal = false" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-semibold">
                                            Batal
                                        </button>
                                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-xl text-xs font-bold">
                                            Konfirmasi Tolak
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Status: APPROVED -->
                @if($booking->status === 'approved')
                    <div x-data="{ showDispatchModal: false, durationMin: 35 }">
                        <button type="button" @click="showDispatchModal = true" class="px-5 py-3 bg-purple-500 hover:bg-purple-600 text-white rounded-xl text-xs font-bold transition shadow-lg flex items-center space-x-2">
                            <i class="fa-solid fa-truck-fast"></i>
                            <span>Send Car (Kirim Mobil & Input Driver)</span>
                        </button>

                        <!-- Dispatch Delivery Modal -->
                        <div x-show="showDispatchModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-sm">
                            <div class="bg-white text-slate-800 rounded-2xl max-w-lg w-full p-6 sm:p-8 shadow-2xl text-left">
                                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                                    <div class="flex items-center space-x-2 text-purple-600 font-bold text-base">
                                        <i class="fa-solid fa-truck-ramp-box"></i>
                                        <span>Konfirmasi Pengiriman Armada</span>
                                    </div>
                                    <button type="button" @click="showDispatchModal = false" class="text-slate-400 hover:text-slate-600">
                                        <i class="fa-solid fa-xmark text-lg"></i>
                                    </button>
                                </div>

                                <p class="text-xs text-slate-500 mb-5">
                                    Input data driver/kurir yang bertugas mengantar mobil <strong>{{ $booking->car->brand }} {{ $booking->car->model }}</strong> ke alamat customer. Sistem akan mengaktifkan live tracking & ETA.
                                </p>

                                <form action="{{ route('admin.bookings.deliver', $booking) }}" method="POST" class="space-y-4">
                                    @csrf

                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Driver / Kurir Pengantar <span class="text-red-500">*</span></label>
                                        <input type="text" name="courier_name" required value="{{ old('courier_name', 'Bambang Sudiro (Driver AutoRent)') }}"
                                               placeholder="Contoh: Pak Bambang (Driver AutoRent)"
                                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-purple-500 focus:bg-white">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nomor WhatsApp / HP Driver <span class="text-red-500">*</span></label>
                                        <input type="text" name="courier_phone" required value="{{ old('courier_phone', '081298765432') }}"
                                               placeholder="Contoh: 081298765432"
                                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-purple-500 focus:bg-white">
                                        <span class="text-[10px] text-slate-400 mt-0.5 block">Customer dapat langsung menghubungi via tombol WhatsApp.</span>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Estimasi Durasi Perjalanan (Menit) <span class="text-red-500">*</span></label>
                                        <div class="flex items-center space-x-3">
                                            <input type="number" name="estimated_duration_minutes" x-model="durationMin" min="5" max="720" required
                                                   class="w-32 px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:bg-white">
                                            <span class="text-xs text-slate-600 font-semibold">Menit perjalanan</span>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Titik Awal Keberangkatan (Pool / Kantor)</label>
                                        <input type="text" name="office_origin_address" value="{{ old('office_origin_address', 'Kantor Pusat AutoRent, Jl. Merdeka No. 1, Gambir, Jakarta Pusat') }}"
                                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-purple-500 focus:bg-white">
                                    </div>

                                    <div class="p-3 bg-purple-50 border border-purple-200 rounded-xl text-xs text-purple-900">
                                        <div class="flex items-center space-x-2 font-bold mb-1">
                                            <i class="fa-solid fa-clock"></i>
                                            <span>Kalkulasi Otomatis ETA:</span>
                                        </div>
                                        <div class="text-[11px] text-purple-800">
                                            Waktu Keberangkatan: <strong>{{ now()->format('H:i') }} WIB</strong>. Perkiraan Tiba: <strong>{{ now()->addMinutes(35)->format('H:i') }} WIB</strong> (<span x-text="durationMin"></span> menit).
                                        </div>
                                    </div>

                                    <div class="flex justify-end space-x-2 pt-3 border-t border-slate-100">
                                        <button type="button" @click="showDispatchModal = false" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold">
                                            Batal
                                        </button>
                                        <button type="submit" class="px-5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold shadow-md flex items-center space-x-1.5">
                                            <i class="fa-solid fa-paper-plane"></i>
                                            <span>Berangkatkan Sekarang (On Delivery)</span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Status: DELIVERING -->
                @if($booking->status === 'delivering')
                    <div class="flex flex-wrap items-center gap-3">
                        <form action="{{ route('admin.bookings.in-use', $booking) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-5 py-3 bg-indigo-500 hover:bg-indigo-600 text-white rounded-xl text-xs font-bold transition shadow-lg flex items-center space-x-2">
                                <i class="fa-solid fa-handshake"></i>
                                <span>Konfirmasi Mobil Diterima Customer (In Use)</span>
                            </button>
                        </form>
                    </div>
                @endif

                <!-- Status: IN USE -->
                @if($booking->status === 'in_use')
                    <form action="{{ route('admin.bookings.return', $booking) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-5 py-3 bg-yellow-500 hover:bg-yellow-600 text-white rounded-xl text-xs font-bold transition shadow-lg flex items-center space-x-2">
                            <i class="fa-solid fa-arrow-rotate-left"></i>
                            <span>Mobil Dikembalikan (Tandai Masuk Garasi)</span>
                        </button>
                    </form>
                @endif

                <!-- Form Selesaikan & Hitung Denda (For in_use or returned) -->
                @if(in_array($booking->status, ['in_use', 'returned']))
                    <a href="#complete-section" class="px-5 py-3 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl text-xs font-bold transition shadow-lg flex items-center space-x-2">
                        <i class="fa-solid fa-calculator"></i>
                        <span>Complete Rental & Calculate Penalty (Input Denda & Selesai)</span>
                    </a>
                @endif

                @if($booking->status === 'completed')
                    <div class="inline-flex items-center space-x-2 px-4 py-2 bg-emerald-500/20 text-emerald-300 rounded-xl text-xs font-bold border border-emerald-400/30">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Rental ini telah selesai dan mobil sudah kembali berstatus Tersedia (Available).</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Complete Rental & Calculate Penalty Form Card (If in_use or returned) -->
    @if(in_array($booking->status, ['in_use', 'returned']))
        <div id="complete-section" class="bg-white rounded-2xl p-6 sm:p-8 border-2 border-emerald-500 shadow-lg mb-8">
            <div class="flex items-center space-x-3 mb-4">
                <span class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-lg font-bold">
                    <i class="fa-solid fa-clipboard-check"></i>
                </span>
                <div>
                    <h3 class="text-lg font-black text-slate-900">Form Penyelesaian Sewa & Perhitungan Denda</h3>
                    <p class="text-xs text-slate-500">Pemeriksaan kondisi fisik mobil, ketepatan waktu pengembalian, dan kalkulasi tagihan akhir.</p>
                </div>
            </div>

            <form action="{{ route('admin.bookings.complete', $booking) }}" method="POST" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="late_penalty_fee" class="block text-xs font-bold text-slate-700 uppercase mb-1">
                            Denda Keterlambatan / Kerusakan (IDR) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 font-bold text-xs">Rp</span>
                            <input type="number" name="late_penalty_fee" id="late_penalty_fee" value="{{ old('late_penalty_fee', 0) }}" min="0" step="10000" required
                                   class="w-full pl-10 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white">
                        </div>
                        <span class="text-[11px] text-slate-400 mt-1 block">Ketik <strong>0</strong> jika mobil dikembalikan tepat waktu dan kondisi aman tanpa kerusakan.</span>
                    </div>

                    <div>
                        <label for="notes" class="block text-xs font-bold text-slate-700 uppercase mb-1">
                            Catatan Pengecekan Kondisi Mobil
                        </label>
                        <input type="text" name="notes" id="notes" value="{{ old('notes', 'Kondisi fisik aman, bahan bakar penuh, tepat waktu.') }}"
                               placeholder="Contoh: Terlambat 3 jam, baret ringan bumper depan..."
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white">
                    </div>
                </div>

                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 text-xs text-slate-600 space-y-1">
                    <div class="font-bold text-slate-800">Catatan Perubahan Otomatis:</div>
                    <div>1. Total Tagihan Akhir akan dikalkulasikan: <code>Total = Biaya Mobil (Rp {{ number_format($booking->car_rental_fee, 0, ',', '.') }}) + Driver (Rp {{ number_format($booking->driver_fee, 0, ',', '.') }}) + Nominal Denda</code>.</div>
                    <div>2. Status transaksi booking berubah menjadi <strong>Completed (Selesai)</strong>.</div>
                    <div>3. Status armada mobil <strong>{{ $booking->car->brand }} {{ $booking->car->model }} ({{ $booking->car->license_plate }})</strong> secara otomatis kembali menjadi <strong>Tersedia (Available)</strong>.</div>
                </div>

                <div class="pt-2 flex justify-end">
                    <button type="submit" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-md transition flex items-center space-x-2">
                        <i class="fa-solid fa-check-double"></i>
                        <span>Selesaikan Transaksi & Jadikan Mobil Tersedia</span>
                    </button>
                </div>
            </form>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Customer Details & Delivery Info -->
        <div class="lg:col-span-6 space-y-6">
            <!-- Customer Card -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4 flex items-center space-x-2">
                    <i class="fa-solid fa-user text-blue-600"></i>
                    <span>Informasi Pelanggan (Customer)</span>
                </h3>

                <div class="space-y-3 text-xs">
                    <div class="flex justify-between py-1 border-b border-slate-100">
                        <span class="text-slate-500">Nama Lengkap:</span>
                        <span class="font-bold text-slate-900 text-sm">{{ $booking->user->name }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-100">
                        <span class="text-slate-500">Email:</span>
                        <span class="font-semibold text-slate-800">{{ $booking->user->email }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-100">
                        <span class="text-slate-500">No. WhatsApp/HP:</span>
                        <span class="font-bold text-blue-600">
                            {{ $booking->user->phone_number ?: '-' }}
                            @if($booking->user->phone_number)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $booking->user->phone_number) }}" target="_blank" class="ml-1 text-emerald-600 hover:underline">
                                    <i class="fa-brands fa-whatsapp"></i> Chat
                                </a>
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-100">
                        <span class="text-slate-500">Nomor NIK KTP:</span>
                        <span class="font-mono font-bold text-slate-800">{{ $booking->user->nik_ktp ?: '-' }}</span>
                    </div>
                    <div class="pt-1">
                        <span class="text-slate-500 block mb-1">Alamat Domisili KTP:</span>
                        <div class="p-2.5 bg-slate-50 rounded-xl text-slate-700 font-medium">{{ $booking->user->address ?: '-' }}</div>
                    </div>
                </div>
            </div>

            <!-- Delivery Location -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3 flex items-center space-x-2">
                    <i class="fa-solid fa-location-dot text-red-500"></i>
                    <span>Alamat Lengkap Pengantaran (Delivery Address)</span>
                </h3>
                <div class="p-4 bg-red-50/50 border border-red-100 rounded-xl text-sm font-semibold text-slate-900 leading-relaxed">
                    {{ $booking->delivery_address }}
                </div>
            </div>

            <!-- Courier & Live ETA Tracking Card -->
            @if($booking->courier_name)
                <div class="bg-white rounded-2xl p-6 border {{ $booking->isDelivering() ? 'border-purple-300 ring-2 ring-purple-100' : 'border-slate-200' }} shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center space-x-2">
                            <i class="fa-solid fa-truck-fast text-purple-600"></i>
                            <span>Informasi Driver & Pengantaran (ETA)</span>
                        </h3>
                        @if($booking->isDelivering())
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-700 animate-pulse">
                                Live On Delivery
                            </span>
                        @endif
                    </div>

                    <div class="space-y-3 text-xs">
                        <div class="flex items-center justify-between py-1 border-b border-slate-100">
                            <span class="text-slate-500">Driver Pengantar:</span>
                            <span class="font-bold text-slate-900">{{ $booking->courier_name }}</span>
                        </div>
                        <div class="flex items-center justify-between py-1 border-b border-slate-100">
                            <span class="text-slate-500">Kontak WhatsApp:</span>
                            <a href="{{ $booking->courier_whatsapp_url }}" target="_blank" class="font-bold text-emerald-600 hover:underline flex items-center space-x-1">
                                <i class="fa-brands fa-whatsapp text-sm"></i>
                                <span>{{ $booking->courier_phone }}</span>
                            </a>
                        </div>
                        <div class="flex items-center justify-between py-1 border-b border-slate-100">
                            <span class="text-slate-500">Waktu Berangkat:</span>
                            <span class="font-semibold text-slate-800">{{ $booking->formatted_dispatch_time }}</span>
                        </div>
                        <div class="flex items-center justify-between py-1 border-b border-slate-100">
                            <span class="text-slate-500">Estimasi Tiba (ETA):</span>
                            <span class="font-black text-purple-700">{{ $booking->formatted_eta }} ({{ $booking->estimated_duration_minutes }} menit)</span>
                        </div>
                        <div class="pt-1">
                            <span class="text-slate-500 block mb-1">Titik Pool Keberangkatan:</span>
                            <div class="p-2.5 bg-slate-50 rounded-xl text-slate-700 font-medium text-[11px]">{{ $booking->origin_address }}</div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Car & Invoice Breakdown -->
        <div class="lg:col-span-6 space-y-6">
            <!-- Car Unit Details -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4 flex items-center space-x-2">
                    <i class="fa-solid fa-car text-blue-600"></i>
                    <span>Informasi Armada Mobil</span>
                </h3>

                <div class="flex items-center space-x-4 pb-4 border-b border-slate-100">
                    <img src="{{ $booking->car->image_url }}"
                         alt="{{ $booking->car->brand }}"
                         class="w-24 h-16 rounded-xl object-cover border border-slate-200">
                    <div>
                        <h4 class="font-bold text-slate-900 text-sm">{{ $booking->car->brand }} {{ $booking->car->model }}</h4>
                        <div class="text-xs font-mono font-bold text-slate-600 mt-0.5">{{ $booking->car->license_plate }}</div>
                        <div class="text-xs text-blue-600 font-semibold mt-1">
                            Status Mobil Saat Ini: <span class="uppercase font-bold">{{ $booking->car->status }}</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 pt-3 text-xs">
                    <div class="p-2.5 bg-slate-50 rounded-xl">
                        <span class="text-slate-400 block mb-0.5">Kapasitas Kursi</span>
                        <span class="font-bold text-slate-800">{{ $booking->car->capacity }} Penumpang</span>
                    </div>
                    <div class="p-2.5 bg-slate-50 rounded-xl">
                        <span class="text-slate-400 block mb-0.5">Opsi Driver</span>
                        <span class="font-bold text-slate-800">{{ $booking->driver_service ? 'Dengan Driver' : 'Lepas Kunci' }}</span>
                    </div>
                </div>
            </div>

            <!-- Invoice Calculation Card -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4 flex items-center space-x-2">
                    <i class="fa-solid fa-receipt text-blue-600"></i>
                    <span>Rincian Biaya & Perhitungan</span>
                </h3>

                <div class="space-y-3 text-xs">
                    <div class="flex justify-between items-center text-slate-600">
                        <span>Periode Sewa:</span>
                        <span class="font-bold text-slate-800">{{ $booking->start_date->format('d M Y') }} s/d {{ $booking->end_date->format('d M Y') }}</span>
                    </div>

                    <div class="flex justify-between items-center text-slate-600">
                        <span>Durasi Hari:</span>
                        <span class="font-bold text-slate-900">{{ $booking->duration_days }} Hari</span>
                    </div>

                    <div class="flex justify-between items-center text-slate-600">
                        <span>Tarif Mobil ({{ $booking->duration_days }} x Rp {{ number_format($booking->car->price_per_day, 0, ',', '.') }}):</span>
                        <span class="font-bold text-slate-900">Rp {{ number_format($booking->car_rental_fee, 0, ',', '.') }}</span>
                    </div>

                    <div class="flex justify-between items-center text-slate-600">
                        <span>Layanan Driver (Rp 150.000/hr):</span>
                        <span class="font-bold text-slate-900">Rp {{ number_format($booking->driver_fee, 0, ',', '.') }}</span>
                    </div>

                    <div class="flex justify-between items-center text-slate-600">
                        <span>Denda Tambahan:</span>
                        <span class="font-bold {{ $booking->late_penalty_fee > 0 ? 'text-red-600' : 'text-slate-900' }}">
                            Rp {{ number_format($booking->late_penalty_fee, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="pt-4 border-t-2 border-dashed border-slate-200 flex justify-between items-center">
                        <span class="font-bold text-slate-800 text-sm">Total Tagihan Final:</span>
                        <span class="font-black text-2xl text-blue-600">
                            Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                @if($booking->notes)
                    <div class="mt-4 pt-4 border-t border-slate-100 text-xs">
                        <span class="text-slate-500 font-semibold block mb-1">Catatan Riwayat Transaksi:</span>
                        <div class="p-3 bg-slate-50 rounded-xl text-slate-700">{{ $booking->notes }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
