@extends('layouts.all')

@php
    $title = "Новости магазина «Служивый 46»";
    $description = "Новости и акции магазина «Служивый 46», узнавайте первыми о новых поступлениях и акционных предложениях нашего магазина";
@endphp

@section('title', $title)
@section('description', $description)

@section('content')
    <x-breadcrumbs :title="$title"></x-breadcrumbs>
    <div class="container">
        <h1>Новости</h1>
        <div class="news_wrapper">
            @foreach ($news as $item)
                <x-news.card :item="$item"></x-news.card>
            @endforeach
        </div>

        <x-pagination :tovars="$news"></x-pagination>
        <br>
    </div>
@endsection
