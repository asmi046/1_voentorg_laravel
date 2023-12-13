@extends('layouts.all')

@php
    $title = $category_info->seo_title;
    $description = $category_info->seo_description;
@endphp

@section('title', $title)
@section('description', $description)

@section('content')
    <x-breadcrumbs :category="$category_info" ></x-breadcrumbs>
    <div class="container">
        <h1>{{$category_info->title}}</h1>

        <div class="cat_product_wrapper">
            <div class ="cat_navigation">
                <ul>
                    <li class="cat-item cat-item-299">
                        <a href="#">ВКПО (ВКБО)</a>
                    </li>
                    <li class="cat-item cat-item-38">
                        <a href="#">Головные уборы</a>
                        <ul class="children">
                            <li class="cat-item cat-item-56"><a href="#">Банданы</a>
                            </li>
                            <li class="cat-item cat-item-59"><a href="#">Береты</a>
                            </li>
                            <li class="cat-item cat-item-170"><a href="#">Головные уборы женские</a>
                            </li>
                            <li class="cat-item cat-item-60"><a href="#">Кепки бейсболки</a>
                            </li>
                            <li class="cat-item cat-item-279"><a href="#">Маски</a>
                            </li>
                            <li class="cat-item cat-item-63"><a href="#">Пилотки</a>
                            </li>
                            <li class="cat-item cat-item-65"><a href="#">Фуражки и бескозырки</a>
                            </li>
                            <li class="cat-item cat-item-66"><a href="#">Шапки</a>
                            </li>
                            <li class="cat-item cat-item-67"><a href="#">Шапки меховые</a>
                        </li>
                        </ul>
                    </li>
                    <li class="cat-item cat-item-263">
                        <a href="#">Детская одежда</a>
                    </li>
                    <li class="cat-item cat-item-302">
                        <a href="#">Женская форма</a>
                        <ul class="children">
                            <li class="cat-item cat-item-307"><a href="#">Юбки женские</a>
                            </li>
                        </ul>
                    </li>
                    <li class="cat-item cat-item-303">
                        <a href="#">Кадетская форма</a>
                    </li>
                    <li class="cat-item cat-item-289">
                        <a href="#">МЧС</a>
                    </li>
                    <li class="cat-item cat-item-40">
                        <a aria-current="page" href="#">Обувь, берцы</a>
                        <ul class="children">
                            <li class="cat-item cat-item-309"><a href="#">Lowa / Лова тактическая обувь</a>
                            </li>
                            <li class="cat-item cat-item-70"><a href="#">Берцы демисезонные / зимние</a>
                            </li>
                            <li class="cat-item cat-item-71"><a href="#">Берцы летние (неутепленные)</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>

            <div class="cat_product_list_wrapper">
                <div class="sub_categories_list">
                    @foreach ($sub_category as $item)
                        <a href="{{route('category', $item->slug)}}" class="sc__item">
                            <div class="img">
                                <img src="{{ !empty($item->img)?$item->img:asset('img/sc_test.webp') }}" alt="{{$item->title}}">
                            </div>

                            <div class="name">
                               <span>{{$item->title_mini}}</span>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="cat_product_list">
                    @foreach ($tovars as $item)
                        <x-tovar-card.main :item="$item"></x-tovar-card.main>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
