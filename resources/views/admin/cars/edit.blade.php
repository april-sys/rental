@extends('layouts.app')

@section('title', 'Edit Data Mobil - Admin')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-6">
        <a href="{{ route('admin.cars.index') }}" class="inline-flex items-center space-x-1.5 text-xs text-slate-500 hover:text-blue-600 mb-2">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Data Mobil</span>
        </a>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Edit Mobil: {{ $car->brand }} {{ $car->model }}</h1>
        <p class="text-xs text-slate-500 mt-0.5">Perbarui tarif sewa, plat nomor, atau status operasional unit.</p>
    </div>

    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-sm">
        <form action="{{ route('admin.cars.update', $car) }}" method="POST" enctype="multipart/form-data" class="space-y-5" x-data="{ imagePreview: null }">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="brand" class="block text-xs font-bold text-slate-700 uppercase mb-1">Merek Mobil <span class="text-red-500">*</span></label>
                    <input type="text" name="brand" id="brand" value="{{ old('brand', $car->brand) }}" required
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white">
                </div>

                <div>
                    <label for="model" class="block text-xs font-bold text-slate-700 uppercase mb-1">Model / Tipe Mobil <span class="text-red-500">*</span></label>
                    <input type="text" name="model" id="model" value="{{ old('model', $car->model) }}" required
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="license_plate" class="block text-xs font-bold text-slate-700 uppercase mb-1">Nomor Plat Polisi <span class="text-red-500">*</span></label>
                    <input type="text" name="license_plate" id="license_plate" value="{{ old('license_plate', $car->license_plate) }}" required
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white">
                </div>

                <div>
                    <label for="capacity" class="block text-xs font-bold text-slate-700 uppercase mb-1">Kapasitas Kursi <span class="text-red-500">*</span></label>
                    <input type="number" name="capacity" id="capacity" value="{{ old('capacity', $car->capacity) }}" min="1" max="50" required
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white">
                </div>

                <div>
                    <label for="status" class="block text-xs font-bold text-slate-700 uppercase mb-1">Status Ketersediaan <span class="text-red-500">*</span></label>
                    <select name="status" id="status" required
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white">
                        <option value="available" {{ old('status', $car->status) == 'available' ? 'selected' : '' }}>Tersedia (Available)</option>
                        <option value="rented" {{ old('status', $car->status) == 'rented' ? 'selected' : '' }}>Disewa (Rented)</option>
                        <option value="maintenance" {{ old('status', $car->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="price_per_day" class="block text-xs font-bold text-slate-700 uppercase mb-1">Tarif Sewa per Hari (IDR) <span class="text-red-500">*</span></label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 font-bold text-xs">Rp</span>
                    <input type="number" name="price_per_day" id="price_per_day" value="{{ old('price_per_day', (int)$car->price_per_day) }}" step="10000" min="10000" required
                           class="w-full pl-10 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white">
                </div>
            </div>

            <!-- Upload Ganti Foto Mobil -->
            <div>
                <label for="image" class="block text-xs font-bold text-slate-700 uppercase mb-1">Ganti Foto Mobil</label>
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 p-4 border-2 border-dashed border-slate-200 rounded-2xl bg-slate-50/50 hover:bg-slate-50 transition">
                    <div class="w-32 h-24 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden flex-shrink-0 flex items-center justify-center text-slate-400 relative shadow-inner">
                        <template x-if="imagePreview">
                            <img :src="imagePreview" alt="Preview Foto Baru" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!imagePreview">
                            <img src="{{ $car->image_url }}" alt="{{ $car->full_name }}" class="w-full h-full object-cover">
                        </template>
                    </div>

                    <div class="flex-1 space-y-1.5">
                        <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/jpg,image/webp"
                               @change="const file = $event.target.files[0]; if (file) { const reader = new FileReader(); reader.onload = (e) => { imagePreview = e.target.result; }; reader.readAsDataURL(file); }"
                               class="block w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer focus:outline-none">
                        <div class="text-[11px] text-slate-500 leading-tight">
                            Pilih file baru jika ingin mengganti foto saat ini. Format: <span class="font-semibold text-slate-700">JPG, PNG, WEBP</span> (Maksimal 5MB).
                        </div>
                        <div class="text-[11px] text-slate-400">
                            Biarkan kosong jika tidak ingin mengubah foto armada yang ada.
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <label for="description" class="block text-xs font-bold text-slate-700 uppercase mb-1">Deskripsi & Kondisi Kendaraan</label>
                <textarea name="description" id="description" rows="3"
                          class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white">{{ old('description', $car->description) }}</textarea>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.cars.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-sm flex items-center space-x-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
