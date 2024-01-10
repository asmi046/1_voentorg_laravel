<a href="{{route('category', $item->slug)}}" class="cat_in_page_blk">

    <img loading="lazy" src="{{$item->img}}" alt="Купить {{$item->title}}" title="{{$item->title}}">


    <div class="cattext">
        <span>{{$item->title}}</span>
    </div>

</a>
