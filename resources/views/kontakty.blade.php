@extends('layouts.all')

@php
    $title = "Контакты";
    $description = "1-й Военторг контактная информация, адреса и телефоны наших магазинов";
@endphp

@section('title', $title)
@section('description', $description)

@section('content')
    <x-breadcrumbs :title="$title"></x-breadcrumbs>
    <div class="container">
        <h1>Контакты</h1>
    </div>
@endsection
