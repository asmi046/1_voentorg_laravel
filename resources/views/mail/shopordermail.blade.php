<h1>Новый заказ на сайте</h1>

<p><strong>Заказ №:</strong> {{ $order->id }}</p>
<p><strong>Дата создания:</strong> {{ $order->created_at ? $order->created_at->format('d.m.Y H:i') : '—' }}</p>

<h2>Данные клиента</h2>
<p><strong>Имя:</strong> {{ $order->name ?? '—' }}</p>
<p><strong>Email:</strong> {{ $order->email ?? '—' }}</p>
<p><strong>Телефон:</strong> {{ $order->phone ?? '—' }}</p>
<p><strong>Комментарий:</strong> {{ $order->comment ?: '—' }}</p>
<p><strong>Промокод:</strong> {{ $order->promo_code ?: '—' }}</p>

<h2>Суммы</h2>
<p><strong>Сумма товаров:</strong> {{ number_format((float) $order->cart_summ, 2, '.', ' ') }} ₽</p>
<p><strong>Скидка:</strong> {{ number_format((float) $order->discount_summ, 2, '.', ' ') }} ₽</p>
<p><strong>Итого:</strong> {{ number_format((float) $order->total_summ, 2, '.', ' ') }} ₽</p>

@if($order->delivery)
    <h2>Доставка</h2>
    <p><strong>Провайдер:</strong> {{ $order->delivery->provider ?? '—' }}</p>
    <p><strong>Способ:</strong> {{ $order->delivery->method ?? '—' }}</p>
    <p><strong>Стоимость:</strong> {{ number_format((float) $order->delivery->price, 2, '.', ' ') }} ₽</p>
    <p><strong>Город:</strong> {{ $order->delivery->city ?? '—' }}</p>
    <p><strong>Адрес доставки:</strong> {{ $order->delivery->delivery_address ?? '—' }}</p>
    <p><strong>Квартира:</strong> {{ $order->delivery->apartment ?? '—' }}</p>
    @if($order->delivery->pickup_point_address)
        <p><strong>Пункт выдачи:</strong> {{ $order->delivery->pickup_point_address }}</p>
    @endif
    @if($order->delivery->delivery_date_range)
        <p><strong>Дата доставки:</strong> {{ $order->delivery->delivery_date_range['start'] ?? '' }}
            @if(isset($order->delivery->delivery_date_range['end']))
                — {{ $order->delivery->delivery_date_range['end'] }}
            @endif
        </p>
    @endif
@endif

@if($order->items->count() > 0)
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
            @foreach($order->items as $item)
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