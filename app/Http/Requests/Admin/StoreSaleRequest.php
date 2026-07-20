<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreSaleRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'customer_name'    => ['nullable', 'string', 'max:255'],
            'payment_method'   => ['required', 'in:cash,transfer'],
            'amount_paid'      => ['required', 'numeric', 'min:0'],
            'items'            => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.qty'      => ['required', 'numeric', 'min:0.01'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required'           => 'Minimal satu produk harus ditambahkan.',
            'items.*.product_id.required' => 'Produk tidak valid.',
            'items.*.qty.min'          => 'Jumlah minimal 0.01.',
        ];
    }
}
