@extends('layouts.all')

@php
    $title = "Благодарим за покупку";
    $description = "Благодарим за покупку, ждите обратной связи от наших специалистов";
@endphp

@section('title', $title)
@section('description', $description)

@section('content')

<x-breadcrumbs :title="$title"></x-breadcrumbs>
    <div class="container">
        <h1>Благодарим за покупку</h1>
    </div>

    <section>
        <div class="container">
            <p>Наши специалисты свяжутся с Вами в ближайшее время для подтверждения заказа.</p>

            <h2>Так же Вы можете приобрести продукцию в сети наших магазинов:</h2>
            @foreach ($shops as $item)
                <div class="shop_page_wrapp">
                    <h2>{{ $item->name }}</h2>
                    <p><strong>Адрес:</strong> {{ $item->adress }}
                        @if (!empty($item->orientir))
                            ({{ $item->orientir }})
                        @endif
                    </p>
                    <p>
                        <strong>Телефон:</strong>
                        <a class="callback__phone" href="tel:+7{{phone_format($item->phone)}}">{{ $item->phone }}</a>
                    </p>

                    <p>
                        <strong>e-mail:</strong>
                        <a href="mailto:{{phone_format($item->email)}}">{{ $item->email }}</a>
                    </p>
                    <p>
                        <strong>Время работы:</strong>
                        {{ $item->time_work }}
                    </p>

                </div>

            @endforeach
        </div>
    </section>

@endsection
