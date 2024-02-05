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

                    for (var i = 0; i < data['location'].length; i++) {
                        var arr = [];
                        for(var j = 0; j < data['location'][i].length; j++){
                            if(arr.length == 0 && i != 0){
                                arr.push(data['location'][i-1][19]['location']);
                            };
                            arr.push(data['location'][i][j]['location']);
                        }
                        var multiRoute = new ymaps.multiRouter.MultiRoute({
                            referencePoints: arr,
                            type: 'viaPoint',
                        });
                        
                        ymaps.modules.require([
                            'MultiRouteColorizer'
                        ], function (MultiRouteColorizer) {
                            new MultiRouteColorizer(multiRoute);
                        });

                        myMap.geoObjects.add(multiRoute);
                       
                    }
                    var start = data['location'][0]['location'];
                    arr.unshift(data['warehouse']['location']);

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
                                    route_id: location_value,
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