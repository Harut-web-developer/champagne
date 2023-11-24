<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU&amp;apikey=e243c296-f6a7-46b7-950a-bd42eb4b2684" type="text/javascript"></script>
    <script src="/js/colorizer.js" type="text/javascript"></script>
    <script src="/js/multiroute_view_access.js" type="text/javascript"></script>

</head>

<body>
<div id="map"></div>
</body>

<style>
    body, #map {
        width: 100%; height: 100%; padding: 0; margin: 0;
    }
</style>
<script>
    // function init () {
    // var multiRoute = new ymaps.multiRouter.MultiRoute({
    // referencePoints: [
    // "40.186488388453874,44.51655924937668",
    // "40.26824750338569,44.6567222087418",
    // "40.18707857534972,44.603401590621395"
    // ],
    // params: {
    // routingMode: 'masstransit'
    // }
    // });
    //
    // ymaps.modules.require([
    // 'MultiRouteColorizer'
    // ], function (MultiRouteColorizer) {
    // new MultiRouteColorizer(multiRoute);
    // });
    //
    // var myMap = new ymaps.Map('map', {
    // center: [40.2100725,44.4987508],
    // zoom: 2,
    // controls: []
    // }, {
    // buttonMaxWidth: 300
    // });
    //
    // myMap.geoObjects.add(multiRoute);
    // }
</script>