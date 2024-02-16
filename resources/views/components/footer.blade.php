<footer class="footer">
    <div class="container">
        <div class="footer-block">

            <div class="footer-title"><h2>Контакты</h2></div>

            @foreach ($shops as $item)
                <ul>
                    <li>
                        <strong>
                            {{ $item->name }}
                        </strong>
                    </li>
                    <li>
                        <span class="footer-address adr_b">
                            {{ $item->adress }}
                            @if (!empty($item->orientir))
                                ({{ $item->orientir }})
                            @endif

                        </span>
                    </li>
                    <li><a class="callback__phone" href="tel:+7{{phone_format($item->phone)}}">{{ $item->phone }}</a></li>
                </ul>
            @endforeach

        </div>

        <div class="footer-block">
        <div class="footer-title"><h2>Информация</h2></div>
        <ul>
            <x-smile-menu-puncts></x-smile-menu-puncts>
        </ul>
      </div>

      <div class="footer-block">
          <div class="footer-title"><h2>Мы в соцсетях</h2></div>
          <x-social-icon></x-social-icon>
      </div>

    </div>
  </footer>
