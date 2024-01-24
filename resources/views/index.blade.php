@extends('layouts.all')

@php
    $title = "Военторг - Тактическое снаряжение, берцы, Юнармия, ВКБО";
    $description = "Военторг тактическое снаряжение, форма ВКБО ВКПО, тактические костюмы, форма Юнармии, берцы (LOWA, Лова, Бизон), Горка, форменная одежда полиции, МЧС, кадетов.";
@endphp

@section('title', $title)
@section('description', $description)

@section('content')
    <x-main-banner></x-main-banner>

    <section class="vedomstva_section">
        <div class="container">
            <h2>Ведомства и подразделения</h2>
            <div class="vedomstva_main_wrap">
                @foreach ($vedomstva as $item)
                    <x-vedomstvo-card :item="$item"></x-vedomstvo-card>
                @endforeach
            </div>

            <div class="section_btn_wrapper">
                <a class="btn" href="{{ route("vedomstva") }}">Все подразделения и ведомства</a>
            </div>
        </div>
    </section>

    <x-tovar-line-section name="Популярные товары">
        @foreach ($sales_liders as $item)
            <x-tovar-card.main :item="$item"></x-tovar-card.main>
        @endforeach
    </x-tovar-line-section>

    <x-categories.list>
        @foreach ($category as $item)
            @if (!empty($item->img))
                <x-categories.item :item="$item"></x-categories.item>
            @endif
        @endforeach
    </x-categories.list>

    <x-tovar-line-section name="Новинки">
        @foreach ($sales as $item)
            <x-tovar-card.main :item="$item"></x-tovar-card.main>
        @endforeach
    </x-tovar-line-section>

@endsection
