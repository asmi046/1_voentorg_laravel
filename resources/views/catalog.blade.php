@extends('layouts.all')

@php
    $title = "Каталог товаров";
    $description = "Ознакомьтесь с каталогом товаров нашего магазина";
@endphp

@section('title', $title)
@section('description', $description)

@section('content')
    <x-breadcrumbs :title="$title"></x-breadcrumbs>

    <section>
        <div class="container">
            <h1>Каталог товаров</h1>
        </div>
    </section>


    <x-categories.list>
        @foreach ($category as $item)
            @if (!empty($item->img))
                <x-categories.item :item="$item"></x-categories.item>
            @endif
        @endforeach
    </x-categories.list>

@endsection
