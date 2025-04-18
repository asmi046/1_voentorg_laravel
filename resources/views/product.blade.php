@extends('layouts.all')

@php
$title = !empty($product->seo_title)?$product->seo_title:$product->title;
$description = !empty($product->seo_description)?$product->seo_description:$product->title;
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

            <div id="cart_app" class="entry-content text_decoration">

                @auth('web')
                    <div class="edit_panel">
                        <a target="_blanck" href="{{ route('platform.product_edit', $product->id) }}">Редактировать товар</a>
                    </div>
                @endauth

                <h1>{{ $product->title }}</h1>
                <p class="sku_in_page">Артикул: <span>{{ $product->sku}}</span></p>

                <page-to-cart sku="{{$product->sku}}" :prices="{{json_encode($product->product_prices)}}"></page-to-cart>


                <div class="page_manager_info">
                    <p>Уточнить цену и наличие товара вы можете по телефону <br><a href="tel:+79510849233">+7 (951) 084-92-33</a> Пн. - Пт. с 9 до 18.00 по МСК<br>Или по электронной почте: <a href="mailto:1voentorg@bk.ru">1voentorg@bk.ru</a> </p>
                </div>

                @if ( $product->product_prices && ($product->product_prices[0]->price < config('cart.min_price')) )
                    <x-min-price></x-min-price>
                @endif


                @if (count($category) > 0)
                    <h3>Товар из категорий:</h3>
                    <div class="category_in_page">
                        @foreach ($category as $item)
                            <a title="Товарв категории {{ $item->title }}" href="{{ route('category', $item->slug) }}">{{ $item->title }}</a>
                        @endforeach
                    </div>
                @endif

                @if ($product->description)


                    <div class="text_decoration">
                        <h2>Описание</h2>
                        {!! $product->description !!}
                    </div>
                @endif

            </div>

        </div>

        @if ($product->specification)
        <div class="tovar_bottom_part text_decoration">
            <h2>Все характеристики</h2>

            {!! $product->specification !!}
        </div>
        @endif
    </div>

@endsection
