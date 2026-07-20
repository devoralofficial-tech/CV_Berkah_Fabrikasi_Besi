<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreStockOutRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:products,id'],
            'qty'        => ['required', 'numeric', 'min:0.01'],
            'reason'     => ['required', 'in:Rusak,Susut,Hilang,Sample,Lainnya'],
            'note'       => ['nullable', 'string', 'max:500'],
            'created_at' => ['nullable', 'date'],
        ];
    }
}
