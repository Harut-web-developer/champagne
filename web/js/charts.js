$(document).ready(function () {

    //
    // $(window).on('load', function (){
    //     let byDate = 1;
    //     let csrfToken = $('meta[name="csrf-token"]').attr("content");
    //     $.ajax({
    //         url:'/dashboard/index',
    //         method:'post',
    //         datatype:'json',
    //         data:{
    //             byDate:byDate,
    //             _crsf:csrfToken
    //         },
    //         success:function (data){
    //             let pars = JSON.parse(data);
    //             if (pars.payment[0].payment != null){
    //                 $('.orders_pay').text('֏' + ' ' + pars.payment[0].payment)
    //             }else {
    //                 $('.orders_pay').text('֏' + ' ' + 0)
    //             }
    //             if (pars.sale[0].sale != null){
    //                 $('.orders_sale').text('֏' + ' ' + pars.sale[0].sale)
    //             }else {
    //                 $('.orders_sale').text('֏' + ' ' + 0)
    //             }
    //             if (pars.deal[0].deal != null){
    //                 $('.orders_deal').text('֏' + ' ' + pars.deal[0].deal)
    //             }else {
    //                 $('.orders_deal').text('֏' + ' ' + 0)
    //             }
    //             $('.orders_profit').text('֏' + ' ' + pars.cost)
    //
    //         }
    //     })
    //
    // })
    // $('body').on('change','#byDate', function () {
    //      let byDate = $(this).val();
    //     let csrfToken = $('meta[name="csrf-token"]').attr("content");
    //     $.ajax({
    //         url:'/dashboard/mini-chart',
    //         method:'post',
    //         datatype:'json',
    //         data:{
    //             byDate:byDate,
    //             _crsf:csrfToken
    //         },
    //         success:function (data){
    //
    //         }
    //     })
    // })

})