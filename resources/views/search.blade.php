@extends('layouts.all')

@php
    $title = "Результаты поиска по запросу: ".$search_str;
    $description = "Результаты поиска по запросу: ".$search_str;
@endphp

@section('title', $title)
@section('description', $description)

@section('content')
    <x-breadcrumbs :title="$title" ></x-breadcrumbs>
    <div class="container">
        <h1>{{ $title }}</h1>

        <div class="cat_product_wrapper">
            <x-categories.navigation :category="null"></x-categories.navigation>


            <div class="cat_product_list_wrapper">
                <div class="category_content">


                    <x-sort-form :searchstr="$search_str"></x-sort-form>

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
