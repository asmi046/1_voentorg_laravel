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
                return $order->name . '<br><small>' . e($order->email ?? '') . '</small>';
            })->filter(),
            TD::make('phone', 'Телефон')->width('10%')->filter(),
            TD::make('total_summ', 'Сумма')->width('10%')->render(function ($order) {
                return number_format((float) $order->total_summ, 2, ',', ' ') . ' ₽';
            })->sort(),
            TD::make('delivery.method', 'Доставка')->width('10%')->render(function ($order) {
                return $order->delivery->method ?? 'Самовывоз';
            }),
            TD::make('delivery.price', 'Доставка')->width('8%')->render(function ($order) {
                return $order->delivery && $order->delivery->price ? number_format((float) $order->delivery->price, 2, ',', ' ') . ' ₽' : '—';
            }),
            TD::make('payment_status', 'Статус')->width('8%')->render(function ($order) {
                return $order->payment_status ?: '—';
            })->filter(),
            TD::make(__('Actions'))
                ->align(TD::ALIGN_CENTER)
                ->width('9%')
                ->render(fn ($order) => Link::make('Подробнее')
                    ->route('platform.orders.show', $order->id)
                    ->icon('eye')),
        ];
    }
}
