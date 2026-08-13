<?php

namespace App\Http\Requests\Shop;

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
            // Данные клиента
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'comment' => ['nullable', 'string', 'max:5000'],

            // Промокод
            'promo_code' => ['nullable', 'string', 'max:50'],

            // Доставка
            'delivery' => ['required', 'array'],
            'delivery.provider' => ['nullable', 'string', 'max:50'],
            'delivery.method' => ['required', 'string', 'in:pickup,pickup_point,courier'],
            'delivery.price' => ['nullable', 'numeric', 'min:0'],
            'delivery.tariff' => ['nullable', 'string', 'max:500'],
            'delivery.delivery_date_range' => ['nullable', 'array'],
            'delivery.city' => ['nullable', 'string', 'max:255'],
            'delivery.pickup_point_id' => ['nullable', 'string', 'max:100'],
            'delivery.pickup_point_address' => ['nullable', 'string', 'max:500'],
            'delivery.delivery_address' => ['nullable', 'string', 'max:500'],
            'delivery.apartment' => ['nullable', 'string', 'max:20'],
            'delivery.raw_data' => ['nullable', 'array'],

            // Позиции заказа
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_sku' => ['required', 'string', 'max:150'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Поле «ФИО» обязательно для заполнения.',
            'phone.required' => 'Поле «Телефон» обязательно для заполнения.',
            'email.email' => 'Укажите корректный email.',
            'delivery.required' => 'Выберите способ доставки.',
            'delivery.method.required' => 'Способ доставки обязателен.',
            'delivery.method.in' => 'Некорректный способ доставки.',
            'items.required' => 'Корзина пуста.',
            'items.min' => 'Корзина пуста.',
            'items.*.product_sku.required' => 'Артикул товара обязателен.',
            'items.*.quantity.required' => 'Количество товара обязательно.',
            'items.*.quantity.min' => 'Минимальное количество товара — 1.',
        ];
    }
}
