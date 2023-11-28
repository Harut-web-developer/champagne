function init () {
    // Создаем карту.

    $('#valuemap').change(function () {
        var location_value = $(this).val();
        var csrfToken = $('meta[name="csrf-token"]').attr("content");
        $.ajax({
            url:"/map/location-value",
            method: 'get',
            dataType:'json',
            data:{
                locationvalue: location_value,
                _csrf:csrfToken,
            },
            success:function(data){
                console.log(data)
                var arr = [];
                for (var i=0; i<data.length;i++)
                {
                    arr[i] = [data[i]['location']] + ",";
                    console.log(arr[i])
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