<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama customer wajib diisi.',
            'name.max' => 'Nama customer maksimal 255 karakter.',
            'email.email' => 'Format email tidak valid.',
        ];
    }
}
