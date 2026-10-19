<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'car_id' => ['required', 'exists:cars,id'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'driver_service' => ['nullable', 'boolean'],
            'delivery_address' => ['required', 'string', 'min:10'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'car_id.required' => 'Pilihan mobil wajib ditentukan.',
            'start_date.required' => 'Tanggal mulai sewa wajib diisi.',
            'start_date.after_or_equal' => 'Tanggal mulai sewa tidak boleh sebelum hari ini.',
            'end_date.required' => 'Tanggal selesai sewa wajib diisi.',
            'end_date.after_or_equal' => 'Tanggal selesai sewa harus sama atau setelah tanggal mulai.',
            'delivery_address.required' => 'Alamat pengantaran mobil wajib diisi.',
            'delivery_address.min' => 'Alamat pengantaran minimal 10 karakter.',
        ];
    }
}
