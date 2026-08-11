<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Клиент</th>
                <th>Телефон</th>
                <th>Сумма</th>
                <th>Доставка</th>
                <th>Цена доставки</th>
                <th>Дата</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->name }}<br><small>{{ $order->email }}</small></td>
                    <td>{{ $order->phone }}</td>
                    <td>{{ number_format((float) $order->amount, 0, ',', ' ') }} ₽</td>
                    <td>{{ $order->delivery ?: 'Самовывоз' }}</td>
                    <td>{{ $order->delivery_price ? number_format((float) $order->delivery_price, 0, ',', ' ') . ' ₽' : '—' }}
                    </td>
                    <td>{{ $order->created_at->format('d.m.Y H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{ $orders->links() }}
