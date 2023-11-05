<footer class="footer">
    <div class="container">
      <div class="footer-block">
        <div class="footer-title"><h2>Информация</h2></div>
        <ul>
            <x-smile-menu-puncts></x-smile-menu-puncts>
        </ul>
      </div>
      <div class="footer-block">
        <div class="footer-title"><h2>Каталог товаров</h2></div>

         <ul>
            <li><a href="{{route('catalog')}}">Каталог</a></li>
            <li><a href="#">Обувь, берцы</a></li>
            <li><a href="#">Головные уборы</a></li>
            <li><a href="#">Снаряжение</a></li>
         </ul>

      </div>
      <div class="footer-block">
          <div class="footer-title"><h2>Мы в соцсетях</h2></div>
        <ul class="social-menu">
            <li><a href="{{$options["vk_lnk"]}}" style="background-image: url({{asset('/images/icons/icons-w-vk.png')}})"></a></li>
            <li><a href="{{$options["ok_lnk"]}}" style="background-image: url({{asset('/images/icons/icons-w-ok.png')}})"></a></li>
            <li><a href="{{$options["telegram_lnk"]}}" style="background-image: url({{asset('/images/icons/icons-w-tg.png')}})"></a></li>
        </ul>
      </div>
      <div class="footer-block">

          <div class="footer-title"><h2>Контакты</h2></div>

          <ul>
            <li><span class="footer-address adr_b">1-й Военторг г.Курск, ул.Верхняя Луговая, д.6 (ост. Ц.Рынок, ТЦ "ПОКРОВСКИЙ")</span></li>
            <li><a class="callback__phone" href="tel:+79513112124">+7 (951) 311 21 24</a></li>
            <li><a class="callback__phone" href="tel:+74712730449">+7 (4712) 730-449</a></li>
          </ul>

          <ul>
            <li><span class="footer-address adr_b">1-й Военторг г.Курск, ул.Карла Маркса, д.66/3 т.</span></li>
            <li><a class="callback__phone" href="tel:+79510889956">+7 (951) 088 99 56</a></li>
          </ul>

          <ul>
            <li><span class="footer-address adr_b">1-й Военторг Жуковский г.Курск, пос.им.М.Жукова, квартал 5, д.18, т.</span></li>
            <li><a class="callback__phone" href="tel:+79510818505">+7 (951) 081 85 05</a></li>
          </ul>

          <ul>
            <li><span class="footer-address adr_b">1-й Военторг Валуйки Белгородская область, Валуйки, ул. 9 Января</span></li>
            <li><a class="callback__phone" href="tel:+79040971614">+7 (904) 097 16 14</a></li>
          </ul>

          <ul>
            <li><span class="footer-address adr_b">1-й Воентор Солоти Белгородская область,Валуйки с. Солоти, ул. Советская , 21. т.</span></li>
            <li><a class="callback__phone" href="tel:+79040971614">+7 (904) 097 16 14</a></li>
          </ul>

      </div>
    </div>
  </footer>
