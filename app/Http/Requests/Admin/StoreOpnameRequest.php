<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreOpnameRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'opname_date'                => ['required', 'date'],
            'note'                       => ['nullable', 'string', 'max:500'],
            'items'                      => ['required', 'array', 'min:1'],
            'items.*.product_id'         => ['required', 'exists:products,id'],
            'items.*.physical_stock'     => ['required', 'numeric', 'min:0'],
        ];
    }
}
