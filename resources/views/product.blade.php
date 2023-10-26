@extends('layouts.all')

@php
    $title = "Продукт";
    $description = "Продукт";
@endphp

@section('title', $title)
@section('description', $description)

@section('content')
    <x-breadcrumbs :title="$title"></x-breadcrumbs>

    <div class="container">
        <div class="product-wrapper ">
            <div class="product-wrapper__images">
            </div>

            <div class="entry-content text_decoration">
                <h1>{{$title}}</h1>
                <p class="sku_in_page">Артикул: <span></span></p>

                <div class="content-block content-block__price">
                    <span class="single-price__old">2500 руб.</span>
                    <span class="single-price">1500 руб.</span>
                </div>

                <div class= "by_blk">
                    <a href="#" class="btn buy_btn">Добавить в корзину</a>
                </div>

            </div>

        </div>

        <div class="tovar_bottom_part text_decoration">
            <h2>Описание</h2>
            <h2>Все характеристики</h2>
        </div>
    </div>

@endsection
