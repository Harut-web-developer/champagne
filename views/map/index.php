<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
$this->params['sub_page'] = $sub_page;
$this->params['date_tab'] = $date_tab;

?>
<div class="titleAndPrevPage">
    <i class='bx bxs-log-out iconPrevPage' onclick="window.location = document.referrer"></i>
</div>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU&amp;apikey=e243c296-f6a7-46b7-950a-bd42eb4b2684" type="text/javascript"></script>
    <script src="/js/colorizer.js" type="text/javascript"></script>
    <script src="/js/multiroute_view_access.js" type="text/javascript"></script>
</head>

<body>
    <div class="form-group col-md-12 col-lg-12 col-sm-12 mapFilter">
        <div class="form-group col-md-6 col-lg-6 col-sm-6 loguser">
            <label for="routeSelect">Երթուղի</label>
            <select id="routeSelect" class="form-select form-control valuemap" aria-label="Default select example">
                <option value="">Ընտրել երթուղին</option>
                <?php foreach ($route as $index => $rout ){ ?>
                    <option value="<?= $rout['id'] ?>"><?= $rout['route'] ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="form-group col-md-6 col-lg-6 col-sm-6 logAction">
            <label for="myLocalDate">Ընտրել ամսաթիվը</label>
            <input id="myLocalDate" class="fil-input form-control valuemap" type="datetime-local" name="date">
        </div>
    </div>
    <div id="map">

    </div>
    <script>
        const x = document.getElementById("map");

        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showPosition);
            } else {
                x.innerHTML = "Geolocation is not supported by this browser.";
            }
        }

        function showPosition(position) {
            x.innerHTML = "Latitude: " + position.coords.latitude +
                "<br>Longitude: " + position.coords.longitude;
        }
        setInterval(getLocation, 1000);
        getLocation();
    </script>

<!--    <script>-->
<!--        ymaps.ready(init);-->
<!---->
<!--        function init() {-->
<!--            var map = new ymaps.Map("map", {-->
<!--                center: [0, 0],-->
<!--                zoom: 3-->
<!--            });-->
<!---->
<!--            var placemark = new ymaps.Placemark([0, 0], {-->
<!--                balloonContent: "Your Location"-->
<!--            });-->
<!---->
<!--            map.geoObjects.add(placemark);-->
<!---->
<!--            function updateLocation() {-->
<!--                navigator.geolocation.getCurrentPosition(-->
<!--                    function(position) {-->
<!--                        var userLocation = [position.coords.latitude, position.coords.longitude];-->
<!---->
<!--                        placemark.geometry.setCoordinates(userLocation);-->
<!--                        map.setCenter(userLocation);-->
<!--                        console.log(userLocation)-->
<!--                    },-->
<!--                    // function(error) {-->
<!--                    //     console.error('Error getting user location:', error);-->
<!--                    // }-->
<!--                );-->
<!--            }-->
<!---->
<!--            setInterval(updateLocation, 1000);-->
<!---->
<!--            updateLocation();-->
<!--        }-->
<!--    </script>-->

</body>
<style>
    body, #map {
        width: 100%; height: 100%; padding: 0; margin: 0;
    }
</style>
