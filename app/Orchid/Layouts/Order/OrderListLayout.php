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
            TD::make('id', '#')->width('6%'),
            TD::make('name', 'Клиент')->width('20%')->render(function ($order) {
                return $order->name . '<br><small>' . e($order->email) . '</small>';
            }),
            TD::make('phone', 'Телефон')->width('12%'),
            TD::make('amount', 'Сумма')->width('10%')->render(function ($order) {
                return number_format((float) $order->amount, 0, ',', ' ') . ' ₽';
            }),
            TD::make('delivery', 'Доставка')->width('15%')->render(function ($order) {
                return $order->delivery ?: 'Самовывоз';
            }),
            TD::make('delivery_price', 'Цена доставки')->width('12%')->render(function ($order) {
                return $order->delivery_price ? number_format((float) $order->delivery_price, 0, ',', ' ') . ' ₽' : '—';
            }),
            TD::make('created_at', 'Дата')->width('12%')->render(function ($order) {
                return $order->created_at ? $order->created_at->format('d.m.Y H:i') : '—';
            }),
            TD::make(__('Actions'))
                ->align(TD::ALIGN_CENTER)
                ->width('8%')
                ->render(fn ($order) => Link::make('Подробнее')
                    ->route('platform.orders.show', $order->id)
                    ->icon('eye')),
        ];
    }
}
