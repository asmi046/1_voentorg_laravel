<?php

namespace App\Http\Requests\Shop;

use Illuminate\Foundation\Http\FormRequest;

class DeleteCartItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_sku' => ['required', 'string', 'max:150'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_sku.required' => 'Артикул товара обязателен.',
        ];
    }
}
