@extends('layouts.all')

@php
    $title = "Производство ателье";
    $description = "1-й Военторг уже более 15-ти лет занимается пошивом одежды для различных силовых структур, кадетов, школ, пошив детских костюмов.";
@endphp

@section('title', $title)
@section('description', $description)

@section('content')
    <div class="container">
        <h1>Производство, ателье</h1>

        {!! $options["proizvodstvo"] !!}
    </div>
@endsection
