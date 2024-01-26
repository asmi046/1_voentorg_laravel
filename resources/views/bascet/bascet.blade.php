@extends('layouts.all')

@php
    $title = "Корзина";
    $description = "Выбранные Вами товары";
@endphp

@section('title', $title)
@section('description', $description)

@section('content')
    <x-head-page :h1="$title"></x-head-page>

    <x-breadcrumbs :title="$title"></x-breadcrumbs>

    <section class="bascet_input_section">
        <div class="_container">
            <bascet></bascet>
        </div>
    </section>

@endsection
