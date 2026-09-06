<?php

namespace App\Orchid\Screens\Order;

use App\Models\ShopOrder;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Screen\Sight;
use Orchid\Screen\TD;
use Orchid\Support\Color;
use Orchid\Support\Facades\Layout;

class OrderShowScreen extends Screen
{
    public $order;

    public function query(int $id): iterable
    {
        $this->order = ShopOrder::with(['delivery', 'items'])->findOrFail($id);

        return [
            'order' => $this->order,
            'delivery' => $this->order->delivery,
            'items' => $this->order->items,
        ];
    }

    public function name(): ?string
    {
        return 'Заказ #'.optional($this->order)->id;
    }

    public function description(): ?string
    {
        return 'Подробная информация по заказу и доставке';
    }

    public function commandBar(): iterable
    {
        return [
            Link::make('Открыть продукт')
                ->href('https://yookassa.ru/my/orders/'.$this->order->payment_id)
                ->icon('bs.box-arrow-up-right')
                ->target('_blank')
                ->canSee($this->order->payment_id !== null),

            Button::make('Назад к заказам')
                ->route('platform.orders')
                ->type(Color::LIGHT()),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::legend('order', [
                Sight::make('id', 'ID заказа'),
                Sight::make('created_at', 'Дата создания')->render(function ($order) {
                    return $order->created_at ? $order->created_at->format('d.m.Y H:i') : '—';
                }),
                Sight::make('user_id', 'Пользователь ID'),
                Sight::make('name', 'Клиент'),
                Sight::make('email', 'Email'),
                Sight::make('phone', 'Телефон'),
                Sight::make('comment', 'Комментарий')->render(function ($order) {
                    return $order->comment ?: '—';
                }),
                Sight::make('promo_code', 'Промокод')->render(function ($order) {
                    return $order->promo_code ?: '—';
                }),
            ])->title('Информация о заказе'),

            Layout::split([
                Layout::legend('order', [
                    Sight::make('cart_summ', 'Сумма товаров')->render(function ($order) {
                        return number_format((float) $order->cart_summ, 2, ',', ' ').' ₽';
                    }),
                    Sight::make('discount_summ', 'Скидка')->render(function ($order) {
                        return number_format((float) $order->discount_summ, 2, ',', ' ').' ₽';
                    }),
                    Sight::make('total_summ', 'Итого')->render(function ($order) {
                        return number_format((float) $order->total_summ, 2, ',', ' ').' ₽';
                    }),
                ])->title('Суммы'),

                Layout::legend('order', [
                    Sight::make('payment_id', 'ID платежа')->render(function ($order) {
                        return $order->payment_id ?: '—';
                    }),
                    Sight::make('payment_status', 'Статус оплаты')->render(function ($order) {
                        return $order->payment_status ?: '—';
                    }),
                    Sight::make('payment_status_text', 'Статус (текст)')->render(function ($order) {
                        return $order->payment_status_text ?: '—';
                    }),
                    Sight::make('session_id', 'Сессия')->render(function ($order) {
                        return $order->session_id ?: '—';
                    }),
                ])->title('Платежная информация'),
            ]),

            Layout::view('platform.orders.delivery', [
                'delivery' => $this->order->delivery,
            ]),

            Layout::table('items', [
                TD::make('id', '#')->width('5%'),
                TD::make('product_sku', 'Артикул')->width('12%'),
                TD::make('product_name', 'Название товара')->width('25%'),
                TD::make('price', 'Цена')->width('10%')->render(function ($item) {
                    return number_format((float) $item->price, 2, ',', ' ').' ₽';
                }),
                TD::make('quantity', 'Кол-во')->width('8%'),
                TD::make('sum', 'Сумма')->width('12%')->render(function ($item) {
                    return number_format((float) $item->price * (int) $item->quantity, 2, ',', ' ').' ₽';
                }),
                TD::make('weight_grams', 'Вес (гр)')->width('8%')->render(function ($item) {
                    return $item->weight_grams !== null ? (int) $item->weight_grams : '—';
                }),
                TD::make('dimensions', 'Габариты (см)')->width('15%')->render(function ($item) {
                    $d = $item->dimensions;
                    if (! $d) {
                        return '—';
                    }

                    $parts = array_filter([
                        $d['length'] ?? null,
                        $d['width'] ?? null,
                        $d['height'] ?? null,
                    ], fn ($v) => $v !== null && $v !== '');

                    return $parts ? implode(' × ', $parts) : '—';
                }),
            ])->title('Товары в заказе'),
        ];
    }
}
