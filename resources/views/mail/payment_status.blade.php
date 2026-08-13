<h1>Статус оплаты заказа</h1>

<p><strong>Заказ №:</strong> {{ $orderId }}</p>
<p><strong>ID платежа:</strong> {{ $paymentId }}</p>
<p><strong>Статус заказа:</strong> {{ $orderStatus }}</p>
<p><strong>Сумма заказа:</strong> {{ $amount }} ₽</p>

@if($shopOrder)
    <h2>Данные клиента</h2>
    <p><strong>Имя:</strong> {{ $shopOrder->name ?? '—' }}</p>
    <p><strong>Email:</strong> {{ $shopOrder->email ?? '—' }}</p>
    <p><strong>Телефон:</strong> {{ $shopOrder->phone ?? '—' }}</p>
    <p><strong>Комментарий:</strong> {{ $shopOrder->comment ?: '—' }}</p>
    <p><strong>Промокод:</strong> {{ $shopOrder->promo_code ?: '—' }}</p>

    <h2>Суммы</h2>
    <p><strong>Сумма товаров:</strong> {{ number_format((float) $shopOrder->cart_summ, 2, '.', ' ') }} ₽</p>
    <p><strong>Скидка:</strong> {{ number_format((float) $shopOrder->discount_summ, 2, '.', ' ') }} ₽</p>
    <p><strong>Итого:</strong> {{ number_format((float) $shopOrder->total_summ, 2, '.', ' ') }} ₽</p>

    @if($shopOrder->delivery)
        <h2>Доставка</h2>
        <p><strong>Провайдер:</strong> {{ $shopOrder->delivery->provider ?? '—' }}</p>
        <p><strong>Способ:</strong> {{ $shopOrder->delivery->method ?? '—' }}</p>
        <p><strong>Стоимость:</strong> {{ number_format((float) $shopOrder->delivery->price, 2, '.', ' ') }} ₽</p>
        <p><strong>Город:</strong> {{ $shopOrder->delivery->city ?? '—' }}</p>
        <p><strong>Адрес доставки:</strong> {{ $shopOrder->delivery->delivery_address ?? '—' }}</p>
        <p><strong>Квартира:</strong> {{ $shopOrder->delivery->apartment ?? '—' }}</p>
        @if($shopOrder->delivery->pickup_point_address)
            <p><strong>Пункт выдачи:</strong> {{ $shopOrder->delivery->pickup_point_address }}</p>
        @endif
        @if($shopOrder->delivery->delivery_date_range)
            <p><strong>Дата доставки:</strong> {{ $shopOrder->delivery->delivery_date_range['start'] ?? '' }}
                @if(isset($shopOrder->delivery->delivery_date_range['end']))
                    — {{ $shopOrder->delivery->delivery_date_range['end'] }}
                @endif
            </p>
        @endif
    @endif

    @if($shopOrder->items->count() > 0)
        <h2>Товары в заказе</h2>
        <table style="width:100%; border-collapse: collapse; margin-top: 10px;">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    <th style="padding: 8px; border: 1px solid #ddd; text-align: left;">Артикул</th>
                    <th style="padding: 8px; border: 1px solid #ddd; text-align: left;">Название</th>
                    <th style="padding: 8px; border: 1px solid #ddd; text-align: right;">Цена</th>
                    <th style="padding: 8px; border: 1px solid #ddd; text-align: center;">Кол-во</th>
                    <th style="padding: 8px; border: 1px solid #ddd; text-align: right;">Сумма</th>
                </tr>
            </thead>
            <tbody>
                @foreach($shopOrder->items as $item)
                    <tr>
                        <td style="padding: 8px; border: 1px solid #ddd;">{{ $item->product_sku }}</td>
                        <td style="padding: 8px; border: 1px solid #ddd;">{{ $item->product_name ?? $item->product_title ?? '—' }}</td>
                        <td style="padding: 8px; border: 1px solid #ddd; text-align: right;">{{ number_format((float) $item->price, 2, '.', ' ') }} ₽</td>
                        <td style="padding: 8px; border: 1px solid #ddd; text-align: center;">{{ $item->quantity }}</td>
                        <td style="padding: 8px; border: 1px solid #ddd; text-align: right;">{{ number_format((float) ($item->price * $item->quantity), 2, '.', ' ') }} ₽</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endif