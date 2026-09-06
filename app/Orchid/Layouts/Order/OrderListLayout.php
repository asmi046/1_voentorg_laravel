<?php

namespace App\Orchid\Layouts\Order;

use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class OrderListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'orders';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('id', '#')->width('5%')->sort(),
            TD::make('created_at', 'Дата')->width('10%')->render(function ($order) {
                return $order->created_at ? $order->created_at->format('d.m.Y H:i') : '—';
            })->sort(),
            TD::make('name', 'Клиент')->width('15%')->render(function ($order) {
                return $order->name.'<br><small>'.e($order->email ?? '').'</small>';
            })->filter(),
            TD::make('phone', 'Телефон')->width('10%')->filter(),
            TD::make('cart_summ', 'Сумма товаров')->width('10%')->render(function ($order) {
                return number_format((float) $order->cart_summ, 2, ',', ' ').' ₽';
            }),
            TD::make('discount_summ', 'Скидка')->width('8%')->render(function ($order) {
                return $order->discount_summ
                    ? number_format((float) $order->discount_summ, 2, ',', ' ').' ₽'
                    : '—';
            }),
            TD::make('total_summ', 'Итого')->width('10%')->render(function ($order) {
                return number_format((float) $order->total_summ, 2, ',', ' ').' ₽';
            })->sort(),
            TD::make('delivery.method', 'Способ доставки')->width('10%')->render(function ($order) {
                return $order->delivery?->method ?? 'Самовывоз';
            }),
            TD::make('payment_status', 'Статус оплаты')->width('8%')->render(function ($order) {
                return $order->payment_status_text ?: $order->payment_status ?: '—';
            })->filter(),
            TD::make(__('Actions'))
                ->align(TD::ALIGN_CENTER)
                ->width('8%')
                ->render(fn ($order) => Link::make('Подробнее')
                    ->route('platform.orders.show', $order->id)
                    ->icon('eye')),
        ];
    }
}
