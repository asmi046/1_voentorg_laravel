@php
    if (isset($category)){
        if (isset($category->parent)) {
            $main_cat = $category->parent;
            $sub_cat = $category->id;
        } else {
            $main_cat = $category->id ;
            $sub_cat = 0;
        }
    } else {
        $main_cat = 0;
        $sub_cat = 0;
    }


@endphp
<div class ="cat_navigation">
    <h2>Каталог</h2>
    <ul>
        @foreach ($all_cat as $item)
            <li class="cat-item">
                <a href="{{ route('category', $item['main']->slug) }}"
                    @class(['active' => $main_cat == $item['main']->id])
                    >{{$item['main']->title}}</a>
                <ul class="children">
                    <li class="cat-item">
                        <a href="{{ route('category', $item['main']->slug) }}"
                            @class(['active' => $main_cat == $item['main']->id && $sub_cat == 0])
                        >Смотреть все товары</a>
                    </li>
                    @if (!empty($item['sub']))
                        @foreach ($item['sub'] as $subitem)
                            <li class="cat-item">
                                <a href="{{ route('category', $subitem->slug) }}"
                                    @class(['active' => $sub_cat == $subitem->id])
                                >{{$subitem->title_mini}}</a>
                            </li>
                        @endforeach
                    @endif
                </ul>
            </li>
        @endforeach

    </ul>
</div>
