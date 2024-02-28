@extends('layouts.all')

@php
    $title = "Понравившиеся товары";
    $description = "Понравившиеся товары";
@endphp

@section('title', $title)
@section('description', $description)

@section('content')
    <x-breadcrumbs :title="$title"></x-breadcrumbs>

    <div id="favorites_app" class="container">
        <h1>Избранное</h1>
        <favorites></favorites>
    </div>
@endsection
