@extends('layouts.all')

@php
    $title = !empty($category_info->seo_title)?$category_info->seo_title:"";
    $description = !empty($category_info->seo_description)?$category_info->seo_description:"";
@endphp

@section('title', $title)
@section('description', $description)

@section('content')
    <x-breadcrumbs :category="$category_info" ></x-breadcrumbs>
    <div class="container">
        <h1>{{$category_info->title}}</h1>

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

        <div class="cat_product_wrapper">
            <x-categories.navigation :category="$category_info"></x-categories.navigation>


            <div class="cat_product_list_wrapper">
                <div class="category_content">


                    <x-sort-form></x-sort-form>

                    <div class="product_card_wrapper c_3">

                        @foreach ($tovars as $item)
                            <x-tovar-card.main :item="$item"></x-tovar-card.main>
                        @endforeach

                        <x-pagination :tovars="$tovars"></x-pagination>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
