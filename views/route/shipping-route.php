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
    <div class="row">
        <h1 id="routeSelect" class="valuemap" value="<?= $id ?>">
            <?= $route["route"] ?>
        </h1>
        <div class="form-group col-md-6 col-lg-6 col-sm-6 loguser">
            <label for="userSelect">Օգտագործող</label>
            <select id="userSelect" class="form-select form-control valueuser" aria-label="Default select example">
                <option value="">Ընտրել օգտագործողին</option>
                <?php foreach ($users as $index => $user ){ ?>
                    <option value="<?= $user['id'] ?>"><?= $user['name'] . ' ' . $user['username']?></option>
                <?php } ?>
            </select>
        </div>
        <div class="form-group col-md-6 col-lg-6 col-sm-6 logAction">
            <label for="myDate">Ընտրել ամսաթիվը</label>
            <input id="myDate" class="fil-input form-control valuemap" type="datetime-local" name="date">
        </div>
    </div>
</div>
<div id="map">

</div>

</body>
<script>
    function init () {
        $('#myDate').on('change', function() {
            var date = $('#myDate').val();
            var user = $('#userSelect').val();
            let url_id = window.location.href;
            let url = new URL(url_id);
            let urlId = url.searchParams.get("id");
            var csrfToken = $('meta[name="csrf-token"]').attr("content");
            $.ajax({
                url:"/route/location-value",
                method: 'get',
                dataType:'json',
                data:{
                    date:date,
                    user:user,
                    urlId:urlId,
                    _csrf:csrfToken,
                },
                success:function(data){
                    console.log(data)
                    if (data['location'].length != 0) {
                        var arr = [];
                        for (var i = 0; i < data['location'].length; i++) {
                            arr.push(data['location'][i]['location']);
                        }
                        arr.unshift(data['warehouse']['location']);
                        var multiRoute = new ymaps.multiRouter.MultiRoute({
                            referencePoints: arr,
                            params: {
                                routingMode: 'masstransit',
                            }
                        });
                        $('#map').html('');
                        var myMap = new ymaps.Map('map', {
                            center: [40.2100725, 44.4987508],
                            zoom: 8,
                            controls: []
                        }, {
                            buttonMaxWidth: 300
                        });

                        ymaps.modules.require([
                            'MultiRouteColorizer'
                        ], function (MultiRouteColorizer) {
                            new MultiRouteColorizer(multiRoute);
                        });

                        myMap.geoObjects.add(multiRoute);
                        myMap.setZoom(8, {duration: 300});
                    }
                }
            })
        });
    }

    ymaps.ready(init);
</script>
<style>
    body, #map {
        width: 100%; height: 100%; padding: 0; margin: 0;
    }
</style>
