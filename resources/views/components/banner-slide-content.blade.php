<article class="slider_contetnt">
    <img loading="lazy" src="{{asset($img)}}" alt="">
    <div class="info-sl__text-new">
        <h2>{!!$title!!}</h2>
        @if ($subtitle !== "")
            <p>{{$subtitle}}</p>
        @endif
    </div>
</article>


