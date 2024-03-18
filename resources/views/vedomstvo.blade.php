@extends('layouts.all')

@php
    $title = !empty($vedomstvo_info->seo_title)?$vedomstvo_info->seo_title:"";
    $description = !empty($vedomstvo_info->seo_description)?$vedomstvo_info->seo_description:"";
@endphp

@section('title', $title)
@section('description', $description)

@section('content')
    <x-breadcrumbs :vedomstvo="$vedomstvo_info" ></x-breadcrumbs>
    <div class="container">
        <h1>Одежда и экипировка для: {{$vedomstvo_info->title}}</h1>

        <div class="cat_product_wrapper">
            <x-categories.navigation></x-categories.navigation>

            <div class="cat_product_list_wrapper">
                <div class="category_content">
                    <x-sort-form></x-sort-form>
                    <div class="product_card_wrapper c_3">
                        @if (isset($tovars))
                            @foreach ($tovars as $item)
                                <x-tovar-card.main :item="$item"></x-tovar-card.main>
                            @endforeach
                        @else
                            <h2>Товары не найдены</h2>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
