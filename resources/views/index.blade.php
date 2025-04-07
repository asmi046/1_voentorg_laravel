@extends('layouts.all')

@php
    $title = isset($options["seo_title_main"])?$options["seo_title_main"]:"Служивый 46 - Тактическое снаряжение, берцы, Юнармия, ВКБО";
    $description = isset($options["seo_description_main"])?$options["seo_description_main"]:"Служивый46 тактическое снаряжение, форма ВКБО ВКПО, тактические костюмы, форма Юнармии, берцы (LOWA, Лова, Бизон), Горка, форменная одежда полиции, МЧС, кадетов.";
@endphp

@section('title', $title)
@section('description', $description)

@section('content')



    <section>
        <div class="container">
            <div class="cat_product_wrapper">
                <x-categories.navigation></x-categories.navigation>


                <div class="cat_product_list_wrapper">
                    <div class="main-slider">
                        <x-main-banner :banners="$banners"></x-main-banner>

                        <article class="vedomstva_section">
                            <h2>Ведомства и подразделения</h2>
                            <div class="vedomstva_main_wrap">
                                @foreach ($vedomstva as $item)
                                    <x-vedomstvo-card :item="$item"></x-vedomstvo-card>
                                @endforeach
                            </div>

                            <div class="section_btn_wrapper">
                                <a class="btn" href="{{ route("vedomstva") }}">Все подразделения и ведомства</a>
                            </div>
                        </article>

                        <x-min-price></x-min-price>

                        {{-- <article class="products">
                            <h2>Популярные товары</h2>
                            <div class="product_card_wrapper c_3">
                                @foreach ($sales_liders as $item)
                                    <x-tovar-card.main :item="$item"></x-tovar-card.main>
                                @endforeach
                            </div>
                        </article> --}}

                    </div>
                </div>

            </div>
        </div>
    </section>


    <x-categories.list>
        @foreach ($category as $item)
            @if (!empty($item->img))
                <x-categories.item :item="$item"></x-categories.item>
            @endif
        @endforeach
    </x-categories.list>

    <x-tovar-line-section name="Новинки">
        @foreach ($news as $item)
            <x-tovar-card.main :item="$item"></x-tovar-card.main>
        @endforeach
    </x-tovar-line-section>

@endsection
