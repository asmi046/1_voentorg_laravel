<header class="header-top">
	<div class="container">
		<div class="header-top__wrapper">

            <ul id="header-top__menu" class="header-top__menu">
                <x-smile-menu-puncts></x-smile-menu-puncts>
            </ul>

			<ul class="social-menu">
				<li><a href="{{$options["vk_lnk"]}}" style="background-image: url({{asset('/images/icons/icons-vk.png')}})"></a></li>
				<li><a href="{{$options["ok_lnk"]}}" style="background-image: url({{asset('/images/icons/icons-ok.png')}})"></a></li>
				<li><a href="{{$options["telegram_lnk"]}}" style="background-image: url({{asset('/images/icons/icons-tg.png')}})"></a></li>
			</ul>
		</div>
	</div>
</header>