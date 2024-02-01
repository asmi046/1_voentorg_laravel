<!DOCTYPE html>
<html lang="ru">
<head>
    <title>@yield('title') - Военторг</title>
    <meta name="description" content="@yield('description')">

    <meta property="og:locale" content="ru_RU" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="@yield('title')" />
    <meta property="og:description" content="@yield('description')" />
    <meta property="og:url" content="{{route('home')}}" />
    <meta property="og:site_name" content="Магазин военных товаров и аммуниции" />
    <meta property="og:image" content="{{asset('img/og_img.jpg')}}" />
    <meta property="og:image:type" content="image/jpeg" />
    <meta name="twitter:card" content="summary_large_image" />

	<meta charset="UTF-8">
	<meta name="format-detection" content="telephone=no">

    <link rel="icon" type="image/png" href="{{asset('/images/favicons/icon256.png')}}" sizes="256x256">
    <link rel="icon" type="image/png" href="{{asset('/images/favicons/icon128.png')}}" sizes="128x128">
    <link rel="icon" type="image/png" href="{{asset('/images/favicons/icon64.png')}}" sizes="64x64">
    <link rel="icon" type="image/png" href="{{asset('/images/favicons/icon32.png')}}" sizes="32x32">
    <link rel="icon" type="image/png" href="{{asset('/images/favicons/icon16.png')}}" sizes="16x16">
    <link rel="icon" type="image/svg" href="{{asset('/images/favicons/icon.svg')}}" sizes="any">

    <meta name="_token" content="{{ csrf_token() }}">

	<meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite([
        'resources/css/app.css',
        'public/fonts/shop/style.css',
        'public/scss/style.scss',
        'public/scss/cart/all-cart.scss',
        'resources/js/app.js',
        'resources/js/cart.js',
        'public/js/fslightbox.js',
    ])

</head>

<body>
    @include("cart.cart-svg")
    @include("allicon")
    <div class="wrapper" id="global_app">
        <x-header.top-line></x-header.top-line>
        <x-header.controll></x-header.controll>
        <x-header.main-menu></x-header.main-menu>

        @yield('content')

        <x-footer></x-footer>
    </div>
</body>

</html>
