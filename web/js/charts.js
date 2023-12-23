$(document).ready(function () {
    $('body').on('change','#byDate', function () {
         let byDate = $(this).val();
        let csrfToken = $('meta[name="csrf-token"]').attr("content");
        $.ajax({
            url:'/dashboard/mini-chart',
            method:'post',
            datatype:'json',
            data:{
                byDate:byDate,
                _crsf:csrfToken
            },
            success:function (data){

            }
        })
    })

})