@extends('layouts.all')

@php
    $title = "Продукт";
    $description = "Продукт";
@endphp

@section('title', $title)
@section('description', $description)

@section('content')
    <x-breadcrumbs :product="$product" :category="$category"></x-breadcrumbs>

    <div class="container">
        <div class="product-wrapper ">
            <div class="product-wrapper__images">
                <swiper-container thumbs-swiper=".product_thumb_slider" class="product_main_slider" slides-per-view="1">

                    @if (isset($product->img))
                        <swiper-slide>
                            <a data-fslightbox="product_thumb_slider" href="{{ $product->img }}">
                                <img src="{{ $product->img }}" alt="{{ $product->title }}">
                            </a>

                        </swiper-slide>
                    @endif


                    @foreach ($product->product_images as $item)
                        <swiper-slide>
                            <a data-fslightbox="product_thumb_slider" href="{{ $item->link }}">
                                <img src="{{ $item->link }}" alt="{{ $item->alt }}">
                            </a>

                        </swiper-slide>
                    @endforeach
                </swiper-container>

                <swiper-container class="product_thumb_slider" space-between="30" slides-per-view="3">
                    @if (isset($product->img))
                        <swiper-slide>
                            <img src="{{ $product->img }}" alt="{{ $product->title }}">
                        </swiper-slide>
                    @endif

                    @foreach ($product->product_images as $item)
                        <swiper-slide>
                            <img src="{{ $item->link }}" alt="{{ $item->alt }}">
                        </swiper-slide>
                    @endforeach
                </swiper-container>

            </div>

            <div class="entry-content text_decoration">

                @auth('web')
                    <div class="edit_panel">
                        <a target="_blanck" href="{{ route('platform.product_edit', $product->id) }}">Редактировать товар</a>
                    </div>
                @endauth

                <h1>{{ $product->title }}</h1>
                <p class="sku_in_page">Артикул: <span>{{ $product->sku}}</span></p>

                <div class="content-block content-block__price">
                    @if ($product->old_price)
                        <span class="single-price__old">{{ $product->old_price}} руб.</span>
                    @endif

                    <span class="single-price">{{ $product->price}} руб.</span>
                </div>

                <div class= "by_blk">
                    <a href="#" class="btn buy_btn">Добавить в корзину</a>
                </div>

                @if (count($category) > 0)
                    <h3>Товар из категорий:</h3>
                    <div class="category_in_page">
                        @foreach ($category as $item)
                            <a title="Товарв категории {{ $item->title }}" href="{{ route('category', $item->slug) }}">{{ $item->title }}</a>
                        @endforeach
                    </div>
                @endif

                @if ($product->description)
                    <h2>Описание</h2>

                    <div class="text_styles">
                        {!! $product->description !!}
                    </div>
                @endif

            </div>

        </div>

        @if ($product->specification)
        <div class="tovar_bottom_part text_decoration">
            <h2>Все характеристики</h2>

            <div class="text_styles">
                {!! $product->specification !!}
            </div>
        </div>
        @endif
    </div>

@endsection
