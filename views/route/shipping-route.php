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
                    <option value="<?= $user['id'] ?>"><?= $user['name']?></option>
                <?php } ?>
            </select>
        </div>
        <div class="form-group col-md-6 col-lg-6 col-sm-6 logAction">
            <label for="myDate">Ընտրել ամսաթիվը</label>
            <input id="myDate" class="fil-input form-control valuemap" type="date" name="date">
        </div>
    </div>
</div>
<div id="map">

</div>

</body>
<script>
    function init () {
        $('#userSelect, #myDate').on('change', function() {
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
                    if (data['location'].length != 0) {
                        var clients = [];
                        var time_visit = {};
                        $('#map').html('');
                        var myMap = new ymaps.Map('map', {
                            center: [40.2100725, 44.4987508],
                            zoom: 11,
                            controls: []
                        }, {
                            buttonMaxWidth: 300
                        }, {
                            searchControlProvider: 'yandex#search'
                        });
                        // arr.unshift(data['warehouse']['location']);
                        for (let i = 0; i < data['location'].length; i++) {
                            var arr = [];
                            if (i == 0){
                                arr.unshift(data['warehouse']['location']);
                            }
                            for(var j = 0; j < data['location'][i].length; j++){
                                if(arr.length == 0 && i != 0){
                                    arr.push(data['location'][i-1][19]['location']);
                                }
                                arr.push(data['location'][i][j]['location']);
                            }
                            var multiRoute = new ymaps.multiRouter.MultiRoute({
                                referencePoints: arr,
                                type: 'viaPoint',
                                params: {
                                    routingMode: 'masstransit',
                                },
                            });
                            ymaps.modules.require([
                                'MultiRouteColorizer'
                            ], function (MultiRouteColorizer) {
                                new MultiRouteColorizer(multiRoute);
                            });
                            myMap.geoObjects.add(multiRoute);
                        }
                        for (var i = 0; i < data['clients_locations'].length; i++) {
                            clients.push(data['clients_locations'][i]['location']);
                        }
                        // arr.unshift(data['warehouse']['location']);
                        function getDistanceFromLatLonInKm(lat1,lon1,lat2,lon2) {
                            var R = 6371; // Radius of the earth in km
                            var dLat = deg2rad(lat2-lat1);  // deg2rad below
                            var dLon = deg2rad(lon2-lon1);
                            var a =
                                Math.sin(dLat/2) * Math.sin(dLat/2) +
                                Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
                                Math.sin(dLon/2) * Math.sin(dLon/2)
                            ;
                            var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                            var d = R * c * 1000; // Distance in metr
                            return d;
                        }
                        for (var y = 0; y < clients.length; y++) {
                            var distance = 0;  //heravorutyun
                            var f = 0;
                            var temp = 0;
                            var lat_long = clients[y];   // klienti koordinat
                            var coordinates = lat_long.split(',');
                            var latitude1 = parseFloat(coordinates[0]);
                            var longitude2 = parseFloat(coordinates[1]);
                            var visit = 0;
                            for (var d = 0; d < data['coordinatesUser'].length; d++) {
                                var latitude = data['coordinatesUser'][d]['latitude'];
                                var longitude = data['coordinatesUser'][d]['longitude'];
                                distance = getDistanceFromLatLonInKm(latitude, longitude, latitude1, longitude2).toFixed(1);
                                if (distance < 300) {
                                    var csrfToken = $('meta[name="csrf-token"]').attr("content");
                                    var coord_id = data['coordinatesUser'][d]['id'];
                                    visit++;
                                    temp = visit;
                                    $.ajax({
                                        url: "/route/update-visit",
                                        method: 'get',
                                        dataType: 'json',
                                        data: {
                                            visit:visit,
                                            coord_id:coord_id,
                                            _csrf:csrfToken,
                                        },
                                    })
                                }
                                if( d == (data['coordinatesUser'].length -1 ) ){
                                    if (temp >= visit){
                                        time_visit[String(y)] = {
                                            time: temp, //qani vor 60 varkyan mek en koordinatnern galis
                                            name: data['clients_locations'][y].name,
                                            location: data['clients_locations'][y].location
                                        };
                                    }else {
                                        time_visit[String(y)] = {
                                            time: visit, //qani vor 60 varkyan mek en koordinatnern galis
                                            name: data['clients_locations'][y].name,
                                            location: data['clients_locations'][y].location
                                        };
                                    }
                                }
                            }
                            $.ajax({
                                url: "/route/delete-all-visit",
                                method: 'get',
                                dataType: 'json',
                                data: {
                                    _csrf:csrfToken,
                                },
                            })
                        }
                        for (let i = 0; i < data['coordinatesUserCopy'].length; i++) {
                            var arr2 = [];
                            for(var j = 0; j < data['coordinatesUserCopy'][i].length; j++){
                                if(arr2.length == 0 && i != 0){
                                    arr2.push(data['coordinatesUserCopy'][i-1][19]['latitude'] + ',' + data['coordinatesUserCopy'][i-1][19]['longitude']);
                                }
                                arr2.push(data['coordinatesUserCopy'][i][j]['latitude'] + ',' + data['coordinatesUserCopy'][i][j]['longitude']);
                            }
                            var multiRoute2 = new ymaps.multiRouter.MultiRoute({
                                referencePoints: arr2,
                                type: 'viaPoint',
                                params: {
                                    routingMode: 'masstransit',
                                },
                            });
                            ymaps.modules.require([
                                'MultiRouteColorizer'
                            ], function (MultiRouteColorizer) {
                                new MultiRouteColorizer(multiRoute2);
                            });
                            myMap.geoObjects.add(multiRoute2);
                        }
                        function deg2rad(deg) {
                            return deg * (Math.PI/180)
                        }
                        Object.values(time_visit).forEach(item => {
                            let [latitude, longitude] = item.location.split(',').map(parseFloat);
                            let switchedLocation = [latitude, longitude];
                            var myPlacemark = new ymaps.Placemark(switchedLocation, {
                                balloonContentHeader: item.name,
                                balloonContentBody: `Կանգառը ՝ <em>${item.time}</em> րոպե։`,
                                balloonContentFooter: "Առաքված",
                                hintContent: "Առաքման կարգավիճակ"
                            });
                            myMap.geoObjects.add(myPlacemark);
                            myPlacemark.balloon.open();
                            myPlacemark.hint.open();
                        });
                        myMap.setZoom(11, { duration: 300 });
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
