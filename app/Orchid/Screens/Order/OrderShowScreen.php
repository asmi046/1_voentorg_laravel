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
                TD::make('id', '#'),
                TD::make('product_sku', 'Артикул'),
                TD::make('product_name', 'Название товара'),
                TD::make('product_title', 'Заголовок'),
                TD::make('price', 'Цена')->render(function ($item) {
                    return number_format((float) $item->price, 2, ',', ' ').' ₽';
                }),
                TD::make('quantity', 'Количество'),
                TD::make('weight_grams', 'Вес (гр)'),
            ])->title('Товары в заказе'),
        ];
    }
}
