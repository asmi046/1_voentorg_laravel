<a href="{{ route('product', $item->slug) }}" class="prod_card d-flex">

    <div class="img_wrapper">
        <img src="{{$item->img}}" alt="Артикул: {{$item->sku}} {{$item->title}}">
    </div>

    <div class="text_wrapper">
		<h4>{{$item->title}}</h4>
		<span class="spacer__vendor">Артикул: {{$item->sku}}</span>
		<div class="prod-card__price d-flex">
            @if (count($item->product_prices) == 0 || issetr($item->product_prices[0]->old_price))
                <p class='prod-card__price-old'> {{$item->product_prices[0]->old_price}} руб.</p>
			@endif

            <p class="prod-card__price-new">{{$item->product_prices[0]->price}} руб.</p>
		</div>
    </div>

	<div class="prod-card__button d-flex">
		<span class="prod-card__order d-flex">Подробнее</span>
	</div>
</a>
