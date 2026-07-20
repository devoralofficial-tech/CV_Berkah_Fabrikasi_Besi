<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name'    => ['required', 'string', 'max:255'],
            'customer_phone'   => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s]{8,20}$/'],
            'customer_address' => ['nullable', 'string', 'max:500'],
            'payment_method'   => ['required', 'in:cash,transfer'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required'  => 'Nama wajib diisi.',
            'customer_phone.required' => 'Nomor HP wajib diisi.',
            'customer_phone.regex'    => 'Format nomor HP tidak valid.',
            'payment_method.required' => 'Metode pembayaran wajib dipilih.',
            'payment_method.in'       => 'Metode pembayaran tidak valid.',
        ];
    }
}
