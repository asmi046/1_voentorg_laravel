<script src="//api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
<script>
    let shops = @json($shops);

    console.log(shops)

    ymaps.ready(init);

    function init () {
        let centerMap =[51.08150249746985,37.88732998300776]
        var myMap = new ymaps.Map("map_page", {
            // Координаты центра карты
            center: centerMap,
            // Масштаб карты
            zoom: 7,
            // Выключаем все управление картой
            controls: ['geolocationControl','zoomControl']
        });

        var myGeoObjects = [];


        var clusterer = new ymaps.Clusterer({
            clusterDisableClickZoom: false,
            clusterOpenBalloonOnClick: false,
            preset: 'islands#greenClusterIcons',
        });

        shops.forEach(element => {
            myGeoObjects = new ymaps.Placemark(element.geo.split(','),
            {
                                        hintContent: '<div class="map-hint">'+element.name+'</div>',
                                        balloonContent: '<div class="map-hint"><b>'+element.name+'</b> <br/> '+element.adress+'<br/> '+element.phone+' </div>',
                                        }
                                        ,{
                                            color:'#65B965'
                                        }
                    );
            clusterer.add(myGeoObjects);
        });





        myMap.geoObjects.add(clusterer);
        // Отключим zoom
        myMap.behaviors.disable('scrollZoom');

    }

</script>

<section class="map_comment">
    <div class="container">
        <h2>Посетите сеть наших рознечных магазинов:</h2>
    </div>
</section>

<section id="map_page" class="map_page">

</section>
