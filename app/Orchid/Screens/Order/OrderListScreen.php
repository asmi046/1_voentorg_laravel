<?php

namespace App\Orchid\Screens\Order;

use App\Models\ShopOrder;
use App\Orchid\Layouts\Order\OrderListLayout;
use Orchid\Screen\Screen;
use Orchid\Screen\Actions\Link;

class OrderListScreen extends Screen
{
    public function query(): array
    {
        return [
            'orders' => ShopOrder::with(['delivery'])
                ->filters()
                ->defaultSort('created_at', 'desc')
                ->paginate(20),
        ];
    }

    public function name(): ?string
    {
        return 'Заказы';
    }

    public function description(): ?string
    {
        return 'Список заказов с информацией о доставке';
    }

    public function layout(): iterable
    {
        return [
            new OrderListLayout(),
        ];
    }
}
