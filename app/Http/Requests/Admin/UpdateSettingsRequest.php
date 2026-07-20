<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'wa_number'           => ['required', 'string', 'min:10', 'max:20', 'regex:/^[0-9+\-\s]+$/'],
            'address'             => ['nullable', 'string', 'max:500'],
            'email'               => ['nullable', 'email', 'max:255'],
            'company_description' => ['nullable', 'string'],
            'operating_hours'     => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'wa_number.required' => 'Nomor WhatsApp wajib diisi.',
            'wa_number.regex'    => 'Format nomor WhatsApp tidak valid.',
            'wa_number.min'      => 'Nomor WhatsApp minimal 10 digit.',
        ];
    }
}
