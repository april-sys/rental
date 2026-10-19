@extends('layouts.app')

@section('title', 'Tambah Mobil Baru - Admin')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-6">
        <a href="{{ route('admin.cars.index') }}" class="inline-flex items-center space-x-1.5 text-xs text-slate-500 hover:text-blue-600 mb-2">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Data Mobil</span>
        </a>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Tambah Mobil Baru ke Armada</h1>
        <p class="text-xs text-slate-500 mt-0.5">Input spesifikasi kendaraan, tarif sewa harian, dan status kesiapan unit.</p>
    </div>

    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-sm">
        <form action="{{ route('admin.cars.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5" x-data="{ imagePreview: null }">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="brand" class="block text-xs font-bold text-slate-700 uppercase mb-1">Merek Mobil <span class="text-red-500">*</span></label>
                    <input type="text" name="brand" id="brand" value="{{ old('brand') }}" required
                           placeholder="Contoh: Toyota, Honda, Mitsubishi"
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white">
                </div>

                <div>
                    <label for="model" class="block text-xs font-bold text-slate-700 uppercase mb-1">Model / Tipe Mobil <span class="text-red-500">*</span></label>
                    <input type="text" name="model" id="model" value="{{ old('model') }}" required
                           placeholder="Contoh: Avanza 1.5 G CVT"
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="license_plate" class="block text-xs font-bold text-slate-700 uppercase mb-1">Nomor Plat Polisi <span class="text-red-500">*</span></label>
                    <input type="text" name="license_plate" id="license_plate" value="{{ old('license_plate') }}" required
                           placeholder="Contoh: B 1234 ABC"
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white">
                </div>

                <div>
                    <label for="capacity" class="block text-xs font-bold text-slate-700 uppercase mb-1">Kapasitas Kursi <span class="text-red-500">*</span></label>
                    <input type="number" name="capacity" id="capacity" value="{{ old('capacity', 7) }}" min="1" max="50" required
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white">
                </div>

                <div>
                    <label for="status" class="block text-xs font-bold text-slate-700 uppercase mb-1">Status Ketersediaan <span class="text-red-500">*</span></label>
                    <select name="status" id="status" required
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white">
                        <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Tersedia (Available)</option>
                        <option value="rented" {{ old('status') == 'rented' ? 'selected' : '' }}>Disewa (Rented)</option>
                        <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="price_per_day" class="block text-xs font-bold text-slate-700 uppercase mb-1">Tarif Sewa per Hari (IDR) <span class="text-red-500">*</span></label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 font-bold text-xs">Rp</span>
                    <input type="number" name="price_per_day" id="price_per_day" value="{{ old('price_per_day', 400000) }}" step="10000" min="10000" required
                           class="w-full pl-10 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white">
                </div>
            </div>

            <!-- Upload File Foto Mobil -->
            <div>
                <label for="image" class="block text-xs font-bold text-slate-700 uppercase mb-1">Upload Foto Mobil</label>
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 p-4 border-2 border-dashed border-slate-200 rounded-2xl bg-slate-50/50 hover:bg-slate-50 transition">
                    <div class="w-32 h-24 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden flex-shrink-0 flex items-center justify-center text-slate-400 relative shadow-inner">
                        <template x-if="imagePreview">
                            <img :src="imagePreview" alt="Preview Foto Mobil" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!imagePreview">
                            <div class="flex flex-col items-center justify-center text-center p-2">
                                <i class="fa-solid fa-image text-2xl text-slate-300 mb-1"></i>
                                <span class="text-[10px] text-slate-400 font-medium">Foto Unit</span>
                            </div>
                        </template>
                    </div>

                    <div class="flex-1 space-y-1.5">
                        <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/jpg,image/webp"
                               @change="const file = $event.target.files[0]; if (file) { const reader = new FileReader(); reader.onload = (e) => { imagePreview = e.target.result; }; reader.readAsDataURL(file); }"
                               class="block w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer focus:outline-none">
                        <div class="text-[11px] text-slate-500 leading-tight">
                            Format didukung: <span class="font-semibold text-slate-700">JPG, PNG, WEBP</span> (Maksimal 5MB). Disimpan ke <code class="text-slate-600 bg-slate-100 px-1 py-0.5 rounded">storage/app/public/cars</code>.
                        </div>
                        <div class="text-[11px] text-blue-600 font-medium flex items-center space-x-1">
                            <i class="fa-solid fa-circle-info"></i>
                            <span>Jika dikosongkan, unit otomatis menggunakan foto default kustom AutoRent (<code class="bg-blue-50 px-1 rounded">public/images/default-car.png</code>).</span>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <label for="description" class="block text-xs font-bold text-slate-700 uppercase mb-1">Deskripsi & Kondisi Kendaraan</label>
                <textarea name="description" id="description" rows="3"
                          placeholder="Kondisi mesin prima, transmisi otomatis, interior bersih..."
                          class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white">{{ old('description') }}</textarea>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.cars.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-sm flex items-center space-x-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Mobil</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
