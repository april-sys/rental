@extends('layouts.app')

@section('title', 'Katalog Rental Mobil & Layanan Pengantaran')

@section('content')
<!-- Hero Section -->
<section class="relative bg-gradient-to-br from-slate-900 via-blue-950 to-slate-900 text-white overflow-hidden py-16 sm:py-24">
    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#3b82f6_1px,transparent_1px)] [background-size:16px_16px]"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 border border-blue-400/30 text-xs font-semibold backdrop-blur-sm">
                    <span class="w-2 h-2 rounded-full bg-blue-400 animate-pulse"></span>
                    <span>Layanan Pengantaran Mobil Langsung ke Pintu Rumah Anda</span>
                </div>
                <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-tight">
                    Rental Mobil Nyaman, <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-sky-300 to-indigo-300">
                        Siap Diantar Kapan Saja
                    </span>
                </h1>
                <p class="text-slate-300 text-sm sm:text-base max-w-2xl leading-relaxed">
                    Nikmati perjalanan tanpa repot. Pilih mobil impian Anda, tentukan tanggal, pilih opsi driver berpengalaman atau lepas kunci, dan mobil diantarkan langsung ke alamat Anda.
                </p>
                <div class="flex flex-wrap gap-4 justify-center lg:justify-start pt-2">
                    <a href="#katalog" class="px-6 py-3.5 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-lg shadow-blue-600/30 transition flex items-center space-x-2 text-sm">
                        <i class="fa-solid fa-car"></i>
                        <span>Pilih Mobil Sekarang</span>
                    </a>
                    <a href="#workflow" class="px-6 py-3.5 bg-slate-800/80 hover:bg-slate-800 text-slate-200 border border-slate-700 font-semibold rounded-xl transition flex items-center space-x-2 text-sm">
                        <i class="fa-solid fa-circle-info text-blue-400"></i>
                        <span>Pelajari Alur Sewa</span>
                    </a>
                </div>

                <!-- Quick Fleet Stats -->
                <div class="grid grid-cols-3 gap-4 pt-6 border-t border-slate-800/80 text-center sm:text-left">
                    <div>
                        <div class="text-2xl font-black text-white">{{ $stats['total_cars'] }}+</div>
                        <div class="text-xs text-slate-400">Pilihan Armada</div>
                    </div>
                    <div>
                        <div class="text-2xl font-black text-emerald-400">{{ $stats['available_cars'] }}</div>
                        <div class="text-xs text-slate-400">Mobil Siap Jalan</div>
                    </div>
                    <div>
                        <div class="text-2xl font-black text-blue-400">100%</div>
                        <div class="text-xs text-slate-400">Antar ke Alamat</div>
                    </div>
                </div>
            </div>

            <!-- Hero Image Banner -->
            <div class="lg:col-span-5 relative">
                <div class="relative mx-auto max-w-md lg:max-w-none">
                    <div class="absolute -inset-1 rounded-3xl bg-gradient-to-r from-blue-600 to-indigo-600 opacity-30 blur-2xl"></div>
                    <div class="relative bg-slate-800/80 rounded-2xl overflow-hidden border border-slate-700/80 shadow-2xl p-3">
                        <img src="https://images.unsplash.com/photo-1549399542-7e3f8b79c341?auto=format&fit=crop&w=1000&q=80"
                             alt="Toyota Avanza Rental"
                             class="rounded-xl w-full h-64 sm:h-80 object-cover">
                        <div class="p-4 bg-slate-900/90 rounded-xl mt-3 flex items-center justify-between">
                            <div>
                                <div class="text-xs text-slate-400">Favorit Keluarga & Bisnis</div>
                                <div class="text-base font-bold text-white">Toyota Avanza 1.5 G CVT</div>
                            </div>
                            <div class="text-right">
                                <div class="text-xs text-blue-400">Mulai dari</div>
                                <div class="text-base font-black text-emerald-400">Rp 400.000<span class="text-xs text-slate-400 font-normal">/hari</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section Alur Kerja (Workflow) -->
<section id="workflow" class="py-16 bg-white border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-12">
            <span class="text-xs font-bold uppercase tracking-wider text-blue-600 bg-blue-50 px-3 py-1 rounded-full border border-blue-200">
                Alur Kerja Transparan
            </span>
            <h2 class="text-2xl sm:text-3xl font-black text-slate-900 mt-3">
                Bagaimana Cara Menyewa Mobil di AutoRent?
            </h2>
            <p class="text-slate-500 text-sm mt-2">
                Proses penyewaan cepat, terverifikasi, dan bebas ribet dari awal pemesanan hingga pengembalian.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 relative">
            <!-- Step 1 -->
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 text-center relative group hover:border-blue-300 hover:shadow-md transition">
                <div class="w-12 h-12 rounded-xl bg-blue-600 text-white flex items-center justify-center font-black text-lg mx-auto mb-3 shadow-md group-hover:scale-105 transition">
                    1
                </div>
                <h3 class="font-bold text-slate-800 text-sm mb-1">Pilih Mobil</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Eksplorasi armada, pilih tanggal sewa, dan tentukan opsi dengan Driver atau Lepas Kunci.
                </p>
            </div>

            <!-- Step 2 -->
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 text-center relative group hover:border-blue-300 hover:shadow-md transition">
                <div class="w-12 h-12 rounded-xl bg-blue-600 text-white flex items-center justify-center font-black text-lg mx-auto mb-3 shadow-md group-hover:scale-105 transition">
                    2
                </div>
                <h3 class="font-bold text-slate-800 text-sm mb-1">Alamat Pengantaran</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Input alamat lengkap pengiriman. Sistem otomatis mengkalkulasi total biaya sewa.
                </p>
            </div>

            <!-- Step 3 -->
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 text-center relative group hover:border-blue-300 hover:shadow-md transition">
                <div class="w-12 h-12 rounded-xl bg-blue-600 text-white flex items-center justify-center font-black text-lg mx-auto mb-3 shadow-md group-hover:scale-105 transition">
                    3
                </div>
                <h3 class="font-bold text-slate-800 text-sm mb-1">Verifikasi Admin</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Admin memverifikasi jadwal & menyetujui pesanan (Status: Siap Dikirim / Approved).
                </p>
            </div>

            <!-- Step 4 -->
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 text-center relative group hover:border-blue-300 hover:shadow-md transition">
                <div class="w-12 h-12 rounded-xl bg-blue-600 text-white flex items-center justify-center font-black text-lg mx-auto mb-3 shadow-md group-hover:scale-105 transition">
                    4
                </div>
                <h3 class="font-bold text-slate-800 text-sm mb-1">Unit Diantar</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Driver mengantarkan unit ke alamat Anda. Tracking status live dari "On Delivery" ke "In Use".
                </p>
            </div>

            <!-- Step 5 -->
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 text-center relative group hover:border-blue-300 hover:shadow-md transition">
                <div class="w-12 h-12 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-black text-lg mx-auto mb-3 shadow-md group-hover:scale-105 transition">
                    5
                </div>
                <h3 class="font-bold text-slate-800 text-sm mb-1">Pengembalian</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Pengecekan fisik unit & tepat waktu. Jika aman tanpa denda, sewa selesai & mobil kembali tersedia.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Section Filter & Katalog Mobil -->
<section id="katalog" class="py-16 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-blue-600 bg-blue-100 px-3 py-1 rounded-full">
                    Pilihan Kendaraan
                </span>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900 mt-2">Katalog Armada Rental</h2>
                <p class="text-slate-500 text-sm mt-1">Temukan kendaraan yang cocok untuk kebutuhan mobilitas Anda</p>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200 mb-8">
            <form action="{{ route('home') }}#katalog" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Search -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Cari Merek / Model</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                            <i class="fa-solid fa-magnifying-glass text-xs"></i>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Contoh: Avanza, Fortuner..."
                               class="w-full pl-8 pr-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <!-- Kapasitas -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Kapasitas Kursi</label>
                    <select name="capacity" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Kapasitas</option>
                        <option value="5" {{ request('capacity') == '5' ? 'selected' : '' }}>Min. 5 Penumpang</option>
                        <option value="7" {{ request('capacity') == '7' ? 'selected' : '' }}>Min. 7 Penumpang</option>
                    </select>
                </div>

                <!-- Harga Maksimal -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Budget Maksimal / Hari</label>
                    <select name="max_price" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Harga</option>
                        <option value="500000" {{ request('max_price') == '500000' ? 'selected' : '' }}>s/d Rp 500.000</option>
                        <option value="800000" {{ request('max_price') == '800000' ? 'selected' : '' }}>s/d Rp 800.000</option>
                        <option value="1200000" {{ request('max_price') == '1200000' ? 'selected' : '' }}>s/d Rp 1.200.000</option>
                    </select>
                </div>

                <!-- Status Ketersediaan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Status Ketersediaan</label>
                    <select name="status" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Status</option>
                        <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Tersedia (Ready)</option>
                        <option value="rented" {{ request('status') == 'rented' ? 'selected' : '' }}>Sedang Disewa</option>
                    </select>
                </div>

                <!-- Buttons -->
                <div class="flex items-end space-x-2">
                    <button type="submit" class="flex-1 py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs transition shadow-sm flex items-center justify-center space-x-1">
                        <i class="fa-solid fa-filter text-xs"></i>
                        <span>Filter</span>
                    </button>
                    <a href="{{ route('home') }}#katalog" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs transition flex items-center justify-center">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                </div>
            </form>
        </div>

        <!-- Car Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($cars as $car)
                <div class="bg-white rounded-2xl overflow-hidden border border-slate-200 shadow-sm hover:shadow-xl transition duration-300 flex flex-col group">
                    <!-- Image Box -->
                    <div class="relative h-52 bg-slate-100 overflow-hidden">
                        <img src="{{ $car->image_url }}"
                             alt="{{ $car->brand }} {{ $car->model }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition duration-500">

                        <!-- Status Badge -->
                        <div class="absolute top-3 right-3">
                            @if($car->status === 'available')
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-emerald-500 text-white shadow-sm flex items-center space-x-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white animate-ping"></span>
                                    <span>Tersedia</span>
                                </span>
                            @elseif($car->status === 'rented')
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-amber-500 text-white shadow-sm flex items-center space-x-1">
                                    <i class="fa-solid fa-clock text-[10px]"></i>
                                    <span>Sedang Disewa</span>
                                </span>
                            @else
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-slate-500 text-white shadow-sm">
                                    Maintenance
                                </span>
                            @endif
                        </div>

                        <!-- Brand Badge -->
                        <div class="absolute bottom-3 left-3 bg-slate-900/80 backdrop-blur-sm text-white px-2.5 py-0.5 rounded-lg text-xs font-semibold">
                            {{ $car->brand }}
                        </div>
                    </div>

                    <!-- Details Content -->
                    <div class="p-5 flex-1 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between text-xs text-slate-400 mb-1">
                                <span class="font-mono bg-slate-100 px-2 py-0.5 rounded text-slate-600 font-semibold">{{ $car->license_plate }}</span>
                                <span class="flex items-center space-x-1">
                                    <i class="fa-solid fa-user-group text-slate-400"></i>
                                    <span>{{ $car->capacity }} Penumpang</span>
                                </span>
                            </div>

                            <h3 class="text-lg font-bold text-slate-900 group-hover:text-blue-600 transition">
                                {{ $car->brand }} {{ $car->model }}
                            </h3>

                            <p class="text-xs text-slate-500 mt-2 line-clamp-2 leading-relaxed">
                                {{ $car->description }}
                            </p>
                        </div>

                        <!-- Price & Action -->
                        <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between">
                            <div>
                                <span class="text-[11px] text-slate-400 block font-medium">Tarif Sewa:</span>
                                <span class="text-lg font-black text-blue-600">
                                    Rp {{ number_format($car->price_per_day, 0, ',', '.') }}
                                    <span class="text-xs text-slate-400 font-normal">/hari</span>
                                </span>
                            </div>

                            <div class="flex items-center space-x-2">
                                <a href="{{ route('cars.show', $car) }}"
                                   class="p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl transition text-xs font-semibold"
                                   title="Lihat Detail">
                                    <i class="fa-solid fa-eye"></i>
                                </a>

                                @if($car->status !== 'maintenance')
                                    <a href="{{ route('bookings.create', $car) }}"
                                       class="px-3.5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-md hover:shadow-lg transition flex items-center space-x-1.5">
                                        <span>Sewa</span>
                                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                    </a>
                                @else
                                    <button disabled class="px-3 py-2 bg-slate-200 text-slate-400 rounded-xl text-xs font-medium cursor-not-allowed">
                                        Dalam Perbaikan
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center bg-white rounded-2xl border border-slate-200">
                    <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                        <i class="fa-solid fa-car-tunnel text-2xl"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-700">Tidak ada mobil yang sesuai dengan filter</h3>
                    <p class="text-xs text-slate-400 mt-1">Coba ubah kata kunci pencarian atau reset filter untuk melihat semua armada.</p>
                    <a href="{{ route('home') }}#katalog" class="inline-block mt-4 px-4 py-2 bg-blue-600 text-white rounded-xl text-xs font-semibold">
                        Reset Filter
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $cars->links() }}
        </div>
    </div>
</section>
@endsection
