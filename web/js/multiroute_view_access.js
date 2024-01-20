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
                    // setInterval(function () {
                        var geolocation = ymaps.geolocation,
                            myMap = new ymaps.Map('map', {
                                center: [55, 34],
                                zoom: 10
                            }, {
                                searchControlProvider: 'yandex#search'
                            });
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
                        console.log(result)
                        result.geoObjects.options.set('preset', 'islands#blueCircleIcon');
                        myMap.geoObjects.add(result.geoObjects);
                    }, function (err) {
                        console.log(err);
                    });

                }
            }
        })
    });
}

ymaps.ready(init);












//function init () {
//     // Создаем карту.
//
//     $('#routeSelect, #myLocalDate').on('change', function() {
//         var location_value = $('#routeSelect').val();
//         var date = $("#myLocalDate").val();
//         var csrfToken = $('meta[name="csrf-token"]').attr("content");
//         $.ajax({
//             url:"/map/location-value",
//             method: 'get',
//             dataType:'json',
//             data:{
//                 locationvalue: location_value,
//                 date:date,
//                 _csrf:csrfToken,
//             },
//             success:function(data){
//                 if (data['location']) {
//                     var arr = [];
//                     for (var i = 0; i < data['location'].length; i++) {
//                         arr.push(data['location'][i]['location']);
//                     }
//                     arr.unshift(data['warehouse']['location']);
//                     var multiRoute = new ymaps.multiRouter.MultiRoute({
//                         referencePoints: arr,
//                         params: {
//                             routingMode: 'masstransit',
//                         }
//                     });
//                     $('#map').html('');
//                     var myMap = new ymaps.Map('map', {
//                         center: [40.2100725, 44.4987508],
//                         zoom: 8,
//                         controls: []
//                     }, {
//                         buttonMaxWidth: 300
//                     });
//                     ymaps.modules.require([
//                         'MultiRouteColorizer'
//                     ], function (MultiRouteColorizer) {
//                         new MultiRouteColorizer(multiRoute);
//                     });
//
//                     myMap.geoObjects.add(multiRoute);
//                     myMap.setZoom(8, {duration: 300});
//
//
//                     setInterval(function () {
//                         var myLatitude = 40.21427467;
//                         var myLongitude = 44.4896076;
//                         function getLocation() {
//                             if (navigator.geolocation) {
//                                 navigator.geolocation.getCurrentPosition(showPosition);
//                             } else {
//                                 console.log("Geolocation is not supported by this browser.");
//                             }
//                         }
//                         function showPosition(position) {
//                             myLatitude = position.coords.latitude;
//                             myLongitude = position.coords.longitude;
//                             var userPosition = new ymaps.Placemark([myLatitude, myLongitude], {
//                                 hintContent: 'Your Location',
//                                 balloonContent: 'Your current location'
//                             });
//                             myMap.geoObjects.add(userPosition);
//                         }
//                         var csrfToken = $('meta[name="csrf-token"]').attr("content");
//                         $.ajax({
//                             url: "/map/coordinates-user",
//                             method: 'post',
//                             dataType: 'json',
//                             data: {
//                                 myLatitude: myLatitude,
//                                 myLongitude: myLongitude,
//                                 _csrf: csrfToken,
//                             },
//                         });
//                         getLocation();
//                     }, 1000000);
//                 }
//             }
//         })
//     });
// }
//
// ymaps.ready(init);