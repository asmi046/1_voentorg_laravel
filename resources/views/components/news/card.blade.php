<div class="news_acrd">
    <a href="{{ route("news_page", $item->slug) }}" class="img_wrap">
        <img src="{{ $item->img }}" alt="{{ $item->title }}">
    </a>
    <div class="text">
        <h2>{{ $item->title }}</h2>
        <span class="date">{{ date("d.m.Y", strtotime($item->created_at)) }}</span>
        {!! $item->short_description !!}
        <a class="more" href="{{ route("news_page", $item->slug) }}">Подробнее...</a>
    </div>
</div>
