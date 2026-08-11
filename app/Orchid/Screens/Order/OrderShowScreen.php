<?php

namespace App\Orchid\Screens\Order;

use App\Models\Order;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Label;
use Orchid\Screen\Layouts\Rows;
use Orchid\Screen\Screen;
use Orchid\Screen\Sight;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Color;

class OrderShowScreen extends Screen
{
    public $order;

    public function query(int $id): iterable
    {
        $this->order = Order::findOrFail($id);

        return [
            'order' => $this->order,
        ];
    }

    public function name(): ?string
    {
        return 'Заказ #' . optional($this->order)->id;
    }

    public function description(): ?string
    {
        return 'Подробная информация по заказу и доставке';
    }

    public function commandBar(): iterable
    {
        return [
            Button::make('Назад к заказам')
                ->route('platform.orders')
                ->type(Color::LIGHT()),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::rows([
                Label::make('order.id')->title('ID заказа')->value(optional($this->order)->id),
                Label::make('order.name')->title('Клиент')->value(optional($this->order)->name),
                Label::make('order.email')->title('Email')->value(optional($this->order)->email ?? '—'),
                Label::make('order.phone')->title('Телефон')->value(optional($this->order)->phone),
                Label::make('order.amount')->title('Сумма')->value(optional($this->order)->amount ? number_format((float) optional($this->order)->amount, 0, ',', ' ') . ' ₽' : '—'),
                Label::make('order.created_at')->title('Дата создания')->value(optional($this->order)->created_at ? optional($this->order)->created_at->format('d.m.Y H:i') : '—'),
            ]),

            Layout::rows([
                Label::make('order.delivery')->title('Способ доставки')->value(optional($this->order)->delivery ?: 'Самовывоз'),
                Label::make('order.delivery_type')->title('Тип доставки')->value(optional($this->order)->delivery_type ?: '—'),
                Label::make('order.delivery_price')->title('Цена доставки')->value(optional($this->order)->delivery_price ? number_format((float) optional($this->order)->delivery_price, 0, ',', ' ') . ' ₽' : '—'),
                Label::make('order.delivery_date_range')->title('Диапазон дат')->value(optional($this->order)->delivery_date_range ?: '—'),
            ]),

            Layout::view('platform.orders.delivery', [
                'delivery_info' => $this->renderDeliveryInfo(),
            ]),


        ];
    }

    protected function renderDeliveryInfo(): string
    {
        if (empty(optional($this->order)->delivery_info)) {
            return '—';
        }

        $items = [];
        foreach ((array) optional($this->order)->delivery_info as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }

            $items[] = '<strong>' . e($key) . ':</strong> ' . e((string) $value);
        }

        return implode('<br>', $items);
    }
}
