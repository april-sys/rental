<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Rental Mobil') }} - @yield('title', 'Sistem Rental Mobil Delivery')</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <style>
        [x-cloak] { display: none !important; }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-800 flex flex-col min-h-screen font-sans antialiased">

    <!-- Top Navigation -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-50 shadow-sm" x-data="{ mobileMenuOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Brand Logo -->
                <div class="flex items-center space-x-3">
                    <a href="{{ route('home') }}" class="flex items-center space-x-2 text-brand-600 font-extrabold text-xl tracking-tight">
                        <span class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-md">
                            <i class="fa-solid fa-car-side text-lg"></i>
                        </span>
                        <span class="text-slate-900">Auto<span class="text-blue-600">Rent</span></span>
                    </a>
                    <span class="hidden md:inline-block text-xs font-semibold px-2 py-0.5 bg-blue-50 text-blue-700 rounded-full border border-blue-200">
                        Delivery Service
                    </span>
                </div>

                <!-- Navigation Links -->
                <nav class="hidden md:flex items-center space-x-6 text-sm font-medium">
                    

                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="text-slate-700 font-semibold hover:text-blue-600 transition flex items-center space-x-1.5 px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-blue-50">
                                <i class="fa-solid fa-gauge-high text-blue-600"></i>
                                <span>Panel Admin</span>
                            </a>
                        @else
                            <a href="{{ route('customer.bookings') }}" class="text-slate-700 hover:text-blue-600 transition flex items-center space-x-1.5">
                                <i class="fa-solid fa-receipt text-xs"></i>
                                <span>Pesanan Saya</span>
                            </a>
                        @endif
                    @endauth
                </nav>

                <!-- Auth Buttons / User Dropdown -->
                <div class="hidden md:flex items-center space-x-3">
                    @auth
                        <div class="relative" x-data="{ userMenuOpen: false }">
                            <button @click="userMenuOpen = !userMenuOpen" class="flex items-center space-x-2 px-3 py-1.5 rounded-full bg-slate-100 hover:bg-slate-200 transition text-sm">
                                <div class="w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-bold uppercase">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                                <span class="font-medium text-slate-800">{{ auth()->user()->name }}</span>
                                <span class="text-xs px-2 py-0.5 rounded {{ auth()->user()->isAdmin() ? 'bg-purple-100 text-purple-700 font-bold' : 'bg-emerald-100 text-emerald-700 font-medium' }}">
                                    {{ ucfirst(auth()->user()->role) }}
                                </span>
                                <i class="fa-solid fa-chevron-down text-xs text-slate-400"></i>
                            </button>

                            <div x-show="userMenuOpen" @click.outside="userMenuOpen = false" x-cloak class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-slate-100 py-1.5 z-50">
                                <div class="px-4 py-2 border-b border-slate-100 text-xs text-slate-500">
                                    Masuk sebagai <strong class="text-slate-800">{{ auth()->user()->email }}</strong>
                                </div>
                                @if(auth()->user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-600">
                                        <i class="fa-solid fa-gauge-high mr-2 text-blue-500"></i> Dashboard Admin
                                    </a>
                                    <a href="{{ route('admin.cars.index') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-600">
                                        <i class="fa-solid fa-car mr-2 text-blue-500"></i> Kelola Mobil
                                    </a>
                                    <a href="{{ route('admin.bookings.index') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-600">
                                        <i class="fa-solid fa-clipboard-list mr-2 text-blue-500"></i> Kelola Transaksi
                                    </a>
                                @else
                                    <a href="{{ route('customer.bookings') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-600">
                                        <i class="fa-solid fa-calendar-check mr-2 text-blue-500"></i> Pesanan & Tracking Saya
                                    </a>
                                @endif
                                <div class="border-t border-slate-100 my-1"></div>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center">
                                        <i class="fa-solid fa-right-from-bracket mr-2"></i> Keluar (Logout)
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-700 hover:text-blue-600 px-3 py-2">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-xl shadow-sm transition">
                            Daftar Sekarang
                        </a>
                    @endauth
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-slate-600 p-2 rounded-lg hover:bg-slate-100">
                        <i class="fa-solid fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Dropdown -->
        <div x-show="mobileMenuOpen" x-cloak class="md:hidden border-t border-slate-100 bg-white px-4 pt-2 pb-4 space-y-2">
            <a href="{{ route('home') }}" class="block py-2 text-slate-700 font-medium hover:text-blue-600">Beranda & Armada</a>
            <a href="{{ route('home') }}#workflow" class="block py-2 text-slate-700 font-medium hover:text-blue-600">Alur Sewa</a>
            @auth
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="block py-2 text-blue-600 font-semibold">Dashboard Admin</a>
                    <a href="{{ route('admin.cars.index') }}" class="block py-2 text-slate-700">Kelola Mobil</a>
                    <a href="{{ route('admin.bookings.index') }}" class="block py-2 text-slate-700">Kelola Transaksi</a>
                @else
                    <a href="{{ route('customer.bookings') }}" class="block py-2 text-slate-700 font-medium">Pesanan Saya</a>
                @endif
                <form action="{{ route('logout') }}" method="POST" class="pt-2">
                    @csrf
                    <button type="submit" class="w-full text-left py-2 text-red-600 font-medium">Keluar</button>
                </form>
            @else
                <div class="pt-2 flex flex-col space-y-2">
                    <a href="{{ route('login') }}" class="block text-center py-2 border border-slate-200 rounded-lg text-slate-700 font-semibold">Masuk</a>
                    <a href="{{ route('register') }}" class="block text-center py-2 bg-blue-600 text-white rounded-lg font-semibold">Daftar Akun</a>
                </div>
            @endauth
        </div>
    </header>

    <!-- Flash Notifications -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
        @if(session('success'))
            <div class="mb-4 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-start space-x-3 shadow-sm">
                <i class="fa-solid fa-circle-check text-emerald-600 text-lg mt-0.5"></i>
                <div class="flex-1 font-medium text-sm">{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 flex items-start space-x-3 shadow-sm">
                <i class="fa-solid fa-circle-exclamation text-red-600 text-lg mt-0.5"></i>
                <div class="flex-1 font-medium text-sm">{{ session('error') }}</div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 shadow-sm">
                <div class="flex items-center space-x-2 font-semibold text-sm mb-1 text-amber-800">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>Terdapat beberapa kesalahan:</span>
                </div>
                <ul class="list-disc list-inside text-xs space-y-1 ml-4 text-amber-700">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 text-sm mt-16 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Col 1 -->
                <div class="space-y-3">
                    <div class="flex items-center space-x-2 text-white font-extrabold text-lg">
                        <span class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center">
                            <i class="fa-solid fa-car-side text-sm"></i>
                        </span>
                        <span>AutoRent Indonesia</span>
                    </div>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Layanan rental mobil prima dengan kemudahan pengantaran langsung ke pintu alamat Anda (Door-to-Door Delivery). Pilihan armada lengkap dengan driver profesional atau lepas kunci.
                    </p>
                </div>

                <!-- Col 2 -->
                <div>
                    <h4 class="text-white font-semibold text-sm mb-3">Layanan Kami</h4>
                    <ul class="space-y-2 text-xs">
                        <li><a href="{{ route('home') }}" class="hover:text-white transition">Rental Mobil Lepas Kunci</a></li>
                        <li><a href="{{ route('home') }}" class="hover:text-white transition">Rental Mobil dengan Driver</a></li>
                        <li><a href="{{ route('home') }}" class="hover:text-white transition">Pengantaran Unit ke Rumah</a></li>
                        <li><a href="{{ route('home') }}" class="hover:text-white transition">Sewa Harian, Mingguan & Bulanan</a></li>
                    </ul>
                </div>

                <!-- Col 3 -->
                <div>
                    <h4 class="text-white font-semibold text-sm mb-3">Ketentuan Sewa</h4>
                    <ul class="space-y-2 text-xs">
                        <li>Wajib KTP/NIK Asli</li>
                        <li>Cek Fisik Sebelum Serah Terima</li>
                        <li>Bebas Denda Jika Tepat Waktu</li>
                        <li>Bahan Bakar Sesuai Indikator Awal</li>
                    </ul>
                </div>

                <!-- Col 4 -->
                <div>
                    <h4 class="text-white font-semibold text-sm mb-3">Hubungi Kami</h4>
                    <p class="text-xs leading-relaxed mb-2">
                        <i class="fa-solid fa-location-dot text-blue-500 mr-1.5"></i> Jl. Merdeka No. 1, Jakarta Pusat
                    </p>
                    <p class="text-xs leading-relaxed mb-2">
                        <i class="fa-solid fa-phone text-blue-500 mr-1.5"></i> +62 812-3456-7890
                    </p>
                    <p class="text-xs leading-relaxed">
                        <i class="fa-solid fa-clock text-blue-500 mr-1.5"></i> Layanan Pengantaran: 06.00 - 22.00 WIB
                    </p>
                </div>
            </div>

            <div class="border-t border-slate-800 mt-8 pt-6 flex flex-col sm:flex-row justify-between items-center text-xs text-slate-500">
                <p>&copy; {{ date('Y') }} AutoRent Car Rental. Hak Cipta Dilindungi.</p>
                <p class="mt-2 sm:mt-0">Laravel 12 & MySQL Laragon Project</p>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
