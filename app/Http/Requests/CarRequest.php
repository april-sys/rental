<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        $carId = $this->route('car') ? $this->route('car')->id : null;

        return [
            'brand' => ['required', 'string', 'max:100'],
            'model' => ['required', 'string', 'max:100'],
            'license_plate' => [
                'required',
                'string',
                'max:20',
                Rule::unique('cars', 'license_plate')->ignore($carId),
            ],
            'capacity' => ['required', 'integer', 'min:1', 'max:50'],
            'price_per_day' => ['required', 'numeric', 'min:10000'],
            'status' => ['required', Rule::in(['available', 'rented', 'maintenance'])],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'description' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'brand.required' => 'Merek mobil wajib diisi.',
            'model.required' => 'Model/Tipe mobil wajib diisi.',
            'license_plate.required' => 'Nomor plat polisi wajib diisi.',
            'license_plate.unique' => 'Nomor plat polisi sudah terdaftar.',
            'capacity.required' => 'Kapasitas penumpang wajib diisi.',
            'price_per_day.required' => 'Harga sewa per hari wajib diisi.',
            'price_per_day.min' => 'Harga sewa per hari minimal Rp 10.000.',
            'status.required' => 'Status ketersediaan mobil wajib dipilih.',
            'image.image' => 'File yang diunggah harus berupa gambar.',
            'image.mimes' => 'Format file gambar harus jpeg, png, jpg, atau webp.',
            'image.max' => 'Ukuran file gambar maksimal 5MB.',
        ];
    }
}
