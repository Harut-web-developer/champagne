$(document).ready(function () {
    var id_count = {};
    var warehouse_id = $('body').find('.warehouse_id').val();
    $('body').on('change','.warehouse_id',function () {
        warehouse_id = $(this).val();
    })
    $('body').on('input', '.ordersCountInput', function () {
        count_id($(this));
    });
    function count_id(el) {
        let id = el.closest('tr').find('.prodId').data('id');
        let count = el.val();
        if (count) {
            id_count[String(id).trim()] = parseInt(count.trim());
        }else{
            delete id_count[String(id).trim()];
        }
    }
    var count_product = {};
    function product_count() {
        $('.ordersAddingTable .tableNomenclature .nom_Id').each(function() {
            let nomIdValue = $(this).val();
            let countProductValue = $(this).closest('tr').find('.countProduct').val();
            let discountProductValue = $(this).closest('tr').find('input[name="discount[]"]').val();
            let beforePriceProductValue = $(this).closest('tr').find('input[name="beforePrice[]"]').val();
            let priceProductValue = $(this).closest('tr').find('input[name="price[]"]').val();
            if (!count_product[nomIdValue]) {
                count_product[nomIdValue] = {};
            }
            count_product[nomIdValue]['count'] = countProductValue;
            count_product[nomIdValue]['discount'] = discountProductValue;
            count_product[nomIdValue]['beforePrice'] = beforePriceProductValue;
            count_product[nomIdValue]['price'] = priceProductValue;
        });
        var ordersTotalPriceSum = 0;
        var ordersTotalCount = 0;
        var ordersBeforTotalPriceSum = 0;
        var totalDiscount = 0;
        for(let i in count_product){
            ordersTotalPriceSum += parseFloat(count_product[i].price * count_product[i].count);     //yndhanur zexchvac gumar
            ordersTotalCount += parseFloat(count_product[i].count);
            ordersBeforTotalPriceSum += parseFloat(count_product[i].beforePrice * count_product[i].count);  //yndhanur gumar
            totalDiscount += parseFloat(count_product[i].discount * count_product[i].count);
        }
        // console.log(count_product)
        return {
            count_product: count_product,
            ordersBeforTotalPriceSum: ordersBeforTotalPriceSum,
            ordersTotalPriceSum: ordersTotalPriceSum,
            ordersTotalCount: ordersTotalCount,
            totalDiscount: totalDiscount
        };
    }

    $('.js-example-basic-single').select2();
    $('body').on('change','#orders-orders_date, #singleClients',function(){
        if($('#orders-orders_date').val() != '' && $('#singleClients').val() != ''){
            $('body').find('.addOrders').attr('disabled',false);
        }
    })

    $('body').on('click', '.addOrders_get_warh_id', function (e) {
        var csrfToken = $('meta[name="csrf-token"]').attr("content");
        $.ajax({
            url:'/orders/get-nomiclature',
            method:'post',
            datatype:'html',
            data:{
                warehouse_id:warehouse_id,
                id_count:id_count,
                csrfToken:csrfToken,
            },
            success:function(data){
                $('#ajax_content').html(data);
            }
        })
    })

    $('body').on('click', '.addOrders_get_warh_id_update', function (e) {
        var csrfToken = $('meta[name="csrf-token"]').attr("content");
        let url_id = window.location.href;
        let url = new URL(url_id);
        let urlId = url.searchParams.get("id");
        $.ajax({
            url:'/orders/get-nomiclature-update',
            method:'post',
            datatype:'html',
            data:{
                warehouse_id:warehouse_id,
                id_count:id_count,
                urlId: urlId,
                csrfToken:csrfToken,
            },
            success:function(data){
                $('#ajax_content').html(data);
            }
        })
    })

    var newTbody = $('<tbody></tbody>');
    var trss = {};
    var trs = {};
    var discount_desc = [];
    $('body').on('click','.create', function (e) {
        var documentsTableBody = '';
        let n = 0;
        let clientId = $('#singleClients').val();
        let totalSum = 0;
        let countSum = 0;
        let orders_date = $('#orders-orders_date').val();
        let csrfToken = $('meta[name="csrf-token"]').attr("content");
        var discountBody = '';
        var ordersTableLength = 0;
        var sequenceNumber = 0;
        $('.addOrdersTableTr').each(function () {
            if ($(this).find('.ordersCountInput').val() != '') {
                countSum += parseInt($(this).children('.ordersAddCount').find('.ordersCountInput').val());
                totalSum += parseFloat($(this).children('.ordersAddCount').find('.ordersPriceInput').val()) * parseInt($(this).children('.ordersAddCount').find('.ordersCountInput').val());
                ordersTableLength++;
            }
        })
        $('.addOrdersTableTr').each(function () {
            if ($(this).find('.ordersCountInput').val() != '') {
                let id = $(this).find(".prodId").attr('data-id');
                 let nomenclature_id = $(this).find('.nomId').data('product');
                 let name = $(this).children(".nomenclatureName").text();
                 let count = $(this).children('.ordersAddCount').find('.ordersCountInput').val();
                 let price = $(this).children('.ordersAddCount').find('.ordersPriceInput').val();
                 let cost = $(this).children('.ordersAddCount').find('.ordersCostInput').val();

                $.ajax({
                    url:'/orders/get-discount',
                    method:'post',
                    datatype:'json',
                    data:{
                        clientId:clientId,
                        product_id: id,
                        nomenclature_id:nomenclature_id,
                        name:name,
                        count:count,
                        price:price,
                        cost:cost,
                        orders_date:orders_date,
                        totalSum:totalSum,
                        countSum:countSum,
                        _csrf:csrfToken
                    },
                    success:function (data) {
                        let pars = JSON.parse(data);
                        if (pars.discount_desc != undefined){
                            discount_desc.push(pars.discount_desc);
                        }
                        let  prod_clients = '';
                        if (pars.discount_client_id_check.length == 1 && pars.discount_client_id_check[0] == 'empty'){
                            prod_clients = '<input type="hidden" class="discount_client_id" name="discount_client_id_check[empty]" value="empty">';
                        }else {
                            for (let c = 0; c < pars.discount_client_id_check.length; c++){
                                if (pars.discount_client_id_check[c] != 'empty'){
                                    prod_clients += '<input type="hidden" class="discount_client_id" name="discount_client_id_check['+pars.discount_client_id_check[c].id+']" value="'+pars.discount_client_id_check[c].clients_id+'">';
                                }
                            }

                        }
                        sequenceNumber++;
                        trss[pars.product_id] = `<tr class="tableNomenclature">
                                                     <td>
                                                        <span>`+sequenceNumber+`</span>
                                                        <input type="hidden" name="order_items[]" value="`+pars.product_id+`">
                                                        <input class="prodId" type="hidden" name="product_id[]" value="`+pars.product_id+`">
                                                        <input class="nom_Id" type="hidden" name="nom_id[]" value="`+pars.nomenclature_id+`">
                                                        <input type="hidden" name="count_discount_id[]" value="`+pars.count_discount_id+`">
                                                        `+prod_clients+`
                                                        <input type="hidden" name="cost[]" value="`+pars.cost+`">
                                                     </td>
                                                     <td  class="name">`+pars.name+`</td>
                                                     <td class="count">
                                                        <input type="number" name="count_[]" value="`+pars.count+`" class="form-control countProduct">
                                                     </td>
                                                     <td class="discount">
                                                        <span>`+parseFloat(pars.discount).toFixed(2)+`</span>
                                                        <input type="hidden" name="discount[]" value="`+parseFloat(pars.discount).toFixed(2)+`">
                                                     </td>
                                                     <td class="beforePrice">
                                                        <span>`+parseFloat(pars.format_before_price).toFixed(2)+`</span>
                                                        <input type="hidden" name="beforePrice[]" value="`+parseFloat(pars.format_before_price).toFixed(2)+`">
                                                     </td>
                                                     <td class="price">
                                                        <span>`+parseFloat(pars.price).toFixed(2)+`</span>
                                                        <input type="hidden" name="price[]" value="`+parseFloat(pars.price).toFixed(2)+`">
                                                     </td>
                                                     <td class="totalBeforePrice">
                                                        <span>`+parseFloat(pars.format_before_price * pars.count).toFixed(2)+`</span>
                                                        <input type="hidden" name="total_before_price[]" value="`+parseFloat(pars.format_before_price * pars.count).toFixed(2)+`">
                                                     </td>
                                                     <td class="totalPrice">
                                                        <span>`+parseFloat(pars.price * pars.count).toFixed(2)+`</span>
                                                        <input type="hidden" name="total_price[]" value="`+parseFloat(pars.price * pars.count).toFixed(2)+`">
                                                     </td>
                                                     <td><button  type="button" class="btn rounded-pill btn-outline-danger deleteItems">Ջնջել</button></td>
                                                 </tr>`;

                        ordersTableLength--;
                        if(ordersTableLength == 0){
                            // console.log(discount_desc)
                            $('.discountDesc tbody').html('');
                            let uniquePairs = discount_desc
                                .flat()
                                .filter(item => item.id !== 'empty')
                                .reduce((result, item) => {
                                    let existingPair = result.find(pair => pair[0] === item.id);
                                    if (existingPair) {
                                        if (item.discount > existingPair[2]) {
                                            existingPair[1] = item.name;
                                            existingPair[2] = item.discount;
                                            existingPair[3] = item.type;
                                        }
                                    } else {
                                        result.push([item.id, item.name, item.discount, item.type]);
                                    }
                                    return result;
                                }, [])
                                .filter(pair => pair.every(value => value !== undefined));

                            uniquePairs.forEach((item,index) => {
                                discountBody += `<tr>
                                                     <td>`+(parseInt(index) + 1) +`</td>
                                                     <td>`+item[1]+`</td>
                                                     <td>`+(item[3] == 'percent' ? item[2] + ' %' : item[2] + ' դր․')+`</td>
                                                </tr>`
                            })
                            $('.discountDesc tbody').parent().append(discountBody);
                            for (let i in trss) {
                                if(trss[i] != ''){
                                    newTbody.append(trss[i]);
                                }
                            }
                            $('.ordersAddingTable tbody').replaceWith(newTbody);
                            trCounter($('body').find('.ordersAddingTable'));
                            newTbody = $('<tbody></tbody>');
                            var result = product_count();
                            $('body').find('#orders-total_price').val(parseFloat(result.ordersTotalPriceSum).toFixed(2));
                            $('body').find('#orders-total_count').val(Math.round(result.ordersTotalCount));
                            $('body').find('#orders-total_price_before_discount').val(parseFloat(result.ordersBeforTotalPriceSum).toFixed(2));
                            $('body').find('#orders-total_discount').val(parseFloat(result.totalDiscount).toFixed(2));
                        }
                    }
                })
            }
        })
    })
    $('body').on('keyup','.countProduct', function (){
        var result = product_count();
        if ($(this).val() < 1 || $(this).val() === ""){
            $(this).val(1)
            $(this).closest('.tableNomenclature').find('.totalPrice').children('span').text(parseFloat($(this).closest('.tableNomenclature').find('.price').children('input').val() * $(this).val()).toFixed(2))
            $(this).closest('.tableNomenclature').find('.totalPrice').children('input').val(parseFloat($(this).closest('.tableNomenclature').find('.price').children('input').val() * $(this).val()).toFixed(2))
            $(this).closest('.tableNomenclature').find('.totalBeforePrice').children('span').text(parseFloat($(this).closest('.tableNomenclature').find('.beforePrice').children('input').val() * $(this).val()).toFixed(2))
            $(this).closest('.tableNomenclature').find('.totalBeforePrice').children('input').val(parseFloat($(this).closest('.tableNomenclature').find('.beforePrice').children('input').val() * $(this).val()).toFixed(2))
            $('body').find('#orders-total_price').val(parseFloat(result.ordersTotalPriceSum).toFixed(2));
            $('body').find('#orders-total_count').val(Math.round(result.ordersTotalCount));
            $('body').find('#orders-total_price_before_discount').val(parseFloat(result.ordersBeforTotalPriceSum).toFixed(2));
            $('body').find('#orders-total_discount').val(parseFloat(result.totalDiscount).toFixed(2));
        }else {
            $(this).closest('.tableNomenclature').find('.totalPrice').children('span').text(parseFloat($(this).closest('.tableNomenclature').find('.price').children('input').val() * $(this).val()).toFixed(2))
            $(this).closest('.tableNomenclature').find('.totalPrice').children('input').val(parseFloat($(this).closest('.tableNomenclature').find('.price').children('input').val() * $(this).val()).toFixed(2))
            $(this).closest('.tableNomenclature').find('.totalBeforePrice').children('span').text(parseFloat($(this).closest('.tableNomenclature').find('.beforePrice').children('input').val() * $(this).val()).toFixed(2))
            $(this).closest('.tableNomenclature').find('.totalBeforePrice').children('input').val(parseFloat($(this).closest('.tableNomenclature').find('.beforePrice').children('input').val() * $(this).val()).toFixed(2))
            $('body').find('#orders-total_price').val(parseFloat(result.ordersTotalPriceSum).toFixed(2));
            $('body').find('#orders-total_count').val(Math.round(result.ordersTotalCount));
            $('body').find('#orders-total_price_before_discount').val(parseFloat(result.ordersBeforTotalPriceSum).toFixed(2));
            $('body').find('#orders-total_discount').val(parseFloat(result.totalDiscount).toFixed(2));
        }
    })
    $('body').on('click','.countProduct', function (){
        var result = product_count();
        if ($(this).val() < 1 || $(this).val() === ""){
            $(this).val(1)
            $(this).closest('.tableNomenclature').find('.totalPrice').children('span').text(parseFloat($(this).closest('.tableNomenclature').find('.price').children('input').val() * $(this).val()).toFixed(2))
            $(this).closest('.tableNomenclature').find('.totalPrice').children('input').val(parseFloat($(this).closest('.tableNomenclature').find('.price').children('input').val() * $(this).val()).toFixed(2))
            $(this).closest('.tableNomenclature').find('.totalBeforePrice').children('span').text(parseFloat($(this).closest('.tableNomenclature').find('.beforePrice').children('input').val() * $(this).val()).toFixed(2))
            $(this).closest('.tableNomenclature').find('.totalBeforePrice').children('input').val(parseFloat($(this).closest('.tableNomenclature').find('.beforePrice').children('input').val() * $(this).val()).toFixed(2))
            $('body').find('#orders-total_price').val(parseFloat(result.ordersTotalPriceSum).toFixed(2));
            $('body').find('#orders-total_count').val(Math.round(result.ordersTotalCount));
            $('body').find('#orders-total_price_before_discount').val(parseFloat(result.ordersBeforTotalPriceSum).toFixed(2));
            $('body').find('#orders-total_discount').val(parseFloat(result.totalDiscount).toFixed(2));
        }else {
            $(this).closest('.tableNomenclature').find('.totalPrice').children('span').text(parseFloat($(this).closest('.tableNomenclature').find('.price').children('input').val() * $(this).val()).toFixed(2))
            $(this).closest('.tableNomenclature').find('.totalPrice').children('input').val(parseFloat($(this).closest('.tableNomenclature').find('.price').children('input').val() * $(this).val()).toFixed(2))
            $(this).closest('.tableNomenclature').find('.totalBeforePrice').children('span').text(parseFloat($(this).closest('.tableNomenclature').find('.beforePrice').children('input').val() * $(this).val()).toFixed(2))
            $(this).closest('.tableNomenclature').find('.totalBeforePrice').children('input').val(parseFloat($(this).closest('.tableNomenclature').find('.beforePrice').children('input').val() * $(this).val()).toFixed(2))
            $('body').find('#orders-total_price').val(parseFloat(result.ordersTotalPriceSum).toFixed(2));
            $('body').find('#orders-total_count').val(Math.round(result.ordersTotalCount));
            $('body').find('#orders-total_price_before_discount').val(parseFloat(result.ordersBeforTotalPriceSum).toFixed(2));
            $('body').find('#orders-total_discount').val(parseFloat(result.totalDiscount).toFixed(2));
        }
    })
    $('body').on('click', '.deleteItems',function (){
        let confirmed =  confirm("Այս ապրանքը դուք ուզում եք ջնջե՞լ:");
        if (confirmed){
            $(this).closest('.tableNomenclature').remove();
            alert('Հաջողությամբ ջնջված է:');
            let id_delete = $(this).closest('.tableNomenclature').find('.nom_Id').val();
            if (id_delete) {
                delete count_product[id_delete];
            }
            var result = product_count();
            $('body').find('#orders-total_price').val(parseFloat(result.ordersTotalPriceSum).toFixed(2));
            $('body').find('#orders-total_count').val(Math.round(result.ordersTotalCount));
            $('body').find('#orders-total_price_before_discount').val(parseFloat(result.ordersBeforTotalPriceSum).toFixed(2));
            $('body').find('#orders-total_discount').val(parseFloat(result.totalDiscount).toFixed(2));
        }
    })

    var old_table = $('.tableNomenclature').closest('tbody').html();
    var old_attrs = {};
    $('body').on('input', '.ordersAddingTable td input', function () {
        // let el = $(this);
        // let prod_id = $(this).closest('tr').find('.prodId').val(); // iitem_id
        let nom_id = $(this).closest('tr').find('.nomId').val(); // iitem_id
        let count = $(this).closest('tr').find('.ordersCountInput').val(); // iitem_id
        let price = $(this).closest('tr').find('.ordersPriceInput').val(); // iitem_id
        old_attrs[nom_id]= { count:count , price:price};
        // console.log("oldvalues",old_attrs)
    })
    var allValues = [];

    // const newtbody = $('.tableNomenclature').closest('tbody').html();
    $('body').on('click','.update', function(){
        var documentsTableBody = '';
        let fromModal = '';
        var discount_name = [];
        let clientId = $('#singleClients').val();
        let orders_date = $('#orders-orders_date').val();
        let csrfToken = $('meta[name="csrf-token"]').attr("content");
        var totalSum = 0;
        var countSum = 0;
        var ordersTableLength = 0;
        let acordingNumber = parseInt($('.fromDB').last().find('.acordingNumber').text());
        $('.addOrdersTableTr').each(function () {
            if ($(this).find('.ordersCountInput').val() != '') {
                countSum += parseInt($(this).children('.ordersAddCount').find('.ordersCountInput').val());
                totalSum += parseFloat($(this).children('.ordersAddCount').find('.ordersPriceInput').val()) * parseInt($(this).children('.ordersAddCount').find('.ordersCountInput').val());
                ordersTableLength++;
            }
        })
        $('.addOrdersTableTr').each(function () {
            if ($(this).find('.ordersCountInput').val() != '') {
                let id = $(this).find(".prodId").attr('data-id');
                let nomenclature_id = $(this).find('.nomId').data('product');
                let name = $(this).children(".nomenclatureName").text();
                let count = $(this).children('.ordersAddCount').find('.ordersCountInput').val();
                let price = $(this).children('.ordersAddCount').find('.ordersPriceInput').val();
                let cost = $(this).children('.ordersAddCount').find('.ordersCostInput').val();

                $.ajax({
                    url:'/orders/get-discount',
                    method:'post',
                    datatype:'json',
                    data:{
                        clientId:clientId,
                        product_id: id,
                        nomenclature_id:nomenclature_id,
                        name:name,
                        count:count,
                        price:price,
                        cost:cost,
                        orders_date:orders_date,
                        totalSum:totalSum,
                        countSum:countSum,
                        _csrf:csrfToken
                    },
                    success:function (data) {
                        let addOrdersTableBody = '';
                        let pars = JSON.parse(data);
                        if (pars.discount_name != undefined){
                            discount_name.push(pars.discount_name);
                        }
                        if (pars.discount_desc != undefined){
                            discount_desc.push(pars.discount_desc);
                        }
                        let  prod_clients = '';
                        if (pars.discount_client_id_check.length == 1 && pars.discount_client_id_check[0] == 'empty'){
                            prod_clients = '<input type="hidden" class="discount_client_id" name="discount_client_id_check[empty]" value="empty">';
                        }else {
                            for (let c = 0; c < pars.discount_client_id_check.length; c++){
                                if (pars.discount_client_id_check[c] != 'empty'){
                                    prod_clients += '<input type="hidden" class="discount_client_id" name="discount_client_id_check['+pars.discount_client_id_check[c].id+']" value="'+pars.discount_client_id_check[c].clients_id+'">';
                                }
                            }
                        }
                        // console.log(discount_desc)
                        acordingNumber++
                        trss[id.trim()] = `<tr class="tableNomenclature">
                                     <td>
                                        <span>`+acordingNumber+`</span>
                                        <input type="hidden" name="order_items[]" value="null">
                                        <input class="prodId" type="hidden" name="product_id[]" value="`+pars.product_id+`">
                                        <input class="nomId"  type="hidden" name="nom_id[]" value="`+pars.nomenclature_id+`">
                                        <input class="countDiscountId" type="hidden" name="count_discount_id[]" value="`+pars.count_discount_id+`">
                                        `+prod_clients+`
                                        <input type="hidden" name="cost[]" value="`+pars.cost+`">
                                     </td>
                                     <td  class="name">`+pars.name+`</td>
                                     <td class="count">
                                        <input type="number" name="count_[]" value="`+pars.count+`" class="form-control countProductForUpdate">
                                     </td>
                                     <td class="discount">
                                        <span>`+parseFloat(pars.discount).toFixed(2)+`</span>
                                        <input type="hidden" name="discount[]" value="`+parseFloat(pars.discount).toFixed(2)+`">
                                     </td>
                                     <td class="beforePrice">
                                        <span>`+parseFloat(pars.format_before_price).toFixed(2)+`</span>
                                        <input type="hidden" name="beforePrice[]" value="`+parseFloat(pars.format_before_price).toFixed(2)+`">
                                     </td>
                                     <td class="price">
                                        <span>`+parseFloat(pars.price).toFixed(2)+`</span>
                                        <input type="hidden" name="price[]" value="`+parseFloat(pars.price).toFixed(2)+`">
                                     </td>
                                     <td class="totalBeforePrice">
                                        <span>`+parseFloat(pars.format_before_price * pars.count).toFixed(2)+`</span>
                                        <input type="hidden" name="total_before_price[]" value="`+parseFloat(pars.format_before_price * pars.count).toFixed(2)+`">
                                     </td>
                                     <td class="totalPrice">
                                        <span>`+parseFloat(pars.price * pars.count).toFixed(2)+`</span>
                                        <input type="hidden" name="total_price[]" value="`+parseFloat(pars.price * pars.count).toFixed(2)+`">
                                     </td>
                                     <td><button  type="button" class="btn rounded-pill btn-outline-danger deleteUpdateItems">Ջնջել</button></td>
                                 </tr>`.trim()
                        ordersTableLength--;
                        if(ordersTableLength == 0) {
                            $('.discountDesc tbody').html('');
                            for (let i in trss) {
                                if(trss[i] != ''){
                                    fromModal += trss[i];
                                }
                            }
                            $('.ordersAddingTable tbody').html('');
                            $('.ordersAddingTable tbody').html(old_table);
                            $('.ordersAddingTable tbody').append(fromModal);
                            trCounter($('body').find('.ordersAddingTable'));

                            let ordersTotalCount = 0;
                            let ordersTotalPriceSum = 0;
                            let ordersBeforTotalPriceSum = 0;
                            let totalDiscount = 0;
                            $('.tableNomenclature').each(function () {
                                    ordersTotalPriceSum += parseFloat($(this).find('.totalPrice').children('input').val());
                                    ordersTotalCount += parseInt($(this).find('.count').children('input').val());
                                    ordersBeforTotalPriceSum += parseFloat($(this).find('.totalBeforePrice').children('input').val());
                                    totalDiscount += parseFloat($(this).find('.discount').children('input').val()) * parseInt($(this).find('.count').children('input').val());
                            })
                            $('body').find('#orders-total_price').val(parseFloat(ordersTotalPriceSum).toFixed(2));
                            $('body').find('#orders-total_count').val(Math.round(ordersTotalCount));
                            $('body').find('#orders-total_price_before_discount').val(parseFloat(ordersBeforTotalPriceSum).toFixed(2));
                            $('body').find('#orders-total_discount').val(parseFloat(totalDiscount).toFixed(2));
                            $('.fromDB').each(function () {
                                let countDiscountValues = $(this).find('.countDiscountId').val().split(',');
                                countDiscountValues.forEach(function(value) {
                                    allValues.push(value.trim())
                                });
                            });
                            // console.log(discount_desc)
                            let uniquePairs = discount_desc
                                .flat()
                                .filter(item => item.id !== 'empty')
                                .reduce((result, item) => {
                                    let existingPair = result.find(pair => pair[0] === item.id);
                                    if (existingPair) {
                                        if (item.discount > existingPair[2]) {
                                            existingPair[1] = item.name;
                                            existingPair[2] = item.discount;
                                            existingPair[3] = item.type;
                                        }
                                    } else {
                                        result.push([item.id, item.name, item.discount, item.type]);
                                    }
                                    return result;
                                }, [])
                                .filter(pair => pair.every(value => value !== undefined));
                            // console.log(uniquePairs)
                            for (let s = 0; s < uniquePairs.length; s++){
                                allValues.push(uniquePairs[s][0] + '')
                            }

                            let uniqueArray = [...new Set(allValues)];

                            let convertedArray = uniqueArray.map(function(element) {
                                return isNaN(element) ? element : parseInt(element);
                            });
                            // console.log(convertedArray)

                            if (discount_name.length != 0){
                                let discount = '';
                                let k = 0;
                                for (let c = 0; c < convertedArray.length; c++){
                                    for (let b = 0; b < discount_name[0].length; b++){
                                        if (typeof convertedArray[c] == 'number'){
                                            if (convertedArray[c] == discount_name[0][b].id){
                                                k++;
                                                discount += `<tr>
                                                            <td>`+ k +`</td>
                                                            <td>`+ discount_name[0][b].name +`</td>
                                                            <td>`+(discount_name[0][b].type == 'percent' ? discount_name[0][b].discount + ' %' : discount_name[0][b].discount + ' դր․')+`</td>
                                                         </tr>`
                                            }
                                        }else {
                                            break;
                                        }
                                    }
                                }
                                $('.discountDesc tbody').append(discount)
                            }
                        }
                    }
                })
            }
        })
        giveOldValues();
    })
    function giveOldValues() {
        for (let argumentsKey in old_attrs) {
            let tr = $('body').find('#tr_'+argumentsKey);
            tr.find('.ordersAddCount').find('.ordersCountInput').val(old_attrs[argumentsKey].count);
            tr.find('.ordersAddCount').find('.ordersPriceInput').val(old_attrs[argumentsKey].price);
        }
    }

    $('body').on('click', '.by_ajax_update',function () {

        var href_ = $(this).attr('data-href');
        getNom(href_);
        let clientId = $('#singleClients').val();
        let totalSum = 0;
        let countSum = 0;
        // $('.ordersAddingTable tbody').html('');
        let orders_date = $('#orders-orders_date').val();
        let csrfToken = $('meta[name="csrf-token"]').attr("content");
        var ordersTotalPriceSum = 0;
        var ordersTotalCount = 0;
        var ordersBeforTotalPriceSum = 0;
        var totalDiscount = 0;
        var discount_desc = [];
        var discountBody = '';
        $('body').find('.loader').toggleClass('d-none');
        // $('body').find('.ordersAddingTable').addClass('d-none');
        var ordersTableLength = 0;
        var sequenceNumber = 0;
        $('.addOrdersTableTr').each(function () {
            if ($(this).find('.ordersCountInput').val() != '') {
                countSum += parseInt($(this).children('.ordersAddCount').find('.ordersCountInput').val());
                totalSum += parseFloat($(this).children('.ordersAddCount').find('.ordersPriceInput').val()) * parseInt($(this).children('.ordersAddCount').find('.ordersCountInput').val());
                ordersTableLength++;
            }
        })
        $('.addOrdersTableTr').each(function () {
            if ($(this).find('.ordersCountInput').val() != '') {
                let id = $(this).find(".prodId").attr('data-id');
                let nomenclature_id = $(this).find('.nomId').data('product');
                let name = $(this).children(".nomenclatureName").text();
                let count = $(this).children('.ordersAddCount').find('.ordersCountInput').val();
                let price = $(this).children('.ordersAddCount').find('.ordersPriceInput').val();
                let cost = $(this).children('.ordersAddCount').find('.ordersCostInput').val();
                $.ajax({
                    url:'/orders/get-discount',
                    method:'post',
                    datatype:'json',
                    data:{
                        clientId:clientId,
                        product_id: id,
                        nomenclature_id:nomenclature_id,
                        name:name,
                        count:count,
                        price:price,
                        cost:cost,
                        orders_date:orders_date,
                        totalSum:totalSum,
                        countSum:countSum,
                        _csrf:csrfToken
                    },
                    success:function (data) {
                        let pars = JSON.parse(data);
                        if (pars.discount_desc != undefined){
                            discount_desc.push(pars.discount_desc);
                        }
                        let  prod_clients = '';
                        if (pars.discount_client_id_check.length == 1 && pars.discount_client_id_check[0] == 'empty'){
                            prod_clients = '<input type="hidden" class="discount_client_id" name="discount_client_id_check[empty]" value="empty">';
                        }else {
                            for (let c = 0; c < pars.discount_client_id_check.length; c++){
                                if (pars.discount_client_id_check[c] != 'empty'){
                                    prod_clients += '<input type="hidden" class="discount_client_id" name="discount_client_id_check['+pars.discount_client_id_check[c].id+']" value="'+pars.discount_client_id_check[c].clients_id+'">';
                                }
                            }
                        }
                        sequenceNumber++;
                        ordersBeforTotalPriceSum += parseFloat(pars.format_before_price * pars.count).toFixed(2);
                        ordersTotalPriceSum += parseFloat(pars.price * pars.count).toFixed(2);
                        ordersTotalCount += pars.count;
                        totalDiscount += parseFloat(pars.discount * pars.count).toFixed(2);
                        trss[pars.product_id] = `<tr class="tableNomenclature">
                                     <td>
                                        <span>`+sequenceNumber+`</span>
                                        <input type="hidden" name="order_items[]" value="null">
                                        <input class="prodId" type="hidden" name="product_id[]" value="`+pars.product_id+`">
                                        <input type="hidden" name="nom_id[]" value="`+pars.nomenclature_id+`">
                                        <input type="hidden" name="count_discount_id[]" value="`+pars.count_discount_id+`">
                                        `+prod_clients+`
                                        <input type="hidden" name="cost[]" value="`+pars.cost+`">
                                     </td>
                                     <td  class="name">`+pars.name+`</td>
                                     <td class="count">
                                        <input type="number" name="count_[]" value="`+pars.count+`" class="form-control countProduct">
                                     </td>
                                     <td class="discount">
                                        <span>`+parseFloat(pars.discount).toFixed(2)+`</span>
                                        <input type="hidden" name="discount[]" value="`+parseFloat(pars.discount).toFixed(2)+`">
                                     </td>
                                     <td class="beforePrice">
                                        <span>`+parseFloat(pars.format_before_price).toFixed(2)+`</span>
                                        <input type="hidden" name="beforePrice[]" value="`+parseFloat(pars.format_before_price).toFixed(2)+`">
                                     </td>
                                     <td class="price">
                                        <span>`+parseFloat(pars.price).toFixed(2)+`</span>
                                        <input type="hidden" name="price[]" value="`+parseFloat(pars.price).toFixed(2)+`">
                                     </td>
                                     <td class="totalBeforePrice">
                                        <span>`+parseFloat(pars.format_before_price * pars.count).toFixed(2)+`</span>
                                        <input type="hidden" name="total_before_price[]" value="`+parseFloat(pars.format_before_price * pars.count).toFixed(2)+`">
                                     </td>
                                     <td class="totalPrice">
                                        <span>`+parseFloat(pars.price * pars.count).toFixed(2)+`</span>
                                        <input type="hidden" name="total_price[]" value="`+parseFloat(pars.price * pars.count).toFixed(2)+`">
                                     </td>
                                     <td><button  type="button" class="btn rounded-pill btn-outline-danger deleteUpdateItems">Ջնջել</button></td>
                                 </tr>`
                        // $('.ordersAddingTable tbody').parent().append(aaa);
                        ordersTableLength--;
                        if(ordersTableLength == 0){
                            let uniquePairs = discount_desc
                                .flat()
                                .filter(item => item.id !== 'empty')
                                .reduce((result, item) => {
                                    let existingPair = result.find(pair => pair[0] === item.id);
                                    if (existingPair) {
                                        if (item.discount > existingPair[2]) {
                                            existingPair[1] = item.name;
                                            existingPair[2] = item.discount;
                                            existingPair[3] = item.type;
                                        }
                                    } else {
                                        result.push([item.id, item.name, item.discount, item.type]);
                                    }
                                    return result;
                                }, [])
                                .filter(pair => pair.every(value => value !== undefined));
                            for (let s = 0; s < uniquePairs.length; s++){
                                allValues.push(uniquePairs[s][0] + '')
                            }

                            // $('body').find('.ordersAddingTable').removeClass('d-none');
                            // $('body').find('.loader').toggleClass('d-none');
                            $('body').find('#orders-total_price').val(parseFloat(ordersTotalPriceSum).toFixed(2));
                            $('body').find('#orders-total_count').val(Math.round(ordersTotalCount));
                            $('body').find('#orders-total_price_before_discount').val(parseFloat(ordersBeforTotalPriceSum).toFixed(2));
                            $('body').find('#orders-total_discount').val(parseFloat(totalDiscount).toFixed(2));
                        }
                    }
                })
            }
        })
    })

    $('body').on('keyup','.countProductForUpdate', function (){
        let ordersTotalCount = 0;
        let ordersTotalPriceSum = 0;
        let ordersBeforTotalPriceSum = 0;
        let totalDiscount = 0;
        if ($(this).val() === "" || $(this).val() < 1){
            $(this).val(1)
            $(this).closest('.tableNomenclature').find('.totalPrice').children('span').text(parseFloat($(this).closest('.tableNomenclature').find('.price').children('input').val() * $(this).val()).toFixed(2))
            $(this).closest('.tableNomenclature').find('.totalPrice').children('input').val(parseFloat($(this).closest('.tableNomenclature').find('.price').children('input').val() * $(this).val()).toFixed(2))
            $(this).closest('.tableNomenclature').find('.totalBeforePrice').children('span').text(parseFloat($(this).closest('.tableNomenclature').find('.beforePrice').children('input').val() * $(this).val()).toFixed(2))
            $(this).closest('.tableNomenclature').find('.totalBeforePrice').children('input').val(parseFloat($(this).closest('.tableNomenclature').find('.beforePrice').children('input').val() * $(this).val()).toFixed(2))
            $('.tableNomenclature').each(function () {
                ordersTotalPriceSum += parseFloat($(this).find('.totalPrice').children('input').val());
                ordersTotalCount += parseInt($(this).find('.count').children('input').val());
                ordersBeforTotalPriceSum += parseFloat($(this).find('.totalBeforePrice').children('input').val());
                totalDiscount += parseFloat($(this).find('.discount').children('input').val()) * parseInt($(this).find('.count').children('input').val());
            })
            $('body').find('#orders-total_price').val(parseFloat(ordersTotalPriceSum).toFixed(2));
            $('body').find('#orders-total_count').val(Math.round(ordersTotalCount));
            $('body').find('#orders-total_price_before_discount').val(parseFloat(ordersBeforTotalPriceSum).toFixed(2));
            $('body').find('#orders-total_discount').val(parseFloat(totalDiscount).toFixed(2));
        }else {
            var this_ = $(this);
            var id = this_.closest('.tableNomenclature').find(".nomId").val();
            var count = this_.val();
            var csrfToken = $('meta[name="csrf-token"]').attr("content");
            $.ajax({
                url: '/products/get-products',
                method: 'post',
                datatype: 'json',
                data: {
                    itemId: id,
                    count:count,
                    _csrf: csrfToken
                },
                success: function (data) {
                    let param = JSON.parse(data)
                    if (data){
                        if (param.count === 'nullable'){
                            this_.val('')
                        }else if (param.count === 'countMore'){
                            this_.val(1)
                            alert('Պահեստում նման քանակի ապրանք չկա');
                            this_.closest('.tableNomenclature').find('.totalPrice').children('span').text(parseFloat(this_.closest('.tableNomenclature').find('.price').children('input').val() * this_.val()).toFixed(2))
                            this_.closest('.tableNomenclature').find('.totalPrice').children('input').val(parseFloat(this_.closest('.tableNomenclature').find('.price').children('input').val() * this_.val()).toFixed(2))
                            this_.closest('.tableNomenclature').find('.totalBeforePrice').children('span').text(parseFloat(this_.closest('.tableNomenclature').find('.beforePrice').children('input').val() * this_.val()).toFixed(2))
                            this_.closest('.tableNomenclature').find('.totalBeforePrice').children('input').val(parseFloat(this_.closest('.tableNomenclature').find('.beforePrice').children('input').val() * this_.val()).toFixed(2))
                            $('body').find('.tableNomenclature').each(function () {
                                ordersTotalPriceSum += parseFloat($(this).find('.totalPrice').children('input').val());
                                ordersTotalCount += parseInt($(this).find('.count').children('input').val());
                                ordersBeforTotalPriceSum += parseFloat($(this).find('.totalBeforePrice').children('input').val());
                                totalDiscount += parseFloat($(this).find('.discount').children('input').val()) * parseInt($(this).find('.count').children('input').val());
                            })
                            $('body').find('#orders-total_price').val(parseFloat(ordersTotalPriceSum).toFixed(2));
                            $('body').find('#orders-total_count').val(Math.round(ordersTotalCount));
                            $('body').find('#orders-total_price_before_discount').val(parseFloat(ordersBeforTotalPriceSum).toFixed(2));
                            $('body').find('#orders-total_discount').val(parseFloat(totalDiscount).toFixed(2));

                        }else if (param.count === 'dontExists'){
                            alert('Նման ապրանք պահեստում գոյություն չունի')
                            this_.val('')
                        }
                        else if(param.count === 'exists'){
                            this_.closest('.tableNomenclature').find('.totalPrice').children('span').text(parseFloat(this_.closest('.tableNomenclature').find('.price').children('input').val() * this_.val()).toFixed(2))
                            this_.closest('.tableNomenclature').find('.totalPrice').children('input').val(parseFloat(this_.closest('.tableNomenclature').find('.price').children('input').val() * this_.val()).toFixed(2))
                            this_.closest('.tableNomenclature').find('.totalBeforePrice').children('span').text(parseFloat(this_.closest('.tableNomenclature').find('.beforePrice').children('input').val() * this_.val()).toFixed(2))
                            this_.closest('.tableNomenclature').find('.totalBeforePrice').children('input').val(parseFloat(this_.closest('.tableNomenclature').find('.beforePrice').children('input').val() * this_.val()).toFixed(2))
                            $('body').find('.tableNomenclature').each(function () {
                                ordersTotalPriceSum += parseFloat($(this).find('.totalPrice').children('input').val());
                                ordersTotalCount += parseInt($(this).find('.count').children('input').val());
                                ordersBeforTotalPriceSum += parseFloat($(this).find('.totalBeforePrice').children('input').val());
                                totalDiscount += parseFloat($(this).find('.discount').children('input').val()) * parseInt($(this).find('.count').children('input').val());
                            })
                            $('body').find('#orders-total_price').val(parseFloat(ordersTotalPriceSum).toFixed(2));
                            $('body').find('#orders-total_count').val(Math.round(ordersTotalCount));
                            $('body').find('#orders-total_price_before_discount').val(parseFloat(ordersBeforTotalPriceSum).toFixed(2));
                            $('body').find('#orders-total_discount').val(parseFloat(totalDiscount).toFixed(2));
                        }
                    }
                }
            })
        }
    })
    $('body').on('click','.countProductForUpdate', function (){
        let ordersTotalCount = 0;
        let ordersTotalPriceSum = 0;
        let ordersBeforTotalPriceSum = 0;
        let totalDiscount = 0;
        var this_ = $(this);
        if ($(this).val() === "" || $(this).val() < 1){
            $(this).val(1)
            $(this).closest('.tableNomenclature').find('.totalPrice').children('span').text(parseFloat($(this).closest('.tableNomenclature').find('.price').children('input').val() * $(this).val()).toFixed(2))
            $(this).closest('.tableNomenclature').find('.totalPrice').children('input').val(parseFloat($(this).closest('.tableNomenclature').find('.price').children('input').val() * $(this).val()).toFixed(2))
            $(this).closest('.tableNomenclature').find('.totalBeforePrice').children('span').text(parseFloat($(this).closest('.tableNomenclature').find('.beforePrice').children('input').val() * $(this).val()).toFixed(2))
            $(this).closest('.tableNomenclature').find('.totalBeforePrice').children('input').val(parseFloat($(this).closest('.tableNomenclature').find('.beforePrice').children('input').val() * $(this).val()).toFixed(2))
            $('.tableNomenclature').each(function () {
                ordersTotalPriceSum += parseFloat($(this).find('.totalPrice').children('input').val());
                ordersTotalCount += parseInt($(this).find('.count').children('input').val());
                ordersBeforTotalPriceSum += parseFloat($(this).find('.totalBeforePrice').children('input').val());
                totalDiscount += parseFloat($(this).find('.discount').children('input').val()) * parseInt($(this).find('.count').children('input').val());
            })
            $('body').find('#orders-total_price').val(parseFloat(ordersTotalPriceSum).toFixed(2));
            $('body').find('#orders-total_count').val(Math.round(ordersTotalCount));
            $('body').find('#orders-total_price_before_discount').val(parseFloat(ordersBeforTotalPriceSum).toFixed(2));
            $('body').find('#orders-total_discount').val(parseFloat(totalDiscount).toFixed(2));
        }else {
            var id = this_.closest('.tableNomenclature').find(".nomId").val();
            var count = this_.val();
            var csrfToken = $('meta[name="csrf-token"]').attr("content");
                $.ajax({
                    url: '/products/get-products',
                    method: 'post',
                    datatype: 'json',
                    data: {
                        itemId: id,
                        count:count,
                        _csrf: csrfToken
                    },
                    success: function (data) {
                        let param = JSON.parse(data)
                        if (data){
                            if (param.count === 'nullable'){
                                this_.val('')
                            }else if (param.count === 'countMore'){
                                this_.val(1)
                                alert('Պահեստում նման քանակի ապրանք չկա');
                                this_.closest('.tableNomenclature').find('.totalPrice').children('span').text(parseFloat(this_.closest('.tableNomenclature').find('.price').children('input').val() * this_.val()).toFixed(2))
                                this_.closest('.tableNomenclature').find('.totalPrice').children('input').val(parseFloat(this_.closest('.tableNomenclature').find('.price').children('input').val() * this_.val()).toFixed(2))
                                this_.closest('.tableNomenclature').find('.totalBeforePrice').children('span').text(parseFloat(this_.closest('.tableNomenclature').find('.beforePrice').children('input').val() * this_.val()).toFixed(2))
                                this_.closest('.tableNomenclature').find('.totalBeforePrice').children('input').val(parseFloat(this_.closest('.tableNomenclature').find('.beforePrice').children('input').val() * this_.val()).toFixed(2))
                                $('body').find('.tableNomenclature').each(function () {
                                    ordersTotalPriceSum += parseFloat($(this).find('.totalPrice').children('input').val());
                                    ordersTotalCount += parseInt($(this).find('.count').children('input').val());
                                    ordersBeforTotalPriceSum += parseFloat($(this).find('.totalBeforePrice').children('input').val());
                                    totalDiscount += parseFloat($(this).find('.discount').children('input').val()) * parseInt($(this).find('.count').children('input').val());
                                })
                                $('body').find('#orders-total_price').val(parseFloat(ordersTotalPriceSum).toFixed(2));
                                $('body').find('#orders-total_count').val(Math.round(ordersTotalCount));
                                $('body').find('#orders-total_price_before_discount').val(parseFloat(ordersBeforTotalPriceSum).toFixed(2));
                                $('body').find('#orders-total_discount').val(parseFloat(totalDiscount).toFixed(2));

                            }else if (param.count === 'dontExists'){
                                alert('Նման ապրանք պահեստում գոյություն չունի')
                                this_.val('')
                            }
                            else if(param.count === 'exists'){
                                this_.closest('.tableNomenclature').find('.totalPrice').children('span').text(parseFloat(this_.closest('.tableNomenclature').find('.price').children('input').val() * this_.val()).toFixed(2))
                                this_.closest('.tableNomenclature').find('.totalPrice').children('input').val(parseFloat(this_.closest('.tableNomenclature').find('.price').children('input').val() * this_.val()).toFixed(2))
                                this_.closest('.tableNomenclature').find('.totalBeforePrice').children('span').text(parseFloat(this_.closest('.tableNomenclature').find('.beforePrice').children('input').val() * this_.val()).toFixed(2))
                                this_.closest('.tableNomenclature').find('.totalBeforePrice').children('input').val(parseFloat(this_.closest('.tableNomenclature').find('.beforePrice').children('input').val() * this_.val()).toFixed(2))
                                $('body').find('.tableNomenclature').each(function () {
                                    ordersTotalPriceSum += parseFloat($(this).find('.totalPrice').children('input').val());
                                    ordersTotalCount += parseInt($(this).find('.count').children('input').val());
                                    ordersBeforTotalPriceSum += parseFloat($(this).find('.totalBeforePrice').children('input').val());
                                    totalDiscount += parseFloat($(this).find('.discount').children('input').val()) * parseInt($(this).find('.count').children('input').val());
                                })
                                $('body').find('#orders-total_price').val(parseFloat(ordersTotalPriceSum).toFixed(2));
                                $('body').find('#orders-total_count').val(Math.round(ordersTotalCount));
                                $('body').find('#orders-total_price_before_discount').val(parseFloat(ordersBeforTotalPriceSum).toFixed(2));
                                $('body').find('#orders-total_discount').val(parseFloat(totalDiscount).toFixed(2));
                            }
                        }
                    }
                })
        }
    })
    $('body').on('click', '.deleteItemsFromDB',function (){
        let confirmed =  confirm("Այս ապրանքը դուք ուզում եք ջնջե՞լ:");
        if (confirmed){
            var this_ = $(this);
            var itemId = this_.closest('.tableNomenclature').find('.orderItemsId').val();
            var nomId = this_.closest('.tableNomenclature').find('.nomId').val();
            var totalPriceBeforeDiscount = $('#orders-total_price_before_discount').val() - (parseFloat(this_.closest('tr').find('.beforePrice').find('input').val() * this_.closest('tr').find('.countProductForUpdate').val()).toFixed(2));
            var totalDiscount = $('#orders-total_discount').val() - (parseFloat(this_.closest('tr').find('.discount').find('input').val() * this_.closest('tr').find('.countProductForUpdate').val()).toFixed(2));
            var totalPrice = $('#orders-total_price').val() - (parseFloat(this_.closest('tr').find('.price').find('input').val() * this_.closest('tr').find('.countProductForUpdate').val()).toFixed(2));
            var totalCount = $('#orders-total_count').val() - (this_.closest('tr').find('.countProductForUpdate').val());
            var csrfToken = $('meta[name="csrf-token"]').attr("content");
            $.ajax({
                url:'/orders/delete-items',
                method:'post',
                datatype:'json',
                data:{
                    itemId:itemId,
                    nomId:nomId,
                    totalPrice:totalPrice,
                    totalCount:totalCount,
                    totalPriceBeforeDiscount:totalPriceBeforeDiscount,
                    totalDiscount:totalDiscount,
                    _csrf:csrfToken
                },
                success:function (data){
                    if (data === 'true'){
                        this_.closest('.tableNomenclature').remove();
                        alert('Հաջողությամբ ջնջված է:');
                        let ordersTotalCount = 0;
                        let ordersTotalPriceSum = 0;
                        let ordersBeforTotalPriceSum = 0;
                        let totalDiscount = 0;
                        $('.tableNomenclature').each(function () {
                            ordersTotalPriceSum += parseFloat($(this).find('.totalPrice').children('input').val());
                            ordersTotalCount += parseInt($(this).find('.count').children('input').val());
                            ordersBeforTotalPriceSum += parseFloat($(this).find('.totalBeforePrice').children('input').val());
                            totalDiscount += parseFloat($(this).find('.discount').children('input').val()) * parseInt($(this).find('.count').children('input').val());
                        })
                        $('body').find('#orders-total_price').val(parseFloat(ordersTotalPriceSum).toFixed(2));
                        $('body').find('#orders-total_count').val(Math.round(ordersTotalCount));
                        $('body').find('#orders-total_price_before_discount').val(parseFloat(ordersBeforTotalPriceSum).toFixed(2));
                        $('body').find('#orders-total_discount').val(parseFloat(totalDiscount).toFixed(2));
                    }else {
                        alert('Գոյություն չունի կամ հաջողությամբ չի կատարվել ջնջումը:');
                    }
                }
            })

        }

    })
    $('body').on('keyup','.ordersCountInput',function (){
        var this_ = $(this);
        var id = this_.closest('.addOrdersTableTr').find(".nomId").attr('data-product');
        var count = this_.val();
        var csrfToken = $('meta[name="csrf-token"]').attr("content");
        $.ajax({
            url: '/products/get-products',
            method: 'post',
            datatype: 'json',
            data: {
                itemId: id,
                count:count,
                _csrf: csrfToken
            },
            success:function (data) {
                let pars = JSON.parse(data)
                if (data){
                    if (pars.count === 'nullable'){
                        this_.val('')
                    }else if (pars.count === 'countMore'){
                        this_.val('')
                        alert('Պահեստում նման քանակի ապրանք չկա');
                    }else if (pars.count === 'dontExists'){
                        alert('Նման ապրանք պահեստում գոյություն չունի')
                        this_.val('')
                    }
                }
            }
        })
    })
    $('body').on('click','.ordersCountInput',function (){
        var this_ = $(this);
        var id = this_.closest('.addOrdersTableTr').find(".nomId").attr('data-product');
        var count = this_.val();
        var csrfToken = $('meta[name="csrf-token"]').attr("content");
        $.ajax({
            url: '/products/get-products',
            method: 'post',
            datatype: 'json',
            data: {
                itemId: id,
                count:count,
                _csrf: csrfToken
            },
            success:function (data) {
                let pars = JSON.parse(data)
                if (data){
                    if (pars.count === 'nullable'){
                        this_.val('')
                    }else if (pars.count === 'countMore'){
                        this_.val('')
                        alert('Պահեստում նման քանակի ապրանք չկա');
                    }else if (pars.count === 'dontExists'){
                        alert('Նման ապրանք պահեստում գոյություն չունի')
                    }
                }
            }
        })
    })

    var arr_carent_page_update = [];
    $('body').on('keyup', '.searchForOrderUpdate',function () {
        var nomenclature = $(this).val();
        let current_href = $('body').find('.active .by_ajax_update').data('href');
        arr_carent_page_update.push(current_href);
        let lastValidValue;
        for (let i = arr_carent_page_update.length - 1; i >= 0; i--) {
            const currentValue = arr_carent_page_update[i];
            if (currentValue !== undefined) {
                current_href = currentValue;
                break;
            }
        }
        getNom( current_href+'&nomenclature='+nomenclature);
        let clientId = $('#singleClients').val();
        let totalSum = 0;
        let countSum = 0;
        let orders_date = $('#orders-orders_date').val();
        let csrfToken = $('meta[name="csrf-token"]').attr("content");
        var ordersTotalPriceSum = 0;
        var ordersTotalCount = 0;
        var ordersBeforTotalPriceSum = 0;
        var totalDiscount = 0;
        var discount_desc = [];
        var discountBody = '';
        var ordersTableLength = 0;
        var sequenceNumber = 0;
        $('.addOrdersTableTr').each(function () {
            if ($(this).find('.ordersCountInput').val() != '') {
                countSum += parseInt($(this).children('.ordersAddCount').find('.ordersCountInput').val());
                totalSum += parseFloat($(this).children('.ordersAddCount').find('.ordersPriceInput').val()) * parseInt($(this).children('.ordersAddCount').find('.ordersCountInput').val());
                ordersTableLength++;
            }
        })
        $('.addOrdersTableTr').each(function () {
            if ($(this).find('.ordersCountInput').val() != '') {
                let id = $(this).find(".prodId").attr('data-id');
                let nomenclature_id = $(this).find('.nomId').data('product');
                let name = $(this).children(".nomenclatureName").text();
                let count = $(this).children('.ordersAddCount').find('.ordersCountInput').val();
                let price = $(this).children('.ordersAddCount').find('.ordersPriceInput').val();
                let cost = $(this).children('.ordersAddCount').find('.ordersCostInput').val();
                $.ajax({
                    url:'/orders/get-discount',
                    method:'post',
                    datatype:'json',
                    data:{
                        clientId:clientId,
                        product_id: id,
                        nomenclature_id:nomenclature_id,
                        name:name,
                        count:count,
                        price:price,
                        cost:cost,
                        orders_date:orders_date,
                        totalSum:totalSum,
                        countSum:countSum,
                        _csrf:csrfToken
                    },
                    success:function (data) {
                        let pars = JSON.parse(data);
                        if (pars.discount_desc != undefined){
                            discount_desc.push(pars.discount_desc);
                        }
                        let  prod_clients = '';
                        if (pars.discount_client_id_check.length == 1 && pars.discount_client_id_check[0] == 'empty'){
                            prod_clients = '<input type="hidden" class="discount_client_id" name="discount_client_id_check[empty]" value="empty">';
                        }else {
                            for (let c = 0; c < pars.discount_client_id_check.length; c++){
                                if (pars.discount_client_id_check[c] != 'empty'){
                                    prod_clients += '<input type="hidden" class="discount_client_id" name="discount_client_id_check['+pars.discount_client_id_check[c].id+']" value="'+pars.discount_client_id_check[c].clients_id+'">';
                                }
                            }
                        }
                        sequenceNumber++;
                        ordersBeforTotalPriceSum += parseFloat(pars.format_before_price * pars.count).toFixed(2);
                        ordersTotalPriceSum += parseFloat(pars.price * pars.count).toFixed(2);
                        ordersTotalCount += pars.count;
                        totalDiscount += parseFloat(pars.discount * pars.count).toFixed(2);
                        trss[pars.product_id] = `<tr class="tableNomenclature">
                                     <td>
                                        <span>`+sequenceNumber+`</span>
                                        <input type="hidden" name="order_items[]" value="`+pars.product_id+`">
                                        <input class="prodId" type="hidden" name="product_id[]" value="`+pars.product_id+`">
                                        <input type="hidden" name="nom_id[]" value="`+pars.nomenclature_id+`">
                                        <input type="hidden" name="count_discount_id[]" value="`+pars.count_discount_id+`">
                                        `+prod_clients+`
                                        <input type="hidden" name="cost[]" value="`+pars.cost+`">
                                     </td>
                                     <td  class="name">`+pars.name+`</td>
                                     <td class="count">
                                        <input type="number" name="count_[]" value="`+pars.count+`" class="form-control countProduct">
                                     </td>
                                     <td class="discount">
                                        <span>`+parseFloat(pars.discount).toFixed(2)+`</span>
                                        <input type="hidden" name="discount[]" value="`+parseFloat(pars.discount).toFixed(2)+`">
                                     </td>
                                     <td class="beforePrice">
                                        <span>`+parseFloat(pars.format_before_price).toFixed(2)+`</span>
                                        <input type="hidden" name="beforePrice[]" value="`+parseFloat(pars.format_before_price).toFixed(2)+`">
                                     </td>
                                     <td class="price">
                                        <span>`+parseFloat(pars.price).toFixed(2)+`</span>
                                        <input type="hidden" name="price[]" value="`+parseFloat(pars.price).toFixed(2)+`">
                                     </td>
                                     <td class="totalBeforePrice">
                                        <span>`+parseFloat(pars.format_before_price * pars.count).toFixed(2)+`</span>
                                        <input type="hidden" name="total_before_price[]" value="`+parseFloat(pars.format_before_price * pars.count).toFixed(2)+`">
                                     </td>
                                     <td class="totalPrice">
                                        <span>`+parseFloat(pars.price * pars.count).toFixed(2)+`</span>
                                        <input type="hidden" name="total_price[]" value="`+parseFloat(pars.price * pars.count).toFixed(2)+`">
                                     </td>
                                     <td><button  type="button" class="btn rounded-pill btn-outline-danger deleteUpdateItems">Ջնջել</button></td>
                                 </tr>`
                        ordersTableLength--;
                        if(ordersTableLength == 0){
                            let uniquePairs = discount_desc
                                .flat()
                                .filter(item => item.id !== 'empty')
                                .reduce((result, item) => {
                                    let existingPair = result.find(pair => pair[0] === item.id);
                                    if (existingPair) {
                                        if (item.discount > existingPair[2]) {
                                            existingPair[1] = item.name;
                                            existingPair[2] = item.discount;
                                            existingPair[3] = item.type;
                                        }
                                    } else {
                                        result.push([item.id, item.name, item.discount, item.type]);
                                    }
                                    return result;
                                }, [])
                                .filter(pair => pair.every(value => value !== undefined));
                            uniquePairs.forEach((item,index) => {
                                discountBody += `<tr>
                                                     <td>`+(parseInt(index) + 1) +`</td>
                                                     <td>`+item[1]+`</td>
                                                     <td>`+(item[3] == 'percent' ? item[2] + ' %' : item[2] + ' դր․')+`</td>
                                                </tr>`
                            })
                            $('.discountDesc tbody').parent().append(discountBody);

                            $('body').find('#orders-total_price').val(parseFloat(ordersTotalPriceSum).toFixed(2));
                            $('body').find('#orders-total_count').val(Math.round(ordersTotalCount));
                            $('body').find('#orders-total_price_before_discount').val(parseFloat(ordersBeforTotalPriceSum).toFixed(2));
                            $('body').find('#orders-total_discount').val(parseFloat(totalDiscount).toFixed(2));
                        }
                    }
                })
            }
        })
    })

    $('body').on('keyup', '.searchForOrder',function () {
        var nomenclature = $(this).val();
        let current_href = $('body').find('.active .by_ajax').data('href');
        getNom(current_href + '&nomenclature=' + nomenclature);
    })

    $('body').on('click', '.by_ajax',function () {
        var href_ = $(this).attr('data-href');
        getNom(href_);
        let clientId = $('#singleClients').val();
        let totalSum = 0;
        let countSum = 0;
        // $('.ordersAddingTable tbody').html('');
        let orders_date = $('#orders-orders_date').val();
        let csrfToken = $('meta[name="csrf-token"]').attr("content");
        var ordersTotalPriceSum = 0;
        var ordersTotalCount = 0;
        var ordersBeforTotalPriceSum = 0;
        var totalDiscount = 0;
        var discountBody = '';
        var ordersTableLength = 0;
        var sequenceNumber = 0;
        $('.addOrdersTableTr').each(function () {
            if ($(this).find('.ordersCountInput').val() != '') {
                countSum += parseInt($(this).children('.ordersAddCount').find('.ordersCountInput').val());
                totalSum += parseFloat($(this).children('.ordersAddCount').find('.ordersPriceInput').val()) * parseInt($(this).children('.ordersAddCount').find('.ordersCountInput').val());
                ordersTableLength++;
            }
        })
        $('.addOrdersTableTr').each(function () {
            if ($(this).find('.ordersCountInput').val() != '') {
                let id = $(this).find(".prodId").attr('data-id');
                let nomenclature_id = $(this).find('.nomId').data('product');
                let name = $(this).children(".nomenclatureName").text();
                let count = $(this).children('.ordersAddCount').find('.ordersCountInput').val();
                let price = $(this).children('.ordersAddCount').find('.ordersPriceInput').val();
                let cost = $(this).children('.ordersAddCount').find('.ordersCostInput').val();
                $.ajax({
                    url:'/orders/get-discount',
                    method:'post',
                    datatype:'json',
                    data:{
                        clientId:clientId,
                        product_id: id,
                        nomenclature_id:nomenclature_id,
                        name:name,
                        count:count,
                        price:price,
                        cost:cost,
                        orders_date:orders_date,
                        totalSum:totalSum,
                        countSum:countSum,
                        _csrf:csrfToken
                    },
                    success:function (data) {
                        let pars = JSON.parse(data);
                        if (pars.discount_desc != undefined){
                            discount_desc.push(pars.discount_desc);
                        }
                        let  prod_clients = '';
                        if (pars.discount_client_id_check.length == 1 && pars.discount_client_id_check[0] == 'empty'){
                            prod_clients = '<input type="hidden" class="discount_client_id" name="discount_client_id_check[empty]" value="empty">';
                        }else {
                            for (let c = 0; c < pars.discount_client_id_check.length; c++){
                                if (pars.discount_client_id_check[c] != 'empty'){
                                    prod_clients += '<input type="hidden" class="discount_client_id" name="discount_client_id_check['+pars.discount_client_id_check[c].id+']" value="'+pars.discount_client_id_check[c].clients_id+'">';
                                }
                            }

                        }
                        sequenceNumber++;
                        ordersBeforTotalPriceSum += parseFloat(pars.format_before_price * pars.count).toFixed(2);
                        ordersTotalPriceSum += parseFloat(pars.price * pars.count).toFixed(2);
                        ordersTotalCount += pars.count;
                        totalDiscount += parseFloat(pars.discount * pars.count).toFixed(2);
                        trss[pars.product_id] = `<tr class="tableNomenclature">
                                     <td>
                                        <span>`+sequenceNumber+`</span>
                                        <input type="hidden" name="order_items[]" value="`+pars.product_id+`">
                                        <input class="prodId" type="hidden" name="product_id[]" value="`+pars.product_id+`">
                                        <input class="nom_Id" type="hidden" name="nom_id[]" value="`+pars.nomenclature_id+`">
                                        <input type="hidden" name="count_discount_id[]" value="`+pars.count_discount_id+`">
                                        `+prod_clients+`
                                        <input type="hidden" name="cost[]" value="`+pars.cost+`">
                                     </td>
                                     <td  class="name">`+pars.name+`</td>
                                     <td class="count">
                                        <input type="number" name="count_[]" value="`+pars.count+`" class="form-control countProduct">
                                     </td>
                                     <td class="discount">
                                        <span>`+parseFloat(pars.discount).toFixed(2)+`</span>
                                        <input type="hidden" name="discount[]" value="`+parseFloat(pars.discount).toFixed(2)+`">
                                     </td>
                                     <td class="beforePrice">
                                        <span>`+parseFloat(pars.format_before_price).toFixed(2)+`</span>
                                        <input type="hidden" name="beforePrice[]" value="`+Math.round(pars.format_before_price).toFixed(2)+`">
                                     </td>
                                     <td class="price">
                                        <span>`+parseFloat(pars.price).toFixed(2)+`</span>
                                        <input type="hidden" name="price[]" value="`+parseFloat(pars.price).toFixed(2)+`">
                                     </td>
                                     <td class="totalBeforePrice">
                                        <span>`+parseFloat(pars.format_before_price * pars.count).toFixed(2)+`</span>
                                        <input type="hidden" name="total_before_price[]" value="`+parseFloat(pars.format_before_price * pars.count).toFixed(2)+`">
                                     </td>
                                     <td class="totalPrice">
                                        <span>`+parseFloat(pars.price * pars.count).toFixed(2)+`</span>
                                        <input type="hidden" name="total_price[]" value="`+parseFloat(pars.price * pars.count).toFixed(2)+`">
                                     </td>
                                     <td><button  type="button" class="btn rounded-pill btn-outline-danger deleteItems">Ջնջել</button></td>
                                 </tr>`
                        ordersTableLength--;
                        if(ordersTableLength == 0){
                            // $('.discountDesc tbody').parent().append(discountBody);
                            for (let i in trss) {
                                if(trss[i] != ''){
                                    newTbody.append(trss[i]);
                                }
                            }
                            $('.ordersAddingTable tbody').replaceWith(newTbody);
                            trCounter($('body').find('.ordersAddingTable'));
                            newTbody = $('<tbody></tbody>');
                            // $('body').find('.ordersAddingTable').removeClass('d-none');
                            // $('body').find('.loader').toggleClass('d-none');
                            $('body').find('#orders-total_price').val(parseFloat(ordersTotalPriceSum).toFixed(2));
                            $('body').find('#orders-total_count').val(Math.round(ordersTotalCount));
                            $('body').find('#orders-total_price_before_discount').val(parseFloat(ordersBeforTotalPriceSum).toFixed(2));
                            $('body').find('#orders-total_discount').val(parseFloat(totalDiscount).toFixed(2));
                        }
                    }
                })
            }
        })
    })
    $('body').on('click', '.deleteUpdateItems',function (){
        let  ordersTotalPriceSum = 0;
        let  ordersTotalCount = 0;
        let  ordersBeforTotalPriceSum = 0;
        let  totalDiscount = 0;
        let confirmed =  confirm("Այս ապրանքը դուք ուզում եք ջնջե՞լ:");
        if (confirmed){
            $(this).closest('.tableNomenclature').remove();
            $('body').find('.tableNomenclature').each(function () {
                ordersTotalPriceSum += parseFloat($(this).find('.totalPrice').children('input').val());
                ordersTotalCount += parseInt($(this).find('.count').children('input').val());
                ordersBeforTotalPriceSum += parseFloat($(this).find('.totalBeforePrice').children('input').val());
                totalDiscount += parseFloat($(this).find('.discount').children('input').val()) * parseInt($(this).find('.count').children('input').val());
            })
            $('body').find('#orders-total_price').val(parseFloat(ordersTotalPriceSum).toFixed(2));
            $('body').find('#orders-total_count').val(Math.round(ordersTotalCount));
            $('body').find('#orders-total_price_before_discount').val(parseFloat(ordersBeforTotalPriceSum).toFixed(2));
            $('body').find('#orders-total_discount').val(parseFloat(totalDiscount).toFixed(2));
            alert('Հաջողությամբ ջնջված է:');
        }
    })
    function getNom(href_) {
        let url_id = window.location.href;
        let url = new URL(url_id);
        let urlId = url.searchParams.get("id");
        $.ajax({
            url:href_+'&warehouse_id='+warehouse_id,
            method:'post',
            datatype:'html',
            data:{
                id_count:id_count,
                urlId: urlId,
            },
            success:function (data) {
                $('#ajax_content').html(data);
            }
        })
    }
    function trCounter(table){
        let i = 0;
        table.find('tbody').find('tr').each(function () {
            $(this).find('td:first').find('span').text(++i);
        })
    }
})
