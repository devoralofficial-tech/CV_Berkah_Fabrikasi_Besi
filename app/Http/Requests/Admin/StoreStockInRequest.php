<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreStockInRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'product_id'   => ['required', 'exists:products,id'],
            'qty'          => ['required', 'numeric', 'min:0.01'],
            'cost_price'   => ['nullable', 'numeric', 'min:0'],
            'supplier'     => ['nullable', 'string', 'max:255'],
            'note'         => ['nullable', 'string', 'max:500'],
            'created_at'   => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Produk wajib dipilih.',
            'qty.required'        => 'Jumlah wajib diisi.',
            'qty.min'             => 'Jumlah minimal 0.01.',
        ];
    }
}
