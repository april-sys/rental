<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompleteBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'late_penalty_fee' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'late_penalty_fee.required' => 'Nominal denda wajib diisi (isi 0 jika tidak ada denda).',
            'late_penalty_fee.min' => 'Nominal denda tidak boleh negatif.',
        ];
    }
}
