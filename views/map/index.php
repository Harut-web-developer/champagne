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
    <div class="form-group col-md-12 col-lg-12 col-sm-12" style="display: flex;">
        <div class="form-group col-md-5 col-lg-3 col-sm-6 loguser">
            <label for="multipleClients">Routes</label>
            <select class="form-select form-control valuemap" id="routeSelect" aria-label="Default select example">
                <option>Select the route</option>
                <?php foreach ($route as $index => $rout ){ ?>
                    <option value="<?= $rout['id'] ?>"><?= $rout['route'] ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="form-group col-md-5 col-lg-3 col-sm-6 logAction">
            <label for="date">Select a date</label>
            <input id="myLocalDate" class="fil-input form-control valuemap" type="datetime-local" name="date">
        </div>
    </div>
    <div id="map"></div>
</body>
<style>
    body, #map {
        width: 100%; height: 100%; padding: 0; margin: 0;
    }
</style>
