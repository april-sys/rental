@extends('layouts.app')

@section('title', 'Live Tracking #' . $booking->booking_code . ' - ' . $booking->car->brand . ' ' . $booking->car->model)

@push('styles')
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <style>
        #deliveryMap {
            z-index: 10;
        }
        .pulse-marker {
            box-shadow: 0 0 0 0 rgba(147, 51, 234, 0.7);
            animation: pulse-purple 2s infinite;
        }
        @keyframes pulse-purple {
            0% {
                box-shadow: 0 0 0 0 rgba(147, 51, 234, 0.7);
            }
            70% {
                box-shadow: 0 0 0 16px rgba(147, 51, 234, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(147, 51, 234, 0);
            }
        }
    </style>
@endpush

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- Breadcrumb & Back -->
    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('customer.bookings') }}" class="inline-flex items-center space-x-1.5 text-xs text-slate-500 hover:text-blue-600">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Riwayat Pesanan</span>
        </a>

        @if($booking->canCancel())
            <form action="{{ route('customer.bookings.cancel', $booking) }}" method="POST"
                  onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')">
                @csrf
                <button type="submit" class="px-3.5 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl text-xs font-bold transition border border-red-200">
                    Batalkan Pesanan
                </button>
            </form>
        @endif
    </div>

    <!-- Booking Header Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100">
            <div>
                <span class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Status Pemesanan Rental</span>
                <div class="flex items-center space-x-3 mt-1">
                    <h1 class="text-2xl font-black text-slate-900 font-mono">{{ $booking->booking_code }}</h1>
                    <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $booking->status_badge_class }}">
                        {{ $booking->status_label }}
                    </span>
                </div>
            </div>

            <div class="text-left sm:text-right text-xs text-slate-500">
                <div>Tanggal Pengajuan: <strong>{{ $booking->created_at->format('d M Y, H:i') }} WIB</strong></div>
                <div class="mt-0.5">Layanan: <strong>Pengantaran Unit ke Alamat (Delivery)</strong></div>
            </div>
        </div>

        <!-- Visual Tracking Stepper (Progress Tracker) -->
        @if(!in_array($booking->status, ['rejected', 'cancelled']))
            @php
                $stepIndex = match($booking->status) {
                    'pending' => 1,
                    'approved' => 2,
                    'delivering' => 3,
                    'in_use' => 4,
                    'returned' => 4,
                    'completed' => 5,
                    default => 1,
                };
            @endphp
            <div class="pt-8 pb-3">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Alur & Progress Pengantaran</h3>
                    @if($booking->isDelivering())
                        <span class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-700 border border-purple-200 animate-pulse">
                            <span class="w-2 h-2 rounded-full bg-purple-600"></span>
                            <span>Live: Armada Sedang Dikirim</span>
                        </span>
                    @endif
                </div>

                <div class="relative">
                    <!-- Progress Bar Background Line -->
                    <div class="overflow-hidden h-2.5 mb-6 text-xs flex rounded-full bg-slate-100">
                        <div style="width: {{ (($stepIndex - 1) / 4) * 100 }}%"
                             class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-600 transition-all duration-700"></div>
                    </div>

                    <!-- Steps Badges -->
                    <div class="grid grid-cols-5 text-center text-xs">
                        <!-- Step 1: Pending -->
                        <div class="space-y-1.5">
                            <div class="w-9 h-9 rounded-full {{ $stepIndex >= 1 ? 'bg-blue-600 text-white shadow-md' : 'bg-slate-200 text-slate-500' }} flex items-center justify-center font-bold mx-auto text-xs transition">
                                <i class="fa-solid fa-clock"></i>
                            </div>
                            <div class="font-bold text-slate-800 text-[11px]">Pending</div>
                            <div class="text-[10px] text-slate-400 hidden sm:block">Menunggu Verifikasi</div>
                        </div>

                        <!-- Step 2: Approved -->
                        <div class="space-y-1.5">
                            <div class="w-9 h-9 rounded-full {{ $stepIndex >= 2 ? 'bg-blue-600 text-white shadow-md' : 'bg-slate-200 text-slate-500' }} flex items-center justify-center font-bold mx-auto text-xs transition">
                                <i class="fa-solid fa-circle-check"></i>
                            </div>
                            <div class="font-bold text-slate-800 text-[11px]">Disetujui</div>
                            <div class="text-[10px] text-slate-400 hidden sm:block">Siap Dikirim</div>
                        </div>

                        <!-- Step 3: Delivering (With Animation Highlight) -->
                        <div class="space-y-1.5">
                            <div class="relative w-9 h-9 mx-auto">
                                @if($stepIndex === 3)
                                    <div class="absolute inset-0 rounded-full bg-purple-500 animate-ping opacity-60"></div>
                                @endif
                                <div class="relative w-9 h-9 rounded-full {{ $stepIndex >= 3 ? 'bg-purple-600 text-white shadow-lg shadow-purple-600/30' : 'bg-slate-200 text-slate-500' }} flex items-center justify-center font-bold text-xs">
                                    <i class="fa-solid fa-truck-fast"></i>
                                </div>
                            </div>
                            <div class="font-black {{ $stepIndex === 3 ? 'text-purple-700' : 'text-slate-800' }} text-[11px]">On Delivery</div>
                            <div class="text-[10px] text-slate-400 hidden sm:block">Dalam Perjalanan</div>
                        </div>

                        <!-- Step 4: In Use -->
                        <div class="space-y-1.5">
                            <div class="w-9 h-9 rounded-full {{ $stepIndex >= 4 ? 'bg-indigo-600 text-white shadow-md' : 'bg-slate-200 text-slate-500' }} flex items-center justify-center font-bold mx-auto text-xs transition">
                                <i class="fa-solid fa-car-side"></i>
                            </div>
                            <div class="font-bold text-slate-800 text-[11px]">In Use</div>
                            <div class="text-[10px] text-slate-400 hidden sm:block">Sedang Digunakan</div>
                        </div>

                        <!-- Step 5: Completed -->
                        <div class="space-y-1.5">
                            <div class="w-9 h-9 rounded-full {{ $stepIndex >= 5 ? 'bg-emerald-600 text-white shadow-md' : 'bg-slate-200 text-slate-500' }} flex items-center justify-center font-bold mx-auto text-xs transition">
                                <i class="fa-solid fa-flag-checkered"></i>
                            </div>
                            <div class="font-bold text-slate-800 text-[11px]">Selesai</div>
                            <div class="text-[10px] text-slate-400 hidden sm:block">Unit Dikembalikan</div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="my-6 p-4 rounded-xl {{ $booking->status === 'cancelled' ? 'bg-gray-100 text-gray-800' : 'bg-red-50 text-red-800 border border-red-200' }} text-xs">
                <strong class="font-bold block mb-1">
                    {{ $booking->status === 'cancelled' ? 'Pesanan ini telah dibatalkan.' : 'Pesanan ini telah ditolak oleh Admin.' }}
                </strong>
                @if($booking->notes)
                    <p class="text-slate-600 mt-1">{{ $booking->notes }}</p>
                @endif
            </div>
        @endif
    </div>

    <!-- ========================================================================= -->
    <!-- WIDGET STATUS ON DELIVERY & LIVE ETA ALA GRAB / GOJEK RIDE-HAILING        -->
    <!-- ========================================================================= -->
    @if($booking->isDelivering())
        <div class="bg-gradient-to-br from-slate-900 via-purple-950 to-slate-900 text-white rounded-3xl p-6 sm:p-8 shadow-2xl border border-purple-500/30 mb-8 relative overflow-hidden"
             x-data="{
                remainingMinutes: {{ $booking->remaining_minutes }},
                etaClock: '{{ $booking->estimated_arrival_time ? $booking->estimated_arrival_time->format('H:i') : '' }}',
                init() {
                    // Update remaining minutes counter locally every 60 seconds
                    setInterval(() => {
                        if (this.remainingMinutes > 1) {
                            this.remainingMinutes--;
                        }
                    }, 60000);
                }
             }">

            <!-- Subtle background grid effect -->
            <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#a855f7_1px,transparent_1px)] [background-size:16px_16px]"></div>

            <div class="relative z-10">
                <!-- Top Status Badge -->
                <div class="flex flex-wrap items-center justify-between gap-3 pb-6 border-b border-purple-800/60">
                    <div class="flex items-center space-x-2.5">
                        <span class="w-3 h-3 rounded-full bg-emerald-400 animate-ping"></span>
                        <span class="text-xs font-black uppercase tracking-wider text-purple-200">Live Delivery Tracking Aktif</span>
                    </div>

                    <div class="text-xs text-purple-200 bg-purple-900/60 px-3.5 py-1.5 rounded-full border border-purple-400/30">
                        <i class="fa-solid fa-clock mr-1 text-purple-300"></i>
                        <span>Waktu Berangkat: <strong>{{ $booking->formatted_dispatch_time }}</strong></span>
                    </div>
                </div>

                <!-- ETA Highlight Card -->
                <div class="my-6 grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
                    <div class="md:col-span-7 space-y-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-purple-300">Perkiraan Waktu Sampai (ETA):</span>
                        <div class="flex items-baseline space-x-3">
                            <span class="text-3xl sm:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 via-teal-300 to-sky-300">
                                <span x-text="remainingMinutes"></span> Menit Lagi
                            </span>
                            <span class="text-sm font-semibold text-purple-200">
                                (Pukul <span x-text="etaClock"></span> WIB)
                            </span>
                        </div>
                        <p class="text-xs text-purple-200 leading-relaxed max-w-xl">
                            Driver sedang membawa armada mobil menuju alamat Anda. Harap pastikan nomor telepon Anda aktif untuk mempermudah serah terima kunci & unit mobil.
                        </p>
                    </div>

                    <!-- Driver Profile Box (Ala Grab) -->
                    <div class="md:col-span-5 bg-white/10 backdrop-blur-md rounded-2xl p-4 sm:p-5 border border-white/15 shadow-xl">
                        <div class="text-[11px] font-bold uppercase tracking-wider text-purple-300 mb-2">Driver Pengantar Anda:</div>
                        <div class="flex items-center space-x-3 mb-3">
                            <div class="relative">
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-purple-500 to-indigo-600 text-white flex items-center justify-center text-lg font-bold shadow-md">
                                    <i class="fa-solid fa-user-tie"></i>
                                </div>
                                <span class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 rounded-full border-2 border-slate-900 flex items-center justify-center text-[8px]" title="Terverifikasi">
                                    <i class="fa-solid fa-check text-white"></i>
                                </span>
                            </div>

                            <div class="flex-1 min-w-0">
                                <h4 class="font-bold text-white text-sm truncate">{{ $booking->courier_name ?: 'Bambang Sudiro' }}</h4>
                                <div class="text-xs text-purple-200 font-mono">{{ $booking->courier_phone ?: '081298765432' }}</div>
                                <div class="text-[10px] text-emerald-400 mt-0.5">
                                    <i class="fa-solid fa-circle text-[6px] mr-1"></i>Driver Resmi AutoRent
                                </div>
                            </div>
                        </div>

                        <!-- CTA Actions: WhatsApp & Telepon -->
                        <div class="grid grid-cols-2 gap-2 pt-2 border-t border-white/10">
                            @if($booking->courier_whatsapp_url)
                                <a href="{{ $booking->courier_whatsapp_url }}" target="_blank"
                                   class="py-2.5 px-3 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition shadow-md flex items-center justify-center space-x-1.5">
                                    <i class="fa-brands fa-whatsapp text-sm"></i>
                                    <span>Chat WA</span>
                                </a>
                            @else
                                <span class="py-2.5 px-3 bg-slate-700 text-slate-300 rounded-xl text-xs font-semibold text-center">Chat WA</span>
                            @endif

                            @if($booking->courier_phone)
                                <a href="tel:{{ $booking->courier_phone }}"
                                   class="py-2.5 px-3 bg-white/15 hover:bg-white/25 text-white rounded-xl text-xs font-bold transition flex items-center justify-center space-x-1.5 border border-white/20">
                                    <i class="fa-solid fa-phone text-xs"></i>
                                    <span>Telepon</span>
                                </a>
                            @else
                                <span class="py-2.5 px-3 bg-slate-700 text-slate-300 rounded-xl text-xs font-semibold text-center">Telepon</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Two-point Route Journey Indicator -->
                <div class="bg-black/25 rounded-2xl p-4 sm:p-5 border border-white/10 text-xs">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Origin -->
                        <div class="flex items-start space-x-3">
                            <span class="w-7 h-7 rounded-lg bg-blue-500/30 text-blue-300 flex items-center justify-center mt-0.5 shrink-0 border border-blue-400/40">
                                <i class="fa-solid fa-building text-xs"></i>
                            </span>
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Titik Keberangkatan (Pool Rental):</span>
                                <span class="font-semibold text-white leading-relaxed">{{ $booking->origin_address }}</span>
                            </div>
                        </div>

                        <!-- Destination -->
                        <div class="flex items-start space-x-3">
                            <span class="w-7 h-7 rounded-lg bg-emerald-500/30 text-emerald-300 flex items-center justify-center mt-0.5 shrink-0 border border-emerald-400/40">
                                <i class="fa-solid fa-location-dot text-xs"></i>
                            </span>
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Tujuan Pengantaran (Alamat Anda):</span>
                                <span class="font-semibold text-white leading-relaxed">{{ $booking->delivery_address }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Interactive Leaflet.js Route Map -->
                <div class="mt-6 pt-6 border-t border-purple-800/60">
                    <div class="flex items-center justify-between mb-3 text-xs">
                        <div class="flex items-center space-x-2 font-bold text-white">
                            <i class="fa-solid fa-map-location-dot text-emerald-400"></i>
                            <span>Peta Rute Pengiriman Armada (Live Simulation)</span>
                        </div>
                        <span class="text-[11px] text-purple-300 hidden sm:inline-block">OpenStreetMap & Leaflet.js</span>
                    </div>

                    <!-- Map Container -->
                    <div class="relative rounded-2xl overflow-hidden border border-purple-500/30 shadow-2xl">
                        <div id="deliveryMap" class="w-full h-80 sm:h-96 bg-slate-800"></div>

                        <!-- Map Live Info Overlay Badge -->
                        <div class="absolute bottom-3 left-3 right-3 sm:right-auto z-20 bg-slate-900/90 backdrop-blur-md px-4 py-2.5 rounded-xl border border-white/20 text-xs text-white shadow-xl flex items-center space-x-4">
                            <div class="flex items-center space-x-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                <span class="font-bold">Estimasi Jarak: ±8.5 km</span>
                            </div>
                            <span class="text-slate-400">|</span>
                            <div class="text-slate-300">
                                <i class="fa-solid fa-car text-purple-400 mr-1"></i>
                                <span>Unit: <strong>{{ $booking->car->brand }} {{ $booking->car->model }}</strong> ({{ $booking->car->license_plate }})</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Left: Car Details & Delivery Address -->
        <div class="lg:col-span-7 space-y-6">
            <!-- Delivery Destination Card -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4 flex items-center space-x-2">
                    <i class="fa-solid fa-location-dot text-blue-600"></i>
                    <span>Tujuan Pengantaran Unit (Delivery Address)</span>
                </h3>
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 text-sm font-medium text-slate-800 leading-relaxed">
                    {{ $booking->delivery_address }}
                </div>
                <div class="mt-3 flex items-center justify-between text-xs text-slate-500">
                    <span>Penerima: <strong>{{ $booking->user->name }}</strong></span>
                    <span>No. WhatsApp: <strong>{{ $booking->user->phone_number ?: '-' }}</strong></span>
                </div>
            </div>

            <!-- Car Unit Details -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4 flex items-center space-x-2">
                    <i class="fa-solid fa-car text-blue-600"></i>
                    <span>Kendaraan yang Disewa</span>
                </h3>

                <div class="flex items-center space-x-4">
                    <img src="{{ $booking->car->image_url }}"
                         alt="{{ $booking->car->brand }}"
                         class="w-28 h-20 rounded-xl object-cover border border-slate-200">
                    <div>
                        <h4 class="font-bold text-slate-900 text-base">{{ $booking->car->brand }} {{ $booking->car->model }}</h4>
                        <div class="text-xs text-slate-500 font-mono mt-0.5">{{ $booking->car->license_plate }}</div>
                        <div class="text-xs text-slate-500 mt-1 flex items-center space-x-2">
                            <span><i class="fa-solid fa-user-group text-slate-400 mr-1"></i>{{ $booking->car->capacity }} Kursi</span>
                            <span>•</span>
                            <span class="font-semibold text-blue-600">{{ $booking->driver_service ? 'Layanan Driver Profesional' : 'Lepas Kunci (Tanpa Driver)' }}</span>
                        </div>
                    </div>
                </div>

                @if($booking->notes)
                    <div class="mt-4 pt-4 border-t border-slate-100 text-xs">
                        <span class="font-bold text-slate-700 block mb-1">Catatan Pesanan / Pengembalian:</span>
                        <div class="p-3 bg-slate-50 rounded-xl text-slate-600 whitespace-pre-line">{{ $booking->notes }}</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right: Invoice & Billing Breakdown -->
        <div class="lg:col-span-5 space-y-6">
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4 flex items-center space-x-2">
                    <i class="fa-solid fa-file-invoice-dollar text-blue-600"></i>
                    <span>Rincian Pembayaran (Invoice)</span>
                </h3>

                <div class="space-y-3 text-xs">
                    <div class="flex justify-between items-center text-slate-600">
                        <span>Periode Sewa:</span>
                        <span class="font-semibold text-slate-800">
                            {{ $booking->start_date->format('d M Y') }} - {{ $booking->end_date->format('d M Y') }}
                        </span>
                    </div>

                    <div class="flex justify-between items-center text-slate-600">
                        <span>Durasi Total:</span>
                        <span class="font-bold text-slate-900">{{ $booking->duration_days }} Hari</span>
                    </div>

                    <div class="flex justify-between items-center text-slate-600">
                        <span>Biaya Sewa Unit ({{ $booking->duration_days }} x Rp {{ number_format($booking->car->price_per_day, 0, ',', '.') }}):</span>
                        <span class="font-bold text-slate-900">Rp {{ number_format($booking->car_rental_fee, 0, ',', '.') }}</span>
                    </div>

                    <div class="flex justify-between items-center text-slate-600">
                        <span>Biaya Layanan Driver:</span>
                        <span class="font-bold {{ $booking->driver_service ? 'text-blue-600' : 'text-slate-500' }}">
                            Rp {{ number_format($booking->driver_fee, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="flex justify-between items-center text-slate-600">
                        <span>Pengantaran ke Alamat:</span>
                        <span class="font-bold text-emerald-600">GRATIS</span>
                    </div>

                    @if($booking->late_penalty_fee > 0)
                        <div class="flex justify-between items-center text-red-600 pt-2 border-t border-slate-100">
                            <span>Denda Keterlambatan / Kerusakan:</span>
                            <span class="font-bold">+ Rp {{ number_format($booking->late_penalty_fee, 0, ',', '.') }}</span>
                        </div>
                    @endif

                    <div class="pt-4 border-t-2 border-dashed border-slate-200 flex justify-between items-center">
                        <div>
                            <span class="text-xs font-bold text-slate-700 block">Total Akhir Biaya</span>
                            <span class="text-[10px] text-slate-400">Termasuk pajak & asuransi</span>
                        </div>
                        <div class="text-2xl font-black text-blue-600">
                            Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                <!-- Assistance box -->
                <div class="mt-6 p-4 rounded-xl bg-blue-50 border border-blue-200 text-xs text-blue-900 flex items-start space-x-3">
                    <i class="fa-solid fa-headset text-blue-600 text-lg mt-0.5"></i>
                    <div>
                        <strong class="font-bold block">Butuh bantuan pengantaran?</strong>
                        <span>Hubungi customer support di <strong>+62 812-3456-7890</strong> dengan menyebutkan kode booking <strong>{{ $booking->booking_code }}</strong>.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if($booking->isDelivering())
    <!-- Leaflet JS CDN -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Coordinate points for Jakarta simulation
            // Origin: AutoRent Pool (Gambir / Jakarta Pusat)
            const originCoord = [-6.175392, 106.827153];
            // Destination: Customer Address (Senayan / Jakarta Selatan)
            const destCoord = [-6.229746, 106.809852];
            // Intermediate route waypoints
            const routeWaypoints = [
                originCoord,
                [-6.1850, 106.8230], // Thamrin
                [-6.1950, 106.8220], // Bundaran HI
                [-6.2080, 106.8210], // Sudirman
                [-6.2200, 106.8150], // Semanggi
                destCoord
            ];

            // Initialize Map
            const map = L.map('deliveryMap', {
                center: [-6.2024, 106.8180],
                zoom: 13,
                zoomControl: true,
                scrollWheelZoom: false
            });

            // Modern OpenStreetMap Tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            // 1. Origin Marker (Pool Kantor)
            const originIcon = L.divIcon({
                className: 'custom-origin-icon',
                html: `
                    <div class="w-10 h-10 rounded-2xl bg-blue-600 text-white flex items-center justify-center shadow-xl border-2 border-white text-base">
                        <i class="fa-solid fa-warehouse"></i>
                    </div>
                `,
                iconSize: [40, 40],
                iconAnchor: [20, 20]
            });
            L.marker(originCoord, { icon: originIcon })
                .addTo(map)
                .bindPopup('<b>Kantor Pusat AutoRent</b><br>Pool Titik Keberangkatan');

            // 2. Destination Marker (Alamat Customer)
            const destIcon = L.divIcon({
                className: 'custom-dest-icon',
                html: `
                    <div class="w-10 h-10 rounded-2xl bg-red-600 text-white flex items-center justify-center shadow-xl border-2 border-white text-base">
                        <i class="fa-solid fa-location-dot"></i>
                    </div>
                `,
                iconSize: [40, 40],
                iconAnchor: [20, 20]
            });
            L.marker(destCoord, { icon: destIcon })
                .addTo(map)
                .bindPopup('<b>Alamat Tujuan Anda</b><br>{{ addslashes($booking->delivery_address) }}');

            // 3. Polyline Route (Connecting points)
            const routeLine = L.polyline(routeWaypoints, {
                color: '#7c3aed', // Purple color
                weight: 5,
                opacity: 0.85,
                dashArray: '8, 12'
            }).addTo(map);

            // 4. Moving Car Marker (Simulasi Driver)
            let currentStep = 2; // Midpoint around Bundaran HI / Sudirman
            const carIcon = L.divIcon({
                className: 'custom-car-icon pulse-marker',
                html: `
                    <div class="w-11 h-11 rounded-2xl bg-purple-700 text-white flex items-center justify-center shadow-2xl border-2 border-white text-lg">
                        <i class="fa-solid fa-car-side"></i>
                    </div>
                `,
                iconSize: [44, 44],
                iconAnchor: [22, 22]
            });

            const carMarker = L.marker(routeWaypoints[currentStep], { icon: carIcon })
                .addTo(map)
                .bindPopup('<b>Armada Sedang Bergerak</b><br>Driver: {{ addslashes($booking->courier_name ?: "Driver AutoRent") }}<br>{{ $booking->car->brand }} {{ $booking->car->model }}')
                .openPopup();

            // Fit bounds to show both origin and destination nicely
            map.fitBounds(routeLine.getBounds(), { padding: [50, 50] });

            // Subtle animated simulation: car moves gently forward on route
            setInterval(() => {
                currentStep = (currentStep + 1) % routeWaypoints.length;
                if (currentStep === 0) currentStep = 1; // don't stay at origin
                const nextPos = routeWaypoints[currentStep];
                carMarker.setLatLng(nextPos);
            }, 8000);
        });
    </script>
@endif
@endpush
