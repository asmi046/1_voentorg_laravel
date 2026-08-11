<?php

namespace App\Orchid\Screens\Order;

use App\Models\Order;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Color;
use Orchid\Screen\Fields\Label;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\Sight;
use Orchid\Screen\Layouts\Component;
use Orchid\Screen\Layouts\Layout;

class OrderListScreen extends Screen
{
    public function query(): array
    {
        return [
            'orders' => Order::latest()->paginate(20),
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
            Layout::view('platform.orders.list'),
        ];
    }
}
