<section class="main-slider">
    <div class="container">
        <swiper-container>

            @foreach ($banners as $item)
                <swiper-slide lazy="true">
                    <x-banner-slide-content
                        :img="$item->img"
                        :title="$item->title"
                        :subtitle="$item->sub_title">
                    </x-banner-slide-content>
                </swiper-slide>
            @endforeach


        </swiper-container>
    </div>
</section>
