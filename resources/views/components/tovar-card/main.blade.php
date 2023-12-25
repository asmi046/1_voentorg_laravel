<div class="prod-card__body d-flex">

	<a href="{{ route('product', $item->slug) }}" class="prod-card__link">
		<img src="{{$item->img}}" alt="">
	</a>

	<a href="{{ route('product', $item->slug) }}" class="prod-card__text d-flex">
		<h4>{{$item->title}}</h4>
		<span class="spacer__vendor">Артикул: {{$item->sku}}</span>
		<div class="prod-card__price d-flex">
            @if ($item->product_prices[0]->old_price)
                <p class='prod-card__price-old'> {{$item->product_prices[0]->old_price}} руб.</p>
			@endif

            <p class="prod-card__price-new">{{$item->product_prices[0]->price}} руб.</p>



		</div>
		<p class="prod-card__benefit"><span></span></p>
	</a>

	<div class="prod-card__button d-flex">
		<a href="#" class="prod-card__order d-flex">Подробнее</a>
	</div>
</div>
