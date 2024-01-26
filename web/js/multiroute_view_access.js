function init () {
    $('#routeSelect, #myLocalDate').on('change', function() {
        var location_value = $('#routeSelect').val();
        var date = $("#myLocalDate").val();
        var csrfToken = $('meta[name="csrf-token"]').attr("content");
        $.ajax({
            url:"/map/location-value",
            method: 'get',
            dataType:'json',
            data:{
                locationvalue: location_value,
                date:date,
                _csrf:csrfToken,
            },
            success:function(data){
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
                    var geolocation = ymaps.geolocation,
                        myMap = new ymaps.Map('map', {
                            center: [40.2100725, 44.4987508],
                            zoom: 8
                        }, {
                            searchControlProvider: 'yandex#search'
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

                    setInterval(function () {
                        var myLatitude = 40;
                        var myLongitude = 44;
                        geolocation.get({
                            provider: 'yandex',
                            mapStateAutoApply: true
                        }).then(function (result) {
                            result.geoObjects.options.set('preset', 'islands#redCircleIcon');
                            result.geoObjects.get(0).properties.set({
                                balloonContentBody: 'Мое местоположение'
                            });
                            myMap.geoObjects.add(result.geoObjects);
                        });
                        geolocation.get({
                            provider: 'browser',
                            mapStateAutoApply: true
                        }).then(function (result) {
                            myLatitude = result.geoObjects.get(0).geometry.getCoordinates()[0];
                            myLongitude = result.geoObjects.get(0).geometry.getCoordinates()[1];
                            result.geoObjects.options.set('preset', 'islands#blueCircleIcon');
                            myMap.geoObjects.add(result.geoObjects);
                            var csrfToken = $('meta[name="csrf-token"]').attr("content");
                            $.ajax({
                                url: "/map/coordinates-user",
                                method: 'post',
                                dataType: 'json',
                                data: {
                                    myLatitude: myLatitude,
                                    myLongitude: myLongitude,
                                    _csrf: csrfToken,
                                },
                            });
                        });
                    }, 60 * 1000);
                }
            }
        })
    });
}
ymaps.ready(init);