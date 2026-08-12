<?php

namespace App\Http\Requests\Shop;

use Illuminate\Foundation\Http\FormRequest;

class AddToCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_sku' => ['required', 'string', 'max:150'],
            'quantity' => ['sometimes', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_sku.required' => 'Артикул товара обязателен.',
            'product_sku.max' => 'Артикул товара слишком длинный.',
            'quantity.integer' => 'Количество должно быть целым числом.',
            'quantity.min' => 'Минимальное количество — 1.',
        ];
    }
}
