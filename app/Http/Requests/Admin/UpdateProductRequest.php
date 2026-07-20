<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'                => ['required', 'string', 'max:255'],
            'category_id'         => ['required', 'exists:categories,id'],
            'unit'                => ['required', 'in:pcs,kg,m'],
            'sell_price'          => ['required', 'numeric', 'min:0'],
            'cost_price'          => ['nullable', 'numeric', 'min:0'],
            'low_stock_threshold' => ['required', 'numeric', 'min:0'],
            'image'               => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'description'         => ['nullable', 'string'],
        ];
    }
}
