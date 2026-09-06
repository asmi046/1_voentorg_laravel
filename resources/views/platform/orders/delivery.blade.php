<div class="text-left">
    <h2>Данные доставки</h2>

    @if($delivery)
        <table class="table">
            <tbody>
            <tr>
                <td width="30%"><strong>Провайдер:</strong></td>
                <td>{{ $delivery->provider ?: '—' }}</td>
            </tr>
            <tr>
                <td><strong>Способ доставки:</strong></td>
                <td>{{ $delivery->method ?: '—' }}</td>
            </tr>
            <tr>
                <td><strong>Стоимость:</strong></td>
                <td>{{ number_format((float) $delivery->price, 2, ',', ' ') }} ₽</td>
            </tr>
            <tr>
                <td><strong>Диапазон дат:</strong></td>
                <td>
                    @if($delivery->delivery_date_range)
                        {{ $delivery->delivery_date_range['start'] ?? '' }}
                        @if(isset($delivery->delivery_date_range['end']))
                            — {{ $delivery->delivery_date_range['end'] }}
                        @endif
                    @else
                        —
                    @endif
                </td>
            </tr>
            <tr>
                <td><strong>Город:</strong></td>
                <td>{{ $delivery->city ?: '—' }}</td>
            </tr>

            @if($delivery->method === 'pickup_point')
                <tr>
                    <td><strong>ID пункта выдачи:</strong></td>
                    <td>{{ $delivery->pickup_point_id ?: '—' }}</td>
                </tr>
                <tr>
                    <td><strong>Адрес пункта выдачи:</strong></td>
                    <td>{{ $delivery->pickup_point_address ?: '—' }}</td>
                </tr>
            @endif

            @if($delivery->method === 'courier')
                <tr>
                    <td><strong>Улица:</strong></td>
                    <td>{{ $delivery->street ?: '—' }}</td>
                </tr>
                <tr>
                    <td><strong>Дом:</strong></td>
                    <td>{{ $delivery->house ?: '—' }}</td>
                </tr>
                <tr>
                    <td><strong>Квартира:</strong></td>
                    <td>{{ $delivery->apartment ?: '—' }}</td>
                </tr>
                <tr>
                    <td><strong>Адрес доставки:</strong></td>
                    <td>{{ $delivery->delivery_address ?: '—' }}</td>
                </tr>
            @endif

            @if($delivery->tariff)
                <tr>
                    <td><strong>Тариф:</strong></td>
                    <td>
                        @if(is_string($delivery->tariff))
                            {{ $delivery->tariff }}
                        @else
                            {{ json_encode($delivery->tariff, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}
                        @endif
                    </td>
                </tr>
            @endif
            @if($delivery->raw_data)
                <tr>
                    <td colspan="2">
                        <details>
                            <summary><strong>Дополнительные данные</strong></summary>
                            <pre class="mt-3">{{ json_encode($delivery->raw_data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}</pre>
                        </details>
                    </td>
                </tr>
            @endif
            </tbody>
        </table>
    @else
        <p>Данные о доставке отсутствуют</p>
    @endif
</div>
