function init () {
    var csrfToken = $('meta[name="csrf-token"]').attr("content");
    $.ajax({
        url:"/clients/clients-location",
        method: 'post',
        dataType:'json',
        data:{
            _csrf:csrfToken
        },
        success:function(data){
            var arr = [];
            for (var i=0; i<data.length;i++)
            {
                    arr[i] = [data[i]['location']];
                // console.log(typeof(data[i]['location']))
            }
            for (var j=0; j<data.length;j++)
            {
                console.log(arr[j])
            }
            // Создаем мультимаршрут.
            var multiRoute = new ymaps.multiRouter.MultiRoute({

                referencePoints: arr,
                params: {
                    routingMode: 'masstransit'
                }
            });

            ymaps.modules.require([
                'MultiRouteColorizer'
            ], function (MultiRouteColorizer) {
                // Создаем объект, раскрашивающий линии сегментов маршрута.
                new MultiRouteColorizer(multiRoute);
            });

            // Создаем карту.
            var myMap = new ymaps.Map('map', {
                center: [40.2100725,44.4987508],
                zoom: 2,
                controls: []
            }, {
                buttonMaxWidth: 300
            });

            // Добавляем мультимаршрут на карту.
            myMap.geoObjects.add(multiRoute);
        }
    })

}

ymaps.ready(init);