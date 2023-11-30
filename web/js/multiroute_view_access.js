function init () {
    // Создаем карту.

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
                var arr = [];
                for (var i = 0; i < data['location'].length; i++) {
                    arr.push(data['location'][i]['location']);
                }
                arr.unshift(data['warehouse']['location']);
                for (var j=1; i<arr.length;i++)
                {
                    console.log(arr[j]);
                }
                var multiRoute = new ymaps.multiRouter.MultiRoute({
                    referencePoints: arr,
                    params: {
                        routingMode: 'masstransit',
                    }
                });
                $('#map').html('');
                var myMap = new ymaps.Map('map', {
                    center: [40.2100725,44.4987508],
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

                // Добавляем мультимаршрут на карту.
                myMap.geoObjects.add(multiRoute);
                myMap.setZoom(8, {duration: 300});
            }
        })
    });
}

ymaps.ready(init);