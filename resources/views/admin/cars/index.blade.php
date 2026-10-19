@extends('layouts.app')

@section('title', 'Manajemen Armada Mobil - Admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('admin.dashboard') }}" class="text-xs text-slate-400 hover:text-blue-600">Admin</a>
                <span class="text-xs text-slate-300">/</span>
                <span class="text-xs font-semibold text-slate-600">Armada Mobil</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight mt-1">Manajemen Mobil Rental</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Kelola data kendaraan, status operasional, kapasitas, dan tarif sewa harian.</p>
        </div>

        <a href="{{ route('admin.cars.create') }}" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-sm flex items-center space-x-2">
            <i class="fa-solid fa-plus"></i>
            <span>Tambah Mobil Baru</span>
        </a>
    </div>

    <!-- Filter & Search -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm mb-6">
        <form action="{{ route('admin.cars.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari berdasarkan merek, model, atau nomor plat..."
                       class="w-full pl-8 pr-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <select name="status" class="px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Status</option>
                <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Tersedia (Available)</option>
                <option value="rented" {{ request('status') == 'rented' ? 'selected' : '' }}>Sedang Disewa (Rented)</option>
                <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition">
                Filter
            </button>

            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('admin.cars.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold flex items-center justify-center">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Cars Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-100 text-slate-500 uppercase tracking-wider font-bold">
                        <th class="py-3 px-4">Kendaraan</th>
                        <th class="py-3 px-4">Plat Nomor</th>
                        <th class="py-3 px-4">Kapasitas</th>
                        <th class="py-3 px-4">Harga Sewa / Hari</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($cars as $car)
                        <tr class="hover:bg-slate-50/60 transition">
                            <!-- Kendaraan & Foto -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center space-x-3">
                                    <img src="{{ $car->image_url }}"
                                         alt="{{ $car->brand }}"
                                         class="w-16 h-12 rounded-xl object-cover border border-slate-200">
                                    <div>
                                        <div class="font-bold text-slate-900 text-sm">{{ $car->brand }} {{ $car->model }}</div>
                                        <div class="text-[11px] text-slate-400 line-clamp-1 max-w-xs">{{ $car->description }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Plat Nomor -->
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-800">
                                {{ $car->license_plate }}
                            </td>

                            <!-- Kapasitas -->
                            <td class="py-3.5 px-4 text-slate-600">
                                <span class="flex items-center space-x-1">
                                    <i class="fa-solid fa-users text-slate-400"></i>
                                    <span>{{ $car->capacity }} Kursi</span>
                                </span>
                            </td>

                            <!-- Tarif Sewa -->
                            <td class="py-3.5 px-4 font-black text-blue-600 text-sm">
                                Rp {{ number_format($car->price_per_day, 0, ',', '.') }}
                            </td>

                            <!-- Status Badge -->
                            <td class="py-3.5 px-4">
                                @if($car->status === 'available')
                                    <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300">
                                        Tersedia
                                    </span>
                                @elseif($car->status === 'rented')
                                    <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-blue-100 text-blue-800 border border-blue-300">
                                        Disewa
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-slate-100 text-slate-800 border border-slate-300">
                                        Maintenance
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('cars.show', $car) }}" target="_blank"
                                       class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg" title="Lihat di Halaman Customer">
                                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                    </a>

                                    <a href="{{ route('admin.cars.edit', $car) }}"
                                       class="p-2 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg font-bold" title="Edit Mobil">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    <form action="{{ route('admin.cars.destroy', $car) }}" method="POST"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus armada mobil ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg font-bold" title="Hapus Mobil">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">
                                Belum ada data mobil yang sesuai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $cars->links() }}
        </div>
    </div>
</div>
@endsection
