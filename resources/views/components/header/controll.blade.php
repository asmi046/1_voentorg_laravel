<section class="header_info">
    <div class="container">
        <a href="{{route('home')}}" class="logo"></a>
              <form role="search" method="get" class="searchform" id="searchform" action="" >
                  <input type="text" value="{{old("search_str")}}" name="search_str" id="search_str" />
                  <button type="submit"></button>
              </form>

        <a id="counter_app" href="{{ route('bascet') }}" class="header__bascket-icon d-flex">
            <div class="bascket-icon-1 d-flex"></div>
            <div class="bascket-icon-2 bascet_counter d-flex">
                <cart-counter></cart-counter>
            </div>
        </a>

        <div class="header-contacts">
            <a href="tel:+74712730499">  <span class=red> 7 (4712) 730-449 </span> </a>	<div class="address">г. Курск, ул. Верхняя Луговая, 6 </div>
        </div>


        <div class="header-contacts">
            <a href="tel:+79510839956"><span class=red>+7 (951) 083-99-56 </span> </a>	<div class="address">ул. Карла Маркса, д. 66/3</div>
        </div>
    </div>
  </section>
