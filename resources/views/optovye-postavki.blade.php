@extends('layouts.all')

@php
    $title = "Оптовые поставки";
    $description = "Магазин Военторг предлагает, как готовые изделия согласно каталогу товаров, так и заказные позиции товаров (по индивидуальному заказу).";
@endphp

@section('title', $title)
@section('description', $description)

@section('content')
    <x-breadcrumbs :title="$title"></x-breadcrumbs>

    <div class="container">
        <h1>Оптовые поставки</h1>

        {!! $options["opt"] !!}
    </div>
@endsection
