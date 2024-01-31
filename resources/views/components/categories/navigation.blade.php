<div class ="cat_navigation">
    <ul>
        @foreach ($all_cat as $item)
            <li class="cat-item">
                <a href="{{ route('category', $item['main']->slug) }}">{{$item['main']->title}}</a>
                <ul class="children">
                    <li class="cat-item">
                        <a href="{{ route('category', $item['main']->slug) }}">Смотреть все товары</a>
                    </li>
                    @if (!empty($item['sub']))
                        @foreach ($item['sub'] as $subitem)
                            <li class="cat-item">
                                <a href="{{ route('category', $subitem->slug) }}">{{$subitem->title_mini}}</a>
                            </li>
                        @endforeach
                    @endif
                </ul>
            </li>
        @endforeach

    </ul>
</div>
