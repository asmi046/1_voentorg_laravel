@extends('layouts.all')

@php
    $title = "Форма и экипировка для подразделений и ведомств";
    $description = "Форма и экипировка для подразделений и ведомств купить онлайн";
@endphp

@section('title', $title)
@section('description', $description)

@section('content')
    <x-breadcrumbs :title="$title"></x-breadcrumbs>

    <section class="vedomstva">
        <div class="container">
            <h1>Подразделения и ведомства</h1>

            <div class="vedomstva_main_wrap vedomstva_cat_wrap">
                @foreach ($vedomstva as $item)
                    <x-vedomstvo-card :item="$item"></x-vedomstvo-card>
                @endforeach
            </div>

        </div>
    </section>




@endsection
