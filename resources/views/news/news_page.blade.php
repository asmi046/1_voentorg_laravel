@extends('layouts.all')

@php
    $title = (empty($page->seo_title))?$page->title:$page->seo_title;
    $description = (empty($page->seo_description))?$page->title:$page->seo_description;;
@endphp

@section('title', $title)
@section('description', $description)

@section('content')
    <x-breadcrumbs :news="$page"></x-breadcrumbs>
    <div class="container text_decoration">
        <h1>{{$title}}</h1>

        <p class="date"> {{ date("d.m.Y", strtotime($page->created_at)) }} </p>

        <img class="page_obl" src="{{ $page->img }}" alt="{{ $page->title }}">

        {!! $page->description !!}
    </div>
@endsection
