@extends('layouts.all')

@php
    $title = "Корзина";
    $description = "Выбранные Вами товары";
@endphp

@section('title', $title)
@section('description', $description)

@section('content')
    <x-breadcrumbs :title="$title"></x-breadcrumbs>
    <div class="container">
        <h1>Корзина</h1>
    </div>

    <section>
        <div id="cart_app" class="container">
            <cart></cart>
        </div>
    </section>
@endsection
