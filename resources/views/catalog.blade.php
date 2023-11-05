@extends('layouts.all')

@php
    $title = "Каталог товаров";
    $description = "Ознакомьтесь с каталогом товаров нашего магазина";
@endphp

@section('title', $title)
@section('description', $description)

@section('content')
    <x-breadcrumbs :title="$title"></x-breadcrumbs>
    <div class="container">
        <h1>{{$title}}</h1>
        <x-categories.list>
            @foreach ($category as $item)
                <x-categories.item :item="$item"></x-categories.item>
            @endforeach
        </x-categories.list>
    </div>
@endsection
