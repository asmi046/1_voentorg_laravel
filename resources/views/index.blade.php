@extends('layouts.all')

@php
    $title = "Военторг - Тактическое снаряжение, берцы, Юнармия, ВКБО";
    $description = "Военторг тактическое снаряжение, форма ВКБО ВКПО, тактические костюмы, форма Юнармии, берцы (LOWA, Лова, Бизон), Горка, форменная одежда полиции, МЧС, кадетов.";
@endphp

@section('title', $title)
@section('description', $description)

@section('content')
    <x-main-banner></x-main-banner>

    <x-tovar-line-section name="Популярные товары">
        @for ($i=0; $i<4; $i++)
            <x-tovar-card.main></x-tovar-card.main>
        @endfor
    </x-tovar-line-section>

    <x-categories.list>
        @for ($i=0; $i<20; $i++)
            <x-categories.item></x-categories.item>
        @endfor
    </x-categories.list>

    <x-tovar-line-section name="Новинки">
        @for ($i=0; $i<8; $i++)
            <x-tovar-card.main></x-tovar-card.main>
        @endfor
    </x-tovar-line-section>

@endsection
