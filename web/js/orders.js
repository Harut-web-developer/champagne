$(document).ready(function () {
    var id_count = {};
    $('body').on('input', '.ordersCountInput', function () {
        count_id($(this));
    });
    function count_id(el) {
        let id = el.closest('tr').find('.nom_Id').data('id');
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
            let id = $(this).closest('tr').find('.prodId').val();
            let nomIdValue = $(this).val();
            let countProductValue = $(this).closest('tr').find('.countProduct').val();
            let discountProductValue = $(this).closest('tr').find('input[name="discount[]"]').val();
            let beforePriceProductValue = $(this).closest('tr').find('input[name="beforePrice[]"]').val();
            let priceProductValue = $(this).closest('tr').find('input[name="price[]"]').val();
            if (!count_product[id]) {
                count_product[id] = {};
            }
            count_product[id]['prodId'] = id;
            count_product[id]['count'] = countProductValue;
            count_product[id]['discount'] = discountProductValue;
            count_product[id]['beforePrice'] = beforePriceProductValue;
            count_product[id]['price'] = priceProductValue;
        });
        var ordersTotalPriceSum = 0;
        var ordersTotalCount = 0;
        var ordersBeforTotalPriceSum = 0;
        var totalDiscount = 0;
        for(let i in count_product){
            if (count_product[i].count == '' || count_product[i].count.startsWith('.') || count_product[i].count.startsWith('-')) {
                ordersTotalCount += parseFloat(1);
                ordersTotalPriceSum += parseFloat(count_product[i].price * 1);     //yndhanur zexchvac gumar
                ordersBeforTotalPriceSum += parseFloat(count_product[i].beforePrice * 1);  //yndhanur gumar
                totalDiscount += parseFloat(count_product[i].discount * 1);
            }else {
                ordersTotalCount += parseFloat(count_product[i].count);
                ordersTotalPriceSum += parseFloat(count_product[i].price * count_product[i].count);     //yndhanur zexchvac gumar
                ordersBeforTotalPriceSum += parseFloat(count_product[i].beforePrice * count_product[i].count);  //yndhanur gumar
                totalDiscount += parseFloat(count_product[i].discount * count_product[i].count);
            }
        }
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
        var warehouse_id = $('body').find('.warehouse_id').val();
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
        var warehouse_id = $('body').find('.warehouse_id').val();
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
        let warehouse_id = $('.warehouse_id').val();
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
                        warehouse_id:warehouse_id,
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
                        let countProd = 0;
                        let discountProd = 0;
                        let priceProd = 0;
                        let beforePriceProd = 0;
                        let lastPrice = 0;
                        let lastBeforePrice = 0;
                        let countDiscountId = '';
                        let aah,cost,name,stringCount,stringCountBalance,stringPrice,stringBeforePrice,stringProductId,nomenclature;
                        let  prod_clients = '';

                        let pars = JSON.parse(data);
                            for (let k = 0; k < pars.length; k++){
                            if (pars[k].discount_desc != undefined){
                                discount_desc.push(pars[k].discount_desc);
                            }

                            countProd += pars[k].count;
                            priceProd += pars[k].price * pars[k].count;
                            beforePriceProd += pars[k].format_before_price * pars[k].count;
                            if(k == pars.length - 1){
                                discountProd = pars[k].discount;
                                countDiscountId = pars[k].count_discount_id;
                                aah = pars[k].aah;
                                cost = pars[k].cost;
                                lastPrice = pars[k].price;
                                lastBeforePrice = pars[k].format_before_price;
                                name = pars[k].name;
                                stringProductId = pars[k].product_id;
                                stringCount = pars[k].string_count;
                                stringCountBalance = pars[k].string_count_balance;
                                stringPrice = pars[k].string_price;
                                stringBeforePrice = pars[k].string_before_price;
                                nomenclature = pars[k].nomenclature_id;
                                if (pars[k].discount_client_id_check.length == 1 && pars[k].discount_client_id_check[0] == 'empty'){
                                    prod_clients = '<input type="hidden" class="discount_client_id" name="discount_client_id_check[empty]" value="empty">';
                                }else {
                                    for (let c = 0; c < pars[k].discount_client_id_check.length; c++){
                                        if (pars[k].discount_client_id_check[c] != 'empty'){
                                            prod_clients += '<input type="hidden" class="discount_client_id" name="discount_client_id_check['+pars[k].discount_client_id_check[c].id+']" value="'+pars[k].discount_client_id_check[c].clients_id+'">';
                                        }
                                    }
                                }
                            }

                        }
                        // console.log(stringPrice,stringBeforePrice)
                        sequenceNumber++;
                        trss[nomenclature] = `<tr class="tableNomenclature">
                                                     <td>
                                                        <span>`+sequenceNumber+`</span>
                                                        <input type="hidden" name="order_items[]" value="`+stringProductId+`">
                                                        <input type="hidden" name="string_price[]" value="`+stringPrice+`">
                                                        <input type="hidden" name="string_before_price[]" value="`+stringBeforePrice+`">
                                                        <input type="hidden" name="string_count[]" value="`+stringCount+`">
                                                        <input type="hidden" name="count_balance[]" value="`+stringCountBalance+`">
                                                        <input class="prodId" type="hidden" name="product_id[]" value="`+stringProductId+`">
                                                        <input class="nom_Id" type="hidden" name="nom_id[]" value="`+nomenclature+`">
                                                        <input type="hidden" name="count_discount_id[]" value="`+countDiscountId+`">
                                                        <input type="hidden" name="aah[]" value="`+aah+`">
                                                        `+prod_clients+`
                                                        <input type="hidden" name="cost[]" value="`+cost+`">
                                                     </td>
                                                     <td  class="name">`+name+`</td>
                                                     <td class="count">
                                                        <input type="number" readonly name="count_[]" value="`+countProd+`" class="form-control countProduct">
                                                     </td>
                                                     <td class="discount">
                                                        <span>`+parseFloat(discountProd).toFixed(2)+`</span>
                                                        <input type="hidden" name="discount[]" value="`+parseFloat(discountProd).toFixed(2)+`">
                                                     </td>
                                                     <td class="beforePrice">
                                                        <span>`+parseFloat(lastBeforePrice).toFixed(2)+`</span>
                                                        <input type="hidden" name="beforePrice[]" value="`+parseFloat(lastBeforePrice).toFixed(2)+`">
                                                     </td>
                                                     <td class="price">
                                                        <span>`+parseFloat(lastPrice).toFixed(2)+`</span>
                                                        <input type="hidden" name="price[]" value="`+parseFloat(lastPrice).toFixed(2)+`">
                                                     </td>
                                                     <td class="totalBeforePrice">
                                                        <span>`+parseFloat(beforePriceProd).toFixed(2)+`</span>
                                                        <input type="hidden" name="total_before_price[]" value="`+parseFloat(beforePriceProd).toFixed(2)+`">
                                                     </td>
                                                     <td class="totalPrice">
                                                        <span>`+parseFloat(priceProd).toFixed(2)+`</span>
                                                        <input type="hidden" name="total_price[]" value="`+parseFloat(priceProd).toFixed(2)+`">
                                                     </td>
                                                     <td><button  type="button" class="btn rounded-pill btn-outline-danger deleteItems">Ջնջել</button></td>
                                                 </tr>`;


                        ordersTableLength--;
                        if(ordersTableLength == 0){
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

                            for (let key in trss) {
                                if (trss.hasOwnProperty(key)) {
                                    newTbody.append(trss[key]);
                                }
                            }
                                $('.ordersAddingTable tbody').empty();
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


    $('body').on('click', '.deleteItems',function (){
        let confirmed =  confirm("Այս ապրանքը դուք ուզում եք ջնջե՞լ:");
        if (confirmed){
            $(this).closest('.tableNomenclature').remove();
            alert('Հաջողությամբ ջնջված է:');
            let id_delete = $(this).closest('.tableNomenclature').find('.prodId').val();
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
        let warehouse_id = $('.warehouse_id').val();
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
                        warehouse_id:warehouse_id,
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
                        let countProd = 0;
                        let discountProd = 0;
                        let priceProd = 0;
                        let beforePriceProd = 0;
                        let lastPrice = 0;
                        let lastBeforePrice = 0;
                        let countDiscountId = '';
                        let aah,cost,name,stringCount,stringCountBalance,stringPrice,stringBeforePrice,stringProductId,nomenclature;
                        let  prod_clients = '';
                        let addOrdersTableBody = '';
                        let pars = JSON.parse(data);
                            for (let k = 0; k < pars.length; k++) {
                            if (pars[k].discount_name != undefined){
                                discount_name.push(pars[k].discount_name);
                            }
                            if (pars[k].discount_desc != undefined){
                                discount_desc.push(pars[k].discount_desc);
                            }
                            countProd += pars[k].count;
                            priceProd += pars[k].price * pars[k].count;
                            beforePriceProd += pars[k].format_before_price * pars[k].count;
                            if(k == pars.length - 1){
                                discountProd = pars[k].discount;
                                countDiscountId = pars[k].count_discount_id;
                                aah = pars[k].aah;
                                cost = pars[k].cost;
                                lastPrice = pars[k].price;
                                lastBeforePrice = pars[k].format_before_price;
                                name = pars[k].name;
                                stringProductId = pars[k].product_id;
                                stringCount = pars[k].string_count;
                                stringCountBalance = pars[k].string_count_balance;
                                stringPrice = pars[k].string_price;
                                stringBeforePrice = pars[k].string_before_price;
                                nomenclature = pars[k].nomenclature_id;
                                if (pars[k].discount_client_id_check.length == 1 && pars[k].discount_client_id_check[0] == 'empty'){
                                    prod_clients = '<input type="hidden" class="discount_client_id" name="discount_client_id_check[empty]" value="empty">';
                                }else {
                                    for (let c = 0; c < pars[k].discount_client_id_check.length; c++){
                                        if (pars[k].discount_client_id_check[c] != 'empty'){
                                            prod_clients += '<input type="hidden" class="discount_client_id" name="discount_client_id_check['+pars[k].discount_client_id_check[c].id+']" value="'+pars[k].discount_client_id_check[c].clients_id+'">';
                                        }
                                    }
                                }
                            }
                        }
                        acordingNumber++
                        trss[nomenclature] = `<tr class="tableNomenclature">
                                     <td>
                                        <span>`+acordingNumber+`</span>
                                        <input type="hidden" name="order_items[]" value="null">
                                        <input type="hidden" name="string_price[]" value="`+stringPrice+`">
                                        <input type="hidden" name="string_before_price[]" value="`+stringBeforePrice+`">
                                        <input type="hidden" name="string_count[]" value="`+stringCount+`">
                                        <input type="hidden" name="count_balance[]" value="`+stringCountBalance+`">
                                        <input class="prodId" type="hidden" name="product_id[]" value="`+stringProductId+`">
                                        <input class="nomId"  type="hidden" name="nom_id[]" value="`+nomenclature+`">
                                        <input class="countDiscountId" type="hidden" name="count_discount_id[]" value="`+countDiscountId+`">
                                        <input type="hidden" name="aah[]" value="`+aah+`">
                                        `+prod_clients+`
                                        <input type="hidden" name="cost[]" value="`+cost+`">
                                     </td>
                                     <td  class="name">`+name+`</td>
                                     <td class="count">
                                        <input type="number" readonly name="count_[]" value="`+countProd+`" class="form-control countProductForUpdate">
                                     </td>
                                     <td class="discount">
                                        <span>`+parseFloat(discountProd).toFixed(2)+`</span>
                                        <input type="hidden" name="discount[]" value="`+parseFloat(discountProd).toFixed(2)+`">
                                     </td>
                                     <td class="beforePrice">
                                        <span>`+parseFloat(lastBeforePrice).toFixed(2)+`</span>
                                        <input type="hidden" name="beforePrice[]" value="`+parseFloat(lastBeforePrice).toFixed(2)+`">
                                     </td>
                                     <td class="price">
                                        <span>`+parseFloat(lastPrice).toFixed(2)+`</span>
                                        <input type="hidden" name="price[]" value="`+parseFloat(lastPrice).toFixed(2)+`">
                                     </td>
                                     <td class="totalBeforePrice">
                                        <span>`+parseFloat(beforePriceProd).toFixed(2)+`</span>
                                        <input type="hidden" name="total_before_price[]" value="`+parseFloat(beforePriceProd).toFixed(2)+`">
                                     </td>
                                     <td class="totalPrice">
                                        <span>`+parseFloat(priceProd).toFixed(2)+`</span>
                                        <input type="hidden" name="total_price[]" value="`+parseFloat(priceProd).toFixed(2)+`">
                                     </td>
                                     <td><button  type="button" class="btn rounded-pill btn-outline-danger deleteUpdateItems">Ջնջել</button></td>
                                 </tr>`

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

                            let uniqueArray = [...new Set(allValues)];

                            let convertedArray = uniqueArray.map(function(element) {
                                return isNaN(element) ? element : parseInt(element);
                            });
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
        let warehouse_id = $('.warehouse_id').val();
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
                        warehouse_id:warehouse_id,
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
                        let countProd = 0;
                        let discountProd = 0;
                        let priceProd = 0;
                        let beforePriceProd = 0;
                        let lastPrice = 0;
                        let lastBeforePrice = 0;
                        let countDiscountId = '';
                        let aah,cost,name,stringCount,stringCountBalance,stringPrice,stringBeforePrice,stringProductId,nomenclature;
                        let  prod_clients = '';
                        let pars = JSON.parse(data);
                        for (let k = 0; k < pars.length; k++) {
                            if (pars[k].discount_desc != undefined){
                                discount_desc.push(pars[k].discount_desc);
                            }
                            if (pars[k].discount_desc != undefined){
                                discount_desc.push(pars[k].discount_desc);
                            }
                            countProd += pars[k].count;
                            priceProd += pars[k].price * pars[k].count;
                            beforePriceProd += pars[k].format_before_price * pars[k].count;
                            if(k == pars.length - 1){
                                discountProd = pars[k].discount;
                                countDiscountId = pars[k].count_discount_id;
                                aah = pars[k].aah;
                                cost = pars[k].cost;
                                lastPrice = pars[k].price;
                                lastBeforePrice = pars[k].format_before_price;
                                name = pars[k].name;
                                stringProductId = pars[k].product_id;
                                stringCount = pars[k].string_count;
                                stringCountBalance = pars[k].string_count_balance;
                                stringPrice = pars[k].string_price;
                                stringBeforePrice = pars[k].string_before_price;
                                nomenclature = pars[k].nomenclature_id;
                                if (pars[k].discount_client_id_check.length == 1 && pars[k].discount_client_id_check[0] == 'empty'){
                                    prod_clients = '<input type="hidden" class="discount_client_id" name="discount_client_id_check[empty]" value="empty">';
                                }else {
                                    for (let c = 0; c < pars[k].discount_client_id_check.length; c++){
                                        if (pars[k].discount_client_id_check[c] != 'empty'){
                                            prod_clients += '<input type="hidden" class="discount_client_id" name="discount_client_id_check['+pars[k].discount_client_id_check[c].id+']" value="'+pars[k].discount_client_id_check[c].clients_id+'">';
                                        }
                                    }
                                }
                            }

                            ordersBeforTotalPriceSum += parseFloat(pars[k].format_before_price * pars[k].count).toFixed(2);
                            ordersTotalPriceSum += parseFloat(pars[k].price * pars[k].count).toFixed(2);
                            ordersTotalCount += pars[k].count;
                            totalDiscount += parseFloat(pars[k].discount * pars[k].count).toFixed(2);

                        }

                        sequenceNumber++
                        trss[nomenclature] = `<tr class="tableNomenclature">
                                     <td>
                                        <span>`+sequenceNumber+`</span>
                                        <input type="hidden" name="order_items[]" value="null">
                                        <input type="hidden" name="string_price[]" value="`+stringPrice+`">
                                        <input type="hidden" name="string_before_price[]" value="`+stringBeforePrice+`">
                                        <input type="hidden" name="string_count[]" value="`+stringCount+`">
                                        <input type="hidden" name="count_balance[]" value="`+stringCountBalance+`">
                                        <input class="prodId" type="hidden" name="product_id[]" value="`+stringProductId+`">
                                        <input class="nomId"  type="hidden" name="nom_id[]" value="`+nomenclature+`">
                                        <input class="countDiscountId" type="hidden" name="count_discount_id[]" value="`+countDiscountId+`">
                                        <input type="hidden" name="aah[]" value="`+aah+`">
                                        `+prod_clients+`
                                        <input type="hidden" name="cost[]" value="`+cost+`">
                                     </td>
                                     <td  class="name">`+name+`</td>
                                     <td class="count">
                                        <input type="number" readonly name="count_[]" value="`+countProd+`" class="form-control countProductForUpdate">
                                     </td>
                                     <td class="discount">
                                        <span>`+parseFloat(discountProd).toFixed(2)+`</span>
                                        <input type="hidden" name="discount[]" value="`+parseFloat(discountProd).toFixed(2)+`">
                                     </td>
                                     <td class="beforePrice">
                                        <span>`+parseFloat(lastBeforePrice).toFixed(2)+`</span>
                                        <input type="hidden" name="beforePrice[]" value="`+parseFloat(lastBeforePrice).toFixed(2)+`">
                                     </td>
                                     <td class="price">
                                        <span>`+parseFloat(lastPrice).toFixed(2)+`</span>
                                        <input type="hidden" name="price[]" value="`+parseFloat(lastPrice).toFixed(2)+`">
                                     </td>
                                     <td class="totalBeforePrice">
                                        <span>`+parseFloat(beforePriceProd).toFixed(2)+`</span>
                                        <input type="hidden" name="total_before_price[]" value="`+parseFloat(beforePriceProd).toFixed(2)+`">
                                     </td>
                                     <td class="totalPrice">
                                        <span>`+parseFloat(priceProd).toFixed(2)+`</span>
                                        <input type="hidden" name="total_price[]" value="`+parseFloat(priceProd).toFixed(2)+`">
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
                        if (data == 'true'){
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
                    }else if (data == 'false'){
                        alert('Մեկ անուն ապրանքի դեպքում պետք է ջնջել ամբողջ պատվերը:');
                    }
                    // else{
                    //     alert('Գոյություն չունի կամ հաջողությամբ չի կատարվել ջնջումը:');
                    // }
                }
            })

        }
    })
    $('body').on('keyup','.ordersCountInput',function (){
        if ($(this).val() < 1){
            $(this).val('')
        }else {
            var this_ = $(this);
            let warehouse_id = $('body').find('.warehouse_id').val();
            var id = this_.closest('.addOrdersTableTr').find(".nomId").attr('data-product');
            var count = this_.val();
            let id_product = $(this).closest('tr').find('.prodId').data('id');
            var csrfToken = $('meta[name="csrf-token"]').attr("content");
            $.ajax({
                url: '/products/get-products',
                method: 'post',
                datatype: 'json',
                data: {
                    itemId: id,
                    warehouse_id:warehouse_id,
                    count:count,
                    _csrf: csrfToken
                },
                success:function (data) {
                    let p = JSON.parse(data)
                    if (data){
                        if (p.count === 'nullable'){
                            this_.val('')
                        }else if (p.count === 'countMore'){
                            this_.val('')
                            count = this_.val('');
                            delete id_count[String(id_product).trim()];
                            delete count_product[String(id).trim()];
                            alert('Պահեստում նման քանակի ապրանք չկա');
                        }else if (p.count === 'dontExists'){
                            alert('Նման ապրանք պահեստում գոյություն չունի')
                            this_.val('')
                        }
                        // else if (pars.count === 'exists'){
                            // }
                    }
                }
            })
        }
    })
    $('body').on('click','.ordersCountInput',function (){
        if ($(this).val() < 1){
            $(this).val('')
        }else {
            var this_ = $(this);
            let warehouse_id = $('body').find('.warehouse_id').val();
            var id = this_.closest('.addOrdersTableTr').find(".nomId").attr('data-product');
            let id_product = $(this).closest('tr').find('.prodId').data('id');
            var count = this_.val();
            var csrfToken = $('meta[name="csrf-token"]').attr("content");
            $.ajax({
                url: '/products/get-products',
                method: 'post',
                datatype: 'json',
                data: {
                    itemId: id,
                    warehouse_id:warehouse_id,
                    count:count,
                    _csrf: csrfToken
                },
                success:function (data) {
                    let p = JSON.parse(data)
                    if (data){
                        if (p.count === 'nullable'){
                            this_.val('')
                        }else if (p.count === 'countMore'){
                            this_.val('')
                            count = this_.val('');
                            delete id_count[String(id_product).trim()];
                            delete count_product[String(id).trim()];
                            alert('Պահեստում նման քանակի ապրանք չկա');
                        }else if (p.count === 'dontExists'){
                            alert('Նման ապրանք պահեստում գոյություն չունի')
                        }
                    }
                }
            })
        }
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
        let warehouse_id = $('.warehouse_id').val();
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
                        warehouse_id:warehouse_id,
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
                        let countProd = 0;
                        let discountProd = 0;
                        let priceProd = 0;
                        let beforePriceProd = 0;
                        let lastPrice = 0;
                        let lastBeforePrice = 0;
                        let countDiscountId = '';
                        let aah,cost,name,stringCount,stringCountBalance,stringPrice,stringBeforePrice,stringProductId,nomenclature;
                        let  prod_clients = '';
                        let pars = JSON.parse(data);
                        for (let k = 0; k < pars.length; k++) {
                            if (pars[k].discount_desc != undefined){
                                discount_desc.push(pars[k].discount_desc);
                            }
                            countProd += pars[k].count;
                            priceProd += pars[k].price * pars[k].count;
                            beforePriceProd += pars[k].format_before_price * pars[k].count;
                            if(k == pars.length - 1){
                                discountProd = pars[k].discount;
                                countDiscountId = pars[k].count_discount_id;
                                aah = pars[k].aah;
                                cost = pars[k].cost;
                                lastPrice = pars[k].price;
                                lastBeforePrice = pars[k].format_before_price;
                                name = pars[k].name;
                                stringProductId = pars[k].product_id;
                                stringCount = pars[k].string_count;
                                stringCountBalance = pars[k].string_count_balance;
                                stringPrice = pars[k].string_price;
                                stringBeforePrice = pars[k].string_before_price;
                                nomenclature = pars[k].nomenclature_id;
                                if (pars[k].discount_client_id_check.length == 1 && pars[k].discount_client_id_check[0] == 'empty'){
                                    prod_clients = '<input type="hidden" class="discount_client_id" name="discount_client_id_check[empty]" value="empty">';
                                }else {
                                    for (let c = 0; c < pars[k].discount_client_id_check.length; c++){
                                        if (pars[k].discount_client_id_check[c] != 'empty'){
                                            prod_clients += '<input type="hidden" class="discount_client_id" name="discount_client_id_check['+pars[k].discount_client_id_check[c].id+']" value="'+pars[k].discount_client_id_check[c].clients_id+'">';
                                        }
                                    }
                                }
                            }
                            ordersBeforTotalPriceSum += parseFloat(pars[k].format_before_price * pars[k].count).toFixed(2);
                            ordersTotalPriceSum += parseFloat(pars[k].price * pars[k].count).toFixed(2);
                            ordersTotalCount += pars[k].count;
                            totalDiscount += parseFloat(pars[k].discount * pars[k].count).toFixed(2);

                        }
                        sequenceNumber++;
                        trss[nomenclature] = `<tr class="tableNomenclature">
                                     <td>
                                        <span>`+sequenceNumber+`</span>
                                        <input type="hidden" name="order_items[]" value="null">
                                        <input type="hidden" name="string_price[]" value="`+stringPrice+`">
                                        <input type="hidden" name="string_before_price[]" value="`+stringBeforePrice+`">
                                        <input type="hidden" name="string_count[]" value="`+stringCount+`">
                                        <input type="hidden" name="count_balance[]" value="`+stringCountBalance+`">
                                        <input class="prodId" type="hidden" name="product_id[]" value="`+stringProductId+`">
                                        <input class="nomId"  type="hidden" name="nom_id[]" value="`+nomenclature+`">
                                        <input class="countDiscountId" type="hidden" name="count_discount_id[]" value="`+countDiscountId+`">
                                        <input type="hidden" name="aah[]" value="`+aah+`">
                                        `+prod_clients+`
                                        <input type="hidden" name="cost[]" value="`+cost+`">
                                     </td>
                                     <td  class="name">`+name+`</td>
                                     <td class="count">
                                        <input type="number" readonly name="count_[]" value="`+countProd+`" class="form-control countProductForUpdate">
                                     </td>
                                     <td class="discount">
                                        <span>`+parseFloat(discountProd).toFixed(2)+`</span>
                                        <input type="hidden" name="discount[]" value="`+parseFloat(discountProd).toFixed(2)+`">
                                     </td>
                                     <td class="beforePrice">
                                        <span>`+parseFloat(lastBeforePrice).toFixed(2)+`</span>
                                        <input type="hidden" name="beforePrice[]" value="`+parseFloat(lastBeforePrice).toFixed(2)+`">
                                     </td>
                                     <td class="price">
                                        <span>`+parseFloat(lastPrice).toFixed(2)+`</span>
                                        <input type="hidden" name="price[]" value="`+parseFloat(lastPrice).toFixed(2)+`">
                                     </td>
                                     <td class="totalBeforePrice">
                                        <span>`+parseFloat(beforePriceProd).toFixed(2)+`</span>
                                        <input type="hidden" name="total_before_price[]" value="`+parseFloat(beforePriceProd).toFixed(2)+`">
                                     </td>
                                     <td class="totalPrice">
                                        <span>`+parseFloat(priceProd).toFixed(2)+`</span>
                                        <input type="hidden" name="total_price[]" value="`+parseFloat(priceProd).toFixed(2)+`">
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
        let clientId = $('#singleClients').val();
        let totalSum = 0;
        let countSum = 0;
        // $('.ordersAddingTable tbody').html('');
        let orders_date = $('#orders-orders_date').val();
        let csrfToken = $('meta[name="csrf-token"]').attr("content");
        let warehouse_id = $('.warehouse_id').val();
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
                        warehouse_id:warehouse_id,
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
                        let countProd = 0;
                        let discountProd = 0;
                        let priceProd = 0;
                        let beforePriceProd = 0;
                        let lastPrice = 0;
                        let lastBeforePrice = 0;
                        let countDiscountId = '';
                        let aah,cost,name,stringCount,stringCountBalance,stringPrice,stringBeforePrice,stringProductId,nomenclature;
                        let  prod_clients = '';
                        let pars = JSON.parse(data);
                        for (let k = 0; k < pars.length; k++) {
                            if (pars[k].discount_desc != undefined){
                                discount_desc.push(pars[k].discount_desc);
                            }

                            countProd += pars[k].count;
                            priceProd += pars[k].price * pars[k].count;
                            beforePriceProd += pars[k].format_before_price * pars[k].count;
                            if(k == pars.length - 1){
                                discountProd = pars[k].discount;
                                countDiscountId = pars[k].count_discount_id;
                                aah = pars[k].aah;
                                cost = pars[k].cost;
                                lastPrice = pars[k].price;
                                lastBeforePrice = pars[k].format_before_price;
                                name = pars[k].name;
                                stringProductId = pars[k].product_id;
                                stringCount = pars[k].string_count;
                                stringCountBalance = pars[k].string_count_balance;
                                stringPrice = pars[k].string_price;
                                stringBeforePrice = pars[k].string_before_price;
                                nomenclature = pars[k].nomenclature_id;
                                if (pars[k].discount_client_id_check.length == 1 && pars[k].discount_client_id_check[0] == 'empty'){
                                    prod_clients = '<input type="hidden" class="discount_client_id" name="discount_client_id_check[empty]" value="empty">';
                                }else {
                                    for (let c = 0; c < pars[k].discount_client_id_check.length; c++){
                                        if (pars[k].discount_client_id_check[c] != 'empty'){
                                            prod_clients += '<input type="hidden" class="discount_client_id" name="discount_client_id_check['+pars[k].discount_client_id_check[c].id+']" value="'+pars[k].discount_client_id_check[c].clients_id+'">';
                                        }
                                    }
                                }

                            }


                            ordersBeforTotalPriceSum += parseFloat(pars[k].format_before_price * pars[k].count).toFixed(2);
                            ordersTotalPriceSum += parseFloat(pars[k].price * pars[k].count).toFixed(2);
                            ordersTotalCount += pars[k].count;
                            totalDiscount += parseFloat(pars[k].discount * pars[k].count).toFixed(2);


                        }

                        sequenceNumber++;
                        trss[nomenclature] = `<tr class="tableNomenclature">
                                                     <td>
                                                        <span>`+sequenceNumber+`</span>
                                                        <input type="hidden" name="order_items[]" value="`+stringProductId+`">
                                                        <input type="hidden" name="string_price[]" value="`+stringPrice+`">
                                                        <input type="hidden" name="string_before_price[]" value="`+stringBeforePrice+`">
                                                        <input type="hidden" name="string_count[]" value="`+stringCount+`">
                                                        <input type="hidden" name="count_balance[]" value="`+stringCountBalance+`">
                                                        <input class="prodId" type="hidden" name="product_id[]" value="`+stringProductId+`">
                                                        <input class="nom_Id" type="hidden" name="nom_id[]" value="`+nomenclature+`">
                                                        <input type="hidden" name="count_discount_id[]" value="`+countDiscountId+`">
                                                        <input type="hidden" name="aah[]" value="`+aah+`">
                                                        `+prod_clients+`
                                                        <input type="hidden" name="cost[]" value="`+cost+`">
                                                     </td>
                                                     <td  class="name">`+name+`</td>
                                                     <td class="count">
                                                        <input type="number" readonly name="count_[]" value="`+countProd+`" class="form-control countProduct">
                                                     </td>
                                                     <td class="discount">
                                                        <span>`+parseFloat(discountProd).toFixed(2)+`</span>
                                                        <input type="hidden" name="discount[]" value="`+parseFloat(discountProd).toFixed(2)+`">
                                                     </td>
                                                     <td class="beforePrice">
                                                        <span>`+parseFloat(lastBeforePrice).toFixed(2)+`</span>
                                                        <input type="hidden" name="beforePrice[]" value="`+parseFloat(lastBeforePrice).toFixed(2)+`">
                                                     </td>
                                                     <td class="price">
                                                        <span>`+parseFloat(lastPrice).toFixed(2)+`</span>
                                                        <input type="hidden" name="price[]" value="`+parseFloat(lastPrice).toFixed(2)+`">
                                                     </td>
                                                     <td class="totalBeforePrice">
                                                        <span>`+parseFloat(beforePriceProd).toFixed(2)+`</span>
                                                        <input type="hidden" name="total_before_price[]" value="`+parseFloat(beforePriceProd).toFixed(2)+`">
                                                     </td>
                                                     <td class="totalPrice">
                                                        <span>`+parseFloat(priceProd).toFixed(2)+`</span>
                                                        <input type="hidden" name="total_price[]" value="`+parseFloat(priceProd).toFixed(2)+`">
                                                     </td>
                                                     <td><button  type="button" class="btn rounded-pill btn-outline-danger deleteItems">Ջնջել</button></td>
                                                 </tr>`;

                        ordersTableLength--;
                        if(ordersTableLength == 0){
                            // $('.discountDesc tbody').parent().append(discountBody);
                            // for (let i in trss) {
                            //     if(trss[i] != ''){
                            //         newTbody.append(trss[i]);
                            //     }
                            // }
                            // $('.ordersAddingTable tbody').replaceWith(newTbody);
                            // trCounter($('body').find('.ordersAddingTable'));
                            // newTbody = $('<tbody></tbody>');
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

    $('body').on('click', '.by_ajax',function () {
        var href_ = $(this).attr('data-href');
        getNom(href_);
        let clientId = $('#singleClients').val();
        let totalSum = 0;
        let countSum = 0;
        // $('.ordersAddingTable tbody').html('');
        let orders_date = $('#orders-orders_date').val();
        let csrfToken = $('meta[name="csrf-token"]').attr("content");
        let warehouse_id = $('.warehouse_id').val();
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
                        warehouse_id:warehouse_id,
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
                        let countProd = 0;
                        let discountProd = 0;
                        let priceProd = 0;
                        let beforePriceProd = 0;
                        let lastPrice = 0;
                        let lastBeforePrice = 0;
                        let countDiscountId = '';
                        let aah,cost,name,stringCount,stringCountBalance,stringPrice,stringBeforePrice,stringProductId,nomenclature;
                        let  prod_clients = '';
                        let pars = JSON.parse(data);
                        for (let k = 0; k < pars.length; k++) {
                            if (pars[k].discount_desc != undefined){
                                discount_desc.push(pars[k].discount_desc);
                            }
                            countProd += pars[k].count;
                            priceProd += pars[k].price * pars[k].count;
                            beforePriceProd += pars[k].format_before_price * pars[k].count;
                            if(k == pars.length - 1){
                                discountProd = pars[k].discount;
                                countDiscountId = pars[k].count_discount_id;
                                aah = pars[k].aah;
                                cost = pars[k].cost;
                                lastPrice = pars[k].price;
                                lastBeforePrice = pars[k].format_before_price;
                                name = pars[k].name;
                                stringProductId = pars[k].product_id;
                                stringCount = pars[k].string_count;
                                stringCountBalance = pars[k].string_count_balance;
                                stringPrice = pars[k].string_price;
                                stringBeforePrice = pars[k].string_before_price;
                                nomenclature = pars[k].nomenclature_id;
                                if (pars[k].discount_client_id_check.length == 1 && pars[k].discount_client_id_check[0] == 'empty'){
                                    prod_clients = '<input type="hidden" class="discount_client_id" name="discount_client_id_check[empty]" value="empty">';
                                }else {
                                    for (let c = 0; c < pars[k].discount_client_id_check.length; c++){
                                        if (pars[k].discount_client_id_check[c] != 'empty'){
                                            prod_clients += '<input type="hidden" class="discount_client_id" name="discount_client_id_check['+pars[k].discount_client_id_check[c].id+']" value="'+pars[k].discount_client_id_check[c].clients_id+'">';
                                        }
                                    }
                                }

                            }

                            ordersBeforTotalPriceSum += parseFloat(pars[k].format_before_price * pars[k].count).toFixed(2);
                            ordersTotalPriceSum += parseFloat(pars[k].price * pars[k].count).toFixed(2);
                            ordersTotalCount += pars[k].count;
                            totalDiscount += parseFloat(pars[k].discount * pars[k].count).toFixed(2);
                        }
                        sequenceNumber++;
                        trss[nomenclature] = `<tr class="tableNomenclature">
                                                     <td>
                                                        <span>`+sequenceNumber+`</span>
                                                        <input type="hidden" name="order_items[]" value="`+stringProductId+`">
                                                        <input type="hidden" name="string_price[]" value="`+stringPrice+`">
                                                        <input type="hidden" name="string_before_price[]" value="`+stringBeforePrice+`">
                                                        <input type="hidden" name="string_count[]" value="`+stringCount+`">
                                                        <input type="hidden" name="count_balance[]" value="`+stringCountBalance+`">
                                                        <input class="prodId" type="hidden" name="product_id[]" value="`+stringProductId+`">
                                                        <input class="nom_Id" type="hidden" name="nom_id[]" value="`+nomenclature+`">
                                                        <input type="hidden" name="count_discount_id[]" value="`+countDiscountId+`">
                                                        <input type="hidden" name="aah[]" value="`+aah+`">
                                                        `+prod_clients+`
                                                        <input type="hidden" name="cost[]" value="`+cost+`">
                                                     </td>
                                                     <td  class="name">`+name+`</td>
                                                     <td class="count">
                                                        <input type="number" readonly name="count_[]" value="`+countProd+`" class="form-control countProduct">
                                                     </td>
                                                     <td class="discount">
                                                        <span>`+parseFloat(discountProd).toFixed(2)+`</span>
                                                        <input type="hidden" name="discount[]" value="`+parseFloat(discountProd).toFixed(2)+`">
                                                     </td>
                                                     <td class="beforePrice">
                                                        <span>`+parseFloat(lastBeforePrice).toFixed(2)+`</span>
                                                        <input type="hidden" name="beforePrice[]" value="`+parseFloat(lastBeforePrice).toFixed(2)+`">
                                                     </td>
                                                     <td class="price">
                                                        <span>`+parseFloat(lastPrice).toFixed(2)+`</span>
                                                        <input type="hidden" name="price[]" value="`+parseFloat(lastPrice).toFixed(2)+`">
                                                     </td>
                                                     <td class="totalBeforePrice">
                                                        <span>`+parseFloat(beforePriceProd).toFixed(2)+`</span>
                                                        <input type="hidden" name="total_before_price[]" value="`+parseFloat(beforePriceProd).toFixed(2)+`">
                                                     </td>
                                                     <td class="totalPrice">
                                                        <span>`+parseFloat(priceProd).toFixed(2)+`</span>
                                                        <input type="hidden" name="total_price[]" value="`+parseFloat(priceProd).toFixed(2)+`">
                                                     </td>
                                                     <td><button  type="button" class="btn rounded-pill btn-outline-danger deleteItems">Ջնջել</button></td>
                                                 </tr>`;


                        ordersTableLength--;
                        if(ordersTableLength == 0){
                            // for (let i in trss) {
                            //     if(trss[i] != ''){
                            //         newTbody.append(trss[i]);
                            //     }
                            // }
                            // $('.ordersAddingTable tbody').replaceWith(newTbody);
                            // trCounter($('body').find('.ordersAddingTable'));
                            // newTbody = $('<tbody></tbody>');
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
    $('body').on('click', '.changeCount',function () {
        let orderItemsId = $(this).data('orders');
        let csrfToken = $('meta[name="csrf-token"]').attr("content");

        $.ajax({
            url: '/orders/change-count',
            method: 'get',
            datatype:'html',
            data:{
                orderItemsId:orderItemsId,
                _csrf:csrfToken
            },
            success: function (data){
                $('.changeModalBody').html(data);
            }
        })
    })
    $('body').on('click', '.addChange', function (){
        let this_ = $(this);
        let itemsId = $('body').find('.itemsId').val();
        let countBy = $('body').find('#countByModal').val();
        let costBy = $('body').find('#costModal').val() * countBy;
        let discountBy = $('body').find('#discountByModal').val() * countBy;
        let priceBy = $('body').find('#totalPriceModal').val();
        let priceBeforeDiscountBy = $('body').find('#totalBeforePriceModal').val();
        let newStringCount = $('body').find('.newStringCountModal').val();
        let newStringPrice = $('body').find('.newStringPriceModal').val();
        let newStringBeforePrice = $('body').find('.newStringBeforePriceModal').val();
        let newStringProductId = $('body').find('.newProductIdModal').val();
        let newCountStringBal = $('body').find('.newCountBalanceModal').val();
        let csrfToken = $('meta[name="csrf-token"]').attr("content");
        $.ajax({
            url:'/orders/changing-items',
            method:'post',
            datatype:'json',
            data:{
                itemsId:itemsId,//
                countBy:countBy,//
                costBy:costBy,//
                discountBy:discountBy,//
                priceBy:priceBy,//
                priceBeforeDiscountBy:priceBeforeDiscountBy,//
                newStringCount:newStringCount,
                newStringPrice:newStringPrice,//
                newStringBeforePrice:newStringBeforePrice,//
                newStringProductId:newStringProductId,
                newCountStringBal:newCountStringBal,//
                _csrf:csrfToken
            },
            success:function (data){
                if (data){
                    alert('Փոփոխությունը կատարվել է հաջողությամբ։');
                    location.reload();
                }
            }
        })
    })
    $('body').on('click','#countByModal', function (){
        $(this).val(function(index, value) {
            return value.replace(/-/g, '');
        });
        if (parseInt($(this).val()) > parseInt($('#countModal').val())){
            alert("Պատվերի քանակից ավել հնարավոր չէ փոխել։")
            $(this).val($('#countModal').val());
        }else if (parseInt($(this).val()) < 1 || $(this).val() == ''){
            alert("Նշված դաշտը չի կարող լինել դատարկ կամ 1-ից պակաս։")
            $(this).val($('#countModal').val());
        }else {
            let bal = parseInt($(this).attr('max')) - $(this).val()
            let priceArr = $('.stringPriceModal').val().split(',');
            let beforePriceArr = $('.stringBeforePriceModal').val().split(',');
            let stringCount = $('.stringCountModal').val().split(',');
            let productId = $('.productIdModal').val().split(',');
            let countBalance = $('.countBalanceModal').val().split(',');
            let newPriceArr = priceArr;
            let newBeforePriceArr = beforePriceArr;
            let newStringCount = stringCount;
            let newProductId = productId;
            let newPrice = 0;
            let newBeforePrice = 0;
            for (let i = productId.length - 1; i >= 0; i--){
                if (parseInt(stringCount[i]) - bal >= 0){
                    stringCount[i] = parseInt(stringCount[i]) - bal;
                    countBalance[i] = parseInt(countBalance[i]) + bal;
                    newPrice += bal * parseFloat(priceArr[i]);
                    newBeforePrice += bal * parseFloat(beforePriceArr[i]);
                    break;
                }
                else if (parseInt(stringCount[i]) - bal < 0){
                    newPrice = parseInt(stringCount[i]) * parseFloat(priceArr[i]);
                    newBeforePrice = parseInt(stringCount[i]) * parseFloat(beforePriceArr[i]);
                    bal -= parseInt(stringCount[i]);
                    countBalance[i] = parseInt(countBalance[i]) + parseInt(stringCount[i]);
                    newPriceArr.pop();
                    newBeforePriceArr.pop();
                    newStringCount.pop();
                    newProductId.pop();
                }
            }
            let newCountBalance = countBalance.join(',');
            let newPriceArrStr = newPriceArr.join(',');
            let newBeforePriceArrStr = newBeforePriceArr.join(',');
            let newStringCountStr = newStringCount.join(',');
            let newProductIdStr = newProductId.join(',');
            $('#totalBeforePriceModal').val(parseFloat($('#totalBeforePriceModal').attr('max') - newBeforePrice).toFixed(2))
            $('#totalPriceModal').val(parseFloat($('#totalPriceModal').attr('max') - newPrice).toFixed(2))
            $('.newCountBalanceModal').val(newCountBalance)
            $('.newStringCountModal').val(newStringCountStr)
            $('.newStringPriceModal').val(newPriceArrStr)
            $('.newStringBeforePriceModal').val(newBeforePriceArrStr)
            $('.newProductIdModal').val(newProductIdStr)
        }
    })

    $('body').on('keyup', '#countByModal', function() {
        $(this).val(function(index, value) {
            return value.replace(/-/g, '');
        });
        if (parseInt($('#countByModal').val()) > parseInt($('#countByModal').attr('max'))) {
            alert("Պատվերի քանակից ավել հնարավոր չէ փոխել։")
            $(this).val($('#countByModal').attr('max'));
            $('.addChange').prop('disabled', false);
        } else if ($(this).val() < 1) {
            alert("Նշված դաշտը չի կարող լինել դատարկ կամ 1-ից պակաս։")
            $('.addChange').prop('disabled', true);
        } else {
            $('.addChange').prop('disabled', false);
            let bal = parseInt($(this).attr('max')) - $(this).val()
            let priceArr = $('.stringPriceModal').val().split(',');
            let beforePriceArr = $('.stringBeforePriceModal').val().split(',');
            let stringCount = $('.stringCountModal').val().split(',');
            let productId = $('.productIdModal').val().split(',');
            let newPriceArr = priceArr;
            let newBeforePriceArr = beforePriceArr;
            let newStringCount = stringCount;
            let newProductId = productId;
            let newPrice = 0;
            let newBeforePrice = 0;
            for (let i = productId.length - 1; i >= 0; i--){
                if (parseInt(stringCount[i]) - bal >= 0){
                    stringCount[i] = parseInt(stringCount[i]) - bal;
                    newPrice += bal * parseFloat(priceArr[i]);
                    newBeforePrice += bal * parseFloat(beforePriceArr[i]);
                    break;
                }
                else if (parseInt(stringCount[i]) - bal < 0){
                    newPrice = parseInt(stringCount[i]) * parseFloat(priceArr[i]);
                    newBeforePrice = parseInt(stringCount[i]) * parseFloat(beforePriceArr[i]);
                    bal -= parseInt(stringCount[i]);
                    newPriceArr.pop();
                    newBeforePriceArr.pop();
                    newStringCount.pop();
                    newProductId.pop();
                }
            }
            let newPriceArrStr = newPriceArr.join(',');
            let newBeforePriceArrStr = newBeforePriceArr.join(',');
            let newStringCountStr = newStringCount.join(',');
            let newProductIdStr = newProductId.join(',');
            $('#totalBeforePriceModal').val(parseFloat($('#totalBeforePriceModal').attr('max') - newBeforePrice).toFixed(2))
            $('#totalPriceModal').val(parseFloat($('#totalPriceModal').attr('max') - newPrice).toFixed(2))
            $('.newStringCountModal').val(newStringCountStr)
            $('.newStringPriceModal').val(newPriceArrStr)
            $('.newStringBeforePriceModal').val(newBeforePriceArrStr)
            $('.newProductIdModal').val(newProductIdStr)
        }
    })
    function getNom(href_) {
        let url_id = window.location.href;
        let url = new URL(url_id);
        let urlId = url.searchParams.get("id");
        var warehouse_id = $('body').find('.warehouse_id').val();
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

    $('body').on('change','#orders-user_id',function(){
        if($('#orders-user_id').val() != ''){
            let user_id = $(this).val();
            let csrfToken = $('meta[name="csrf-token"]').attr("content");
            $.ajax({
                url:'/orders/get-manager',
                method:'get',
                datatype:'html',
                data:{
                    user_id:user_id,
                    _csrf:csrfToken
                },
                success:function (data){
                    $('.clients_ajax_content').html(data);
                }
            })
        }else if($('#orders-user_id').val() == ''){
            $('#singleClients').empty();
        }
    })

    $('body').on('change','#singleClients',function(){
        if($('#singleClients').val() != ''){
            let client_id = $(this).val();
            let csrfToken = $('meta[name="csrf-token"]').attr("content");
            $.ajax({
                url:'/orders/get-warehouse',
                method:'post',
                datatype:'json',
                data:{
                    client_id:client_id,
                    _csrf:csrfToken,
                },
                success:function (data){
                    $('.warhouse_ajax_content').html(data);
                }
            })
        }
    })

})
