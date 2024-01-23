$(document).ready(function () {
    var id_count = {};
    $('body').on('input', '.ordersCountInput', function () {
        count_id($(this));
        // console.log(1)
    });
    function count_id(el) {
        let id = el.closest('tr').find('.prodId').data('id');
        let count = el.val();
        if (count) {
            id_count[String(id).trim()] = parseInt(count.trim());
        }else{
            delete id_count[String(id).trim()];
        }
        // console.log('2')
    }

    $('.js-example-basic-single').select2();
    $('body').on('change','#orders-orders_date, #singleClients',function(){
        if($('#orders-orders_date').val() != '' && $('#singleClients').val() != ''){
            $('body').find('.addOrders').attr('disabled',false);
        }
        // console.log('3')
    })
    var newTbody = $('<tbody></tbody>');
    var trss = {};
    var trs = {};
    $('body').on('click','.create', function (e) {
        var documentsTableBody = '';
        let n = 0;
        let clientId = $('#singleClients').val();
        let totalSum = 0;
        let countSum = 0;
        $('.ordersAddingTable tbody').html('');
        let orders_date = $('#orders-orders_date').val();
        let csrfToken = $('meta[name="csrf-token"]').attr("content");
        var ordersTotalPriceSum = 0;
        var ordersTotalCount = 0;
        var ordersBeforTotalPriceSum = 0;
        var totalDiscount = 0;
        var discount_desc = [];
        var discountBody = '';
        $('body').find('.loader').toggleClass('d-none');
        $('body').find('.ordersAddingTable').addClass('d-none');
        var ordersTableLength = 0;
        var sequenceNumber = 0;
        $('.addOrdersTableTr').each(function () {
            if ($(this).find('.ordersCountInput').val() != '') {
                countSum += parseInt($(this).children('.ordersAddCount').find('.ordersCountInput').val());
                totalSum += parseInt($(this).children('.ordersAddCount').find('.ordersPriceInput').val()) * parseInt($(this).children('.ordersAddCount').find('.ordersCountInput').val());
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
                        let clientsIdCheck = [];
                        let pars = JSON.parse(data);
                        if (pars.discount_desc != undefined){
                            discount_desc.push(pars.discount_desc);
                        }
                        // let uniquePairs = [...new Set(discount_desc.flat().map(item => item.id))].map(id => {
                        //     let matchingItems = discount_desc.flat().filter(item => item.id === id);
                        //     let uniqueName = [...new Set(matchingItems.map(item => item.name))];
                        //     return [id, uniqueName[0]];
                        // });
                        // if (pars.discount_client_id_check != []){
                        //     for (let d = 0; d < pars.discount_client_id_check.length; d++){
                        //         let arr = [];
                        //         arr.push(pars.discount_client_id_check[d].id);
                        //         arr.push(pars.discount_client_id_check[d].clients_id);
                        //         clientsIdCheck.push(arr);
                        //     }
                        // }

                        sequenceNumber++;
                        ordersBeforTotalPriceSum += Math.round(pars.format_before_price) * pars.count;
                        ordersTotalPriceSum += Math.round(pars.price) * pars.count;
                        ordersTotalCount += pars.count;
                        totalDiscount += Math.round(pars.discount) * pars.count;
                        trss[pars.product_id] = `<tr class="tableNomenclature">
                                                     <td>
                                                        <span>`+sequenceNumber+`</span>
                                                        <input type="hidden" name="order_items[]" value="`+pars.product_id+`">
                                                        <input type="hidden" name="nomenclature_id[]" value="`+pars.nomenclature_id+`">
                                                        <input type="hidden" name="count_discount_id[]" value="`+pars.count_discount_id+`">
<!--                                                        <input type="hidden" name="discount_client_id_check[]" value='`+(pars.discount_client_id_check == [] ? []: JSON.stringify(clientsIdCheck))+`'>-->
                                                        <input type="hidden" name="cost[]" value="`+pars.cost+`">
                                                     </td>
                                                     <td  class="name">`+pars.name+`</td>
                                                     <td class="count">
                                                        <input type="number" name="count_[]" value="`+pars.count+`" class="form-control countProduct">
                                                     </td>
                                                     <td class="discount">
                                                        <span>`+Math.round(pars.discount)+`</span>
                                                        <input type="hidden" name="discount[]" value="`+Math.round(pars.discount)+`">
                                                     </td>
                                                     <td class="beforePrice">
                                                        <span>`+Math.round(pars.format_before_price)+`</span>
                                                        <input type="hidden" name="beforePrice[]" value="`+Math.round(pars.format_before_price)+`">
                                                     </td>
                                                     <td class="price">
                                                        <span>`+Math.round(pars.price)+`</span>
                                                        <input type="hidden" name="price[]" value="`+Math.round(pars.price)+`">
                                                     </td>
                                                     <td class="totalBeforePrice">
                                                        <span>`+Math.round(pars.format_before_price) * pars.count+`</span>
                                                        <input type="hidden" name="totalBeforePrice[]" value="`+Math.round(pars.format_before_price) * pars.count+`">
                                                     </td>
                                                     <td class="totalPrice">
                                                        <span>`+Math.round(pars.price) * pars.count+`</span>
                                                        <input type="hidden" name="totalPrice[]" value="`+Math.round(pars.price) * pars.count+`">
                                                     </td>
                                                     <td><button  type="button" class="btn rounded-pill btn-outline-danger deleteItems">Ջնջել</button></td>
                                                 </tr>`;

                        ordersTableLength--;
                        if(ordersTableLength == 0){
                            let uniquePairs = [...new Set(discount_desc.flat().map(item => item.id))].map(id => {
                                let matchingItems = discount_desc.flat().filter(item => item.id === id);
                                let uniqueName = [...new Set(matchingItems.map(item => item.name))];
                                let uniqueDiscount = [...new Set(matchingItems.map(item => item.discount))];
                                let uniqueDiscountType = [...new Set(matchingItems.map(item => item.type))];
                                return [id, uniqueName[0], uniqueDiscount[0],uniqueDiscountType[0]];
                            });

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

                            $('body').find('.ordersAddingTable').removeClass('d-none');
                            $('body').find('.loader').toggleClass('d-none');
                            $('body').find('#orders-total_price').val(Math.round(ordersTotalPriceSum));
                            $('body').find('#orders-total_count').val(Math.round(ordersTotalCount));
                            $('body').find('#orders-total_price_before_discount').val(Math.round(ordersBeforTotalPriceSum));
                            $('body').find('#orders-total_discount').val(Math.round(totalDiscount));
                        }
                    }
                })
            }
        })
        // console.log('4')
    })
    $('body').on('keyup','.countProduct', function (){
            let ordersTotalCount = 0;
            let ordersTotalPriceSum = 0;
            let ordersBeforTotalPriceSum = 0;
            let totalDiscount = 0;
            if ($(this).val() < 1 || $(this).val() === ""){
                $(this).val(1)
                $(this).closest('.tableNomenclature').find('.totalPrice').children('span').text($(this).closest('.tableNomenclature').find('.price').children('input').val() * $(this).val())
                $(this).closest('.tableNomenclature').find('.totalPrice').children('input').val($(this).closest('.tableNomenclature').find('.price').children('input').val() * $(this).val())
                $(this).closest('.tableNomenclature').find('.totalBeforePrice').children('span').text($(this).closest('.tableNomenclature').find('.beforePrice').children('input').val() * $(this).val())
                $(this).closest('.tableNomenclature').find('.totalBeforePrice').children('input').val($(this).closest('.tableNomenclature').find('.beforePrice').children('input').val() * $(this).val())
                $('.tableNomenclature').each(function (){
                    ordersTotalPriceSum += parseInt($(this).find('.totalPrice').children('input').val());
                    ordersTotalCount += parseInt($(this).find('.count').children('input').val());
                    ordersBeforTotalPriceSum += parseInt($(this).find('.totalBeforePrice').children('input').val());
                    totalDiscount += parseInt($(this).find('.discount').children('input').val()) * parseInt($(this).find('.count').children('input').val());
                })
                $('body').find('#orders-total_price').val(Math.round(ordersTotalPriceSum));
                $('body').find('#orders-total_count').val(Math.round(ordersTotalCount));
                $('body').find('#orders-total_price_before_discount').val(Math.round(ordersBeforTotalPriceSum));
                $('body').find('#orders-total_discount').val(Math.round(totalDiscount));
            }else {
                $(this).closest('.tableNomenclature').find('.totalPrice').children('span').text($(this).closest('.tableNomenclature').find('.price').children('input').val() * $(this).val())
                $(this).closest('.tableNomenclature').find('.totalPrice').children('input').val($(this).closest('.tableNomenclature').find('.price').children('input').val() * $(this).val())
                $(this).closest('.tableNomenclature').find('.totalBeforePrice').children('span').text($(this).closest('.tableNomenclature').find('.beforePrice').children('input').val() * $(this).val())
                $(this).closest('.tableNomenclature').find('.totalBeforePrice').children('input').val($(this).closest('.tableNomenclature').find('.beforePrice').children('input').val() * $(this).val())
                $('.tableNomenclature').each(function (){
                    ordersTotalPriceSum += parseInt($(this).find('.totalPrice').children('input').val());
                    ordersTotalCount += parseInt($(this).find('.count').children('input').val());
                    ordersBeforTotalPriceSum += parseInt($(this).find('.totalBeforePrice').children('input').val());
                    totalDiscount += parseInt($(this).find('.discount').children('input').val()) * parseInt($(this).find('.count').children('input').val());
                })
                $('body').find('#orders-total_price').val(Math.round(ordersTotalPriceSum));
                $('body').find('#orders-total_count').val(Math.round(ordersTotalCount));
                $('body').find('#orders-total_price_before_discount').val(Math.round(ordersBeforTotalPriceSum));
                $('body').find('#orders-total_discount').val(Math.round(totalDiscount));
            }
        // console.log('5')

        })
    $('body').on('click','.countProduct', function (){
        let ordersTotalCount = 0;
        let ordersTotalPriceSum = 0;
        let ordersBeforTotalPriceSum = 0;
        let totalDiscount = 0;
        if ($(this).val() < 1 || $(this).val() === ""){
            $(this).val(1)
            $(this).closest('.tableNomenclature').find('.totalPrice').children('span').text($(this).closest('.tableNomenclature').find('.price').children('input').val() * $(this).val())
            $(this).closest('.tableNomenclature').find('.totalPrice').children('input').val($(this).closest('.tableNomenclature').find('.price').children('input').val() * $(this).val())
            $(this).closest('.tableNomenclature').find('.totalBeforePrice').children('span').text($(this).closest('.tableNomenclature').find('.beforePrice').children('input').val() * $(this).val())
            $(this).closest('.tableNomenclature').find('.totalBeforePrice').children('input').val($(this).closest('.tableNomenclature').find('.beforePrice').children('input').val() * $(this).val())
            $('.tableNomenclature').each(function (){
                ordersTotalPriceSum += parseInt($(this).find('.totalPrice').children('input').val());
                ordersTotalCount += parseInt($(this).find('.count').children('input').val());
                ordersBeforTotalPriceSum += parseInt($(this).find('.totalBeforePrice').children('input').val());
                totalDiscount += parseInt($(this).find('.discount').children('input').val()) * parseInt($(this).find('.count').children('input').val());
            })
            $('body').find('#orders-total_price').val(Math.round(ordersTotalPriceSum));
            $('body').find('#orders-total_count').val(Math.round(ordersTotalCount));
            $('body').find('#orders-total_price_before_discount').val(Math.round(ordersBeforTotalPriceSum));
            $('body').find('#orders-total_discount').val(Math.round(totalDiscount));
        }else {
            $(this).closest('.tableNomenclature').find('.totalPrice').children('span').text($(this).closest('.tableNomenclature').find('.price').children('input').val() * $(this).val())
            $(this).closest('.tableNomenclature').find('.totalPrice').children('input').val($(this).closest('.tableNomenclature').find('.price').children('input').val() * $(this).val())
            $(this).closest('.tableNomenclature').find('.totalBeforePrice').children('span').text($(this).closest('.tableNomenclature').find('.beforePrice').children('input').val() * $(this).val())
            $(this).closest('.tableNomenclature').find('.totalBeforePrice').children('input').val($(this).closest('.tableNomenclature').find('.beforePrice').children('input').val() * $(this).val())
            $('.tableNomenclature').each(function (){
                ordersTotalPriceSum += parseInt($(this).find('.totalPrice').children('input').val());
                ordersTotalCount += parseInt($(this).find('.count').children('input').val());
                ordersBeforTotalPriceSum += parseInt($(this).find('.totalBeforePrice').children('input').val());
                totalDiscount += parseInt($(this).find('.discount').children('input').val()) * parseInt($(this).find('.count').children('input').val());
            })
            $('body').find('#orders-total_price').val(Math.round(ordersTotalPriceSum));
            $('body').find('#orders-total_count').val(Math.round(ordersTotalCount));
            $('body').find('#orders-total_price_before_discount').val(Math.round(ordersBeforTotalPriceSum));
            $('body').find('#orders-total_discount').val(Math.round(totalDiscount));
        }
        // console.log('6')
    })
    $('body').on('click', '.deleteItems',function (){
        let confirmed =  confirm("Այս ապրանքը դուք ուզում եք ջնջե՞լ:");
        if (confirmed){
            $(this).closest('.tableNomenclature').remove();
            alert('Հաջողությամբ ջնջված է:');
            let ordersTotalCount = 0;
            let ordersTotalPriceSum = 0;
            let ordersBeforTotalPriceSum = 0;
            let totalDiscount = 0;
            $('.tableNomenclature').each(function () {
                ordersTotalPriceSum += parseInt($(this).find('.totalPrice').children('input').val());
                ordersTotalCount += parseInt($(this).find('.count').children('input').val());
                ordersBeforTotalPriceSum += parseInt($(this).find('.totalBeforePrice').children('input').val());
                totalDiscount += parseInt($(this).find('.discount').children('input').val()) * parseInt($(this).find('.count').children('input').val());
            });
            $('body').find('#orders-total_price').val(Math.round(ordersTotalPriceSum));
            $('body').find('#orders-total_count').val(Math.round(ordersTotalCount));
            $('body').find('#orders-total_price_before_discount').val(Math.round(ordersBeforTotalPriceSum));
            $('body').find('#orders-total_discount').val(Math.round(totalDiscount));
        }
        // console.log('7')
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
        // console.log("8")
    })

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
                totalSum += parseInt($(this).children('.ordersAddCount').find('.ordersPriceInput').val()) * parseInt($(this).children('.ordersAddCount').find('.ordersCountInput').val());
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
                        acordingNumber++
                        trss[id.trim()] = `<tr class="tableNomenclature">
                                     <td>
                                        <span>`+acordingNumber+`</span>
                                        <input type="hidden" name="order_items[]" value="null">
                                        <input class="prodId" type="hidden" name="product_id[]" value="`+pars.product_id+`">
                                        <input class="nomId"  type="hidden" name="nom_id[]" value="`+pars.nomenclature_id+`">
                                        <input class="countDiscountId" type="hidden" name="count_discount_id[]" value="`+pars.count_discount_id+`">
                                        <input type="hidden" name="cost[]" value="`+pars.cost+`">
                                     </td>
                                     <td  class="name">`+pars.name+`</td>
                                     <td class="count">
                                        <input type="number" name="count_[]" value="`+pars.count+`" class="form-control countProductForUpdate">
                                     </td>
                                     <td class="discount">
                                        <span>`+Math.round(pars.discount)+`</span>
                                        <input type="hidden" name="discount[]" value="`+Math.round(pars.discount)+`">
                                     </td>
                                     <td class="beforePrice">
                                        <span>`+Math.round(pars.format_before_price)+`</span>
                                        <input type="hidden" name="beforePrice[]" value="`+Math.round(pars.format_before_price)+`">
                                     </td>
                                     <td class="price">
                                        <span>`+Math.round(pars.price)+`</span>
                                        <input type="hidden" name="price[]" value="`+Math.round(pars.price)+`">
                                     </td>
                                     <td class="totalBeforePrice">
                                        <span>`+Math.round(pars.format_before_price) * pars.count+`</span>
                                        <input type="hidden" name="total_before_price[]" value="`+Math.round(pars.format_before_price) * pars.count+`">
                                     </td>
                                     <td class="totalPrice">
                                        <span>`+Math.round(pars.price) * pars.count+`</span>
                                        <input type="hidden" name="total_price[]" value="`+Math.round(pars.price) * pars.count+`">
                                     </td>
                                     <td><button  type="button" class="btn rounded-pill btn-outline-danger deleteItems">Ջնջել</button></td>
                                 </tr>`.trim()
                        ordersTableLength--;
                        if(ordersTableLength == 0) {
                            for (let i in trss) {
                                if(trss[i] != ''){
                                    fromModal += trss[i];
                                }
                            }
                            $('.ordersAddingTable tbody').html('');
                            $('.ordersAddingTable tbody').html(old_table);
                            $('.ordersAddingTable tbody').append(fromModal);
                            let ordersTotalCount = 0;
                            let ordersTotalPriceSum = 0;
                            let ordersBeforTotalPriceSum = 0;
                            let totalDiscount = 0;
                            $('.tableNomenclature').each(function () {
                                    ordersTotalPriceSum += parseInt($(this).find('.totalPrice').children('input').val());
                                    ordersTotalCount += parseInt($(this).find('.count').children('input').val());
                                    ordersBeforTotalPriceSum += parseInt($(this).find('.totalBeforePrice').children('input').val());
                                    totalDiscount += parseInt($(this).find('.discount').children('input').val()) * parseInt($(this).find('.count').children('input').val());
                            })
                            $('body').find('#orders-total_price').val(Math.round(ordersTotalPriceSum));
                            $('body').find('#orders-total_count').val(Math.round(ordersTotalCount));
                            $('body').find('#orders-total_price_before_discount').val(Math.round(ordersBeforTotalPriceSum));
                            $('body').find('#orders-total_discount').val(Math.round(totalDiscount));
                            let allValues = [];
                            $('.tableNomenclature').each(function () {
                                let countDiscountValues = $(this).find('.countDiscountId').val().split(',');
                                countDiscountValues.forEach(function(value) {
                                    allValues.push(value.trim())
                                });
                            });
                            let uniqueArray = [...new Set(allValues)];
                            let convertedArray = uniqueArray.map(function(element) {
                                return isNaN(element) ? element : parseInt(element);
                            });

                            if (discount_name.length != 0){
                                $('.discountDesc tbody').html('');
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
        // // console.log(newTbody)
        // newTbody.append(old_table);
        // for (let i in trss) {
        //     if(trss[i] != ''){
        //         newTbody.append(trss[i]);
        //     }
        // }
        // newTbody.append(documentsTableBody);
        // // console.log(trss)
        // $('.documentsAddingTable tbody').replaceWith(newTbody);
        giveOldValues();
        // console.log('9')
    })

    function giveOldValues() {
        for (let argumentsKey in old_attrs) {
            let tr = $('body').find('#tr_'+argumentsKey);
            tr.find('.ordersAddCount').find('.ordersCountInput').val(old_attrs[argumentsKey].count);
            tr.find('.ordersAddCount').find('.ordersPriceInput').val(old_attrs[argumentsKey].price);
        }
        // console.log('10')
    }

    $('body').on('click', '.by_ajax_update',function () {
        var href_ = $(this).attr('data-href');
        getNom(href_);
        let clientId = $('#singleClients').val();
        let totalSum = 0;
        let countSum = 0;
        //
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
                totalSum += parseInt($(this).children('.ordersAddCount').find('.ordersPriceInput').val()) * parseInt($(this).children('.ordersAddCount').find('.ordersCountInput').val());
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
                        sequenceNumber++;
                        ordersBeforTotalPriceSum += Math.round(pars.format_before_price) * pars.count;
                        ordersTotalPriceSum += Math.round(pars.price) * pars.count;
                        ordersTotalCount += pars.count;
                        totalDiscount += Math.round(pars.discount) * pars.count;
                        trss[pars.product_id] = `<tr class="tableNomenclature">
                                     <td>
                                        <span>`+sequenceNumber+`</span>
                                        <input type="hidden" name="order_items[]" value="null">
                                        <input type="hidden" name="nomenclature_id[]" value="`+pars.nomenclature_id+`">
                                        <input type="hidden" name="count_discount_id[]" value="`+pars.count_discount_id+`">
                                        <input type="hidden" name="cost[]" value="`+pars.cost+`">
                                     </td>
                                     <td  class="name">`+pars.name+`</td>
                                     <td class="count">
                                        <input type="number" name="count_[]" value="`+pars.count+`" class="form-control countProduct">
                                     </td>
                                     <td class="discount">
                                        <span>`+Math.round(pars.discount)+`</span>
                                        <input type="hidden" name="discount[]" value="`+Math.round(pars.discount)+`">
                                     </td>
                                     <td class="beforePrice">
                                        <span>`+Math.round(pars.format_before_price)+`</span>
                                        <input type="hidden" name="beforePrice[]" value="`+Math.round(pars.format_before_price)+`">
                                     </td>
                                     <td class="price">
                                        <span>`+Math.round(pars.price)+`</span>
                                        <input type="hidden" name="price[]" value="`+Math.round(pars.price)+`">
                                     </td>
                                     <td class="totalBeforePrice">
                                        <span>`+Math.round(pars.format_before_price) * pars.count+`</span>
                                        <input type="hidden" name="totalBeforePrice[]" value="`+Math.round(pars.format_before_price) * pars.count+`">
                                     </td>
                                     <td class="totalPrice">
                                        <span>`+Math.round(pars.price) * pars.count+`</span>
                                        <input type="hidden" name="totalPrice[]" value="`+Math.round(pars.price) * pars.count+`">
                                     </td>
                                     <td><button  type="button" class="btn rounded-pill btn-outline-danger deleteItems">Ջնջել</button></td>
                                 </tr>`
                        // $('.ordersAddingTable tbody').parent().append(aaa);
                        ordersTableLength--;
                        if(ordersTableLength == 0){
                            let uniquePairs = [...new Set(discount_desc.flat().map(item => item.id))].map(id => {
                                let matchingItems = discount_desc.flat().filter(item => item.id === id);
                                let uniqueName = [...new Set(matchingItems.map(item => item.name))];
                                let uniqueDiscount = [...new Set(matchingItems.map(item => item.discount))];
                                let uniqueDiscountType = [...new Set(matchingItems.map(item => item.type))];
                                return [id, uniqueName[0], uniqueDiscount[0],uniqueDiscountType[0]];
                            });

                            uniquePairs.forEach((item,index) => {
                                discountBody += `<tr>
                                                     <td>`+(parseInt(index) + 1) +`</td>
                                                     <td>`+item[1]+`</td>
                                                     <td>`+(item[3] == 'percent' ? item[2] + ' %' : item[2] + ' դր․')+`</td>
                                                </tr>`
                            })
                            $('.discountDesc tbody').parent().append(discountBody);

                            $('body').find('.ordersAddingTable').removeClass('d-none');
                            $('body').find('.loader').toggleClass('d-none');
                            $('body').find('#orders-total_price').val(Math.round(ordersTotalPriceSum));
                            $('body').find('#orders-total_count').val(Math.round(ordersTotalCount));
                            $('body').find('#orders-total_price_before_discount').val(Math.round(ordersBeforTotalPriceSum));
                            $('body').find('#orders-total_discount').val(Math.round(totalDiscount));
                        }
                    }
                })
            }
        })
        // console.log('11',old_table)
    })


    $('body').on('keyup','.countProductForUpdate', function (){
        let ordersTotalCount = 0;
        let ordersTotalPriceSum = 0;
        let ordersBeforTotalPriceSum = 0;
        let totalDiscount = 0;
        if ($(this).val() === "" || $(this).val() < 1){
            $(this).val(1)
            $(this).closest('.tableNomenclature').find('.totalPrice').children('span').text($(this).closest('.tableNomenclature').find('.price').children('input').val() * $(this).val())
            $(this).closest('.tableNomenclature').find('.totalPrice').children('input').val($(this).closest('.tableNomenclature').find('.price').children('input').val() * $(this).val())
            $(this).closest('.tableNomenclature').find('.totalBeforePrice').children('span').text($(this).closest('.tableNomenclature').find('.beforePrice').children('input').val() * $(this).val())
            $(this).closest('.tableNomenclature').find('.totalBeforePrice').children('input').val($(this).closest('.tableNomenclature').find('.beforePrice').children('input').val() * $(this).val())
            $('.tableNomenclature').each(function () {
                ordersTotalPriceSum += parseInt($(this).find('.totalPrice').children('input').val());
                ordersTotalCount += parseInt($(this).find('.count').children('input').val());
                ordersBeforTotalPriceSum += parseInt($(this).find('.totalBeforePrice').children('input').val());
                totalDiscount += parseInt($(this).find('.discount').children('input').val()) * parseInt($(this).find('.count').children('input').val());
            })
            $('body').find('#orders-total_price').val(Math.round(ordersTotalPriceSum));
            $('body').find('#orders-total_count').val(Math.round(ordersTotalCount));
            $('body').find('#orders-total_price_before_discount').val(Math.round(ordersBeforTotalPriceSum));
            $('body').find('#orders-total_discount').val(Math.round(totalDiscount));
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
                            this_.closest('.tableNomenclature').find('.totalPrice').children('span').text(this_.closest('.tableNomenclature').find('.price').children('input').val() * this_.val())
                            this_.closest('.tableNomenclature').find('.totalPrice').children('input').val(this_.closest('.tableNomenclature').find('.price').children('input').val() * this_.val())
                            this_.closest('.tableNomenclature').find('.totalBeforePrice').children('span').text(this_.closest('.tableNomenclature').find('.beforePrice').children('input').val() * this_.val())
                            this_.closest('.tableNomenclature').find('.totalBeforePrice').children('input').val(this_.closest('.tableNomenclature').find('.beforePrice').children('input').val() * this_.val())
                            $('body').find('.tableNomenclature').each(function () {
                                ordersTotalPriceSum += parseInt($(this).find('.totalPrice').children('input').val());
                                ordersTotalCount += parseInt($(this).find('.count').children('input').val());
                                ordersBeforTotalPriceSum += parseInt($(this).find('.totalBeforePrice').children('input').val());
                                totalDiscount += parseInt($(this).find('.discount').children('input').val()) * parseInt($(this).find('.count').children('input').val());
                            })
                            $('body').find('#orders-total_price').val(Math.round(ordersTotalPriceSum));
                            $('body').find('#orders-total_count').val(Math.round(ordersTotalCount));
                            $('body').find('#orders-total_price_before_discount').val(Math.round(ordersBeforTotalPriceSum));
                            $('body').find('#orders-total_discount').val(Math.round(totalDiscount));

                        }else if (param.count === 'dontExists'){
                            alert('Նման ապրանք պահեստում գոյություն չունի')
                            this_.val('')
                        }
                        else if(param.count === 'exists'){
                            this_.closest('.tableNomenclature').find('.totalPrice').children('span').text(this_.closest('.tableNomenclature').find('.price').children('input').val() * this_.val())
                            this_.closest('.tableNomenclature').find('.totalPrice').children('input').val(this_.closest('.tableNomenclature').find('.price').children('input').val() * this_.val())
                            this_.closest('.tableNomenclature').find('.totalBeforePrice').children('span').text(this_.closest('.tableNomenclature').find('.beforePrice').children('input').val() * this_.val())
                            this_.closest('.tableNomenclature').find('.totalBeforePrice').children('input').val(this_.closest('.tableNomenclature').find('.beforePrice').children('input').val() * this_.val())
                            $('body').find('.tableNomenclature').each(function () {
                                ordersTotalPriceSum += parseInt($(this).find('.totalPrice').children('input').val());
                                ordersTotalCount += parseInt($(this).find('.count').children('input').val());
                                ordersBeforTotalPriceSum += parseInt($(this).find('.totalBeforePrice').children('input').val());
                                totalDiscount += parseInt($(this).find('.discount').children('input').val()) * parseInt($(this).find('.count').children('input').val());
                            })
                            $('body').find('#orders-total_price').val(Math.round(ordersTotalPriceSum));
                            $('body').find('#orders-total_count').val(Math.round(ordersTotalCount));
                            $('body').find('#orders-total_price_before_discount').val(Math.round(ordersBeforTotalPriceSum));
                            $('body').find('#orders-total_discount').val(Math.round(totalDiscount));
                        }
                    }
                }
            })

        }
        // console.log('12')
    })
    $('body').on('click','.countProductForUpdate', function (){
        let ordersTotalCount = 0;
        let ordersTotalPriceSum = 0;
        let ordersBeforTotalPriceSum = 0;
        let totalDiscount = 0;
        var this_ = $(this);
        if ($(this).val() === "" || $(this).val() < 1){
            $(this).val(1)
            $(this).closest('.tableNomenclature').find('.totalPrice').children('span').text($(this).closest('.tableNomenclature').find('.price').children('input').val() * $(this).val())
            $(this).closest('.tableNomenclature').find('.totalPrice').children('input').val($(this).closest('.tableNomenclature').find('.price').children('input').val() * $(this).val())
            $(this).closest('.tableNomenclature').find('.totalBeforePrice').children('span').text($(this).closest('.tableNomenclature').find('.beforePrice').children('input').val() * $(this).val())
            $(this).closest('.tableNomenclature').find('.totalBeforePrice').children('input').val($(this).closest('.tableNomenclature').find('.beforePrice').children('input').val() * $(this).val())
            $('.tableNomenclature').each(function () {
                ordersTotalPriceSum += parseInt($(this).find('.totalPrice').children('input').val());
                ordersTotalCount += parseInt($(this).find('.count').children('input').val());
                ordersBeforTotalPriceSum += parseInt($(this).find('.totalBeforePrice').children('input').val());
                totalDiscount += parseInt($(this).find('.discount').children('input').val()) * parseInt($(this).find('.count').children('input').val());
            })
            $('body').find('#orders-total_price').val(Math.round(ordersTotalPriceSum));
            $('body').find('#orders-total_count').val(Math.round(ordersTotalCount));
            $('body').find('#orders-total_price_before_discount').val(Math.round(ordersBeforTotalPriceSum));
            $('body').find('#orders-total_discount').val(Math.round(totalDiscount));
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
                                this_.closest('.tableNomenclature').find('.totalPrice').children('span').text(this_.closest('.tableNomenclature').find('.price').children('input').val() * this_.val())
                                this_.closest('.tableNomenclature').find('.totalPrice').children('input').val(this_.closest('.tableNomenclature').find('.price').children('input').val() * this_.val())
                                this_.closest('.tableNomenclature').find('.totalBeforePrice').children('span').text(this_.closest('.tableNomenclature').find('.beforePrice').children('input').val() * this_.val())
                                this_.closest('.tableNomenclature').find('.totalBeforePrice').children('input').val(this_.closest('.tableNomenclature').find('.beforePrice').children('input').val() * this_.val())
                                $('body').find('.tableNomenclature').each(function () {
                                    ordersTotalPriceSum += parseInt($(this).find('.totalPrice').children('input').val());
                                    ordersTotalCount += parseInt($(this).find('.count').children('input').val());
                                    ordersBeforTotalPriceSum += parseInt($(this).find('.totalBeforePrice').children('input').val());
                                    totalDiscount += parseInt($(this).find('.discount').children('input').val()) * parseInt($(this).find('.count').children('input').val());
                                })
                                $('body').find('#orders-total_price').val(Math.round(ordersTotalPriceSum));
                                $('body').find('#orders-total_count').val(Math.round(ordersTotalCount));
                                $('body').find('#orders-total_price_before_discount').val(Math.round(ordersBeforTotalPriceSum));
                                $('body').find('#orders-total_discount').val(Math.round(totalDiscount));

                            }else if (param.count === 'dontExists'){
                                alert('Նման ապրանք պահեստում գոյություն չունի')
                                this_.val('')
                            }
                            else if(param.count === 'exists'){
                                this_.closest('.tableNomenclature').find('.totalPrice').children('span').text(this_.closest('.tableNomenclature').find('.price').children('input').val() * this_.val())
                                this_.closest('.tableNomenclature').find('.totalPrice').children('input').val(this_.closest('.tableNomenclature').find('.price').children('input').val() * this_.val())
                                this_.closest('.tableNomenclature').find('.totalBeforePrice').children('span').text(this_.closest('.tableNomenclature').find('.beforePrice').children('input').val() * this_.val())
                                this_.closest('.tableNomenclature').find('.totalBeforePrice').children('input').val(this_.closest('.tableNomenclature').find('.beforePrice').children('input').val() * this_.val())
                                $('body').find('.tableNomenclature').each(function () {
                                    ordersTotalPriceSum += parseInt($(this).find('.totalPrice').children('input').val());
                                    ordersTotalCount += parseInt($(this).find('.count').children('input').val());
                                    ordersBeforTotalPriceSum += parseInt($(this).find('.totalBeforePrice').children('input').val());
                                    totalDiscount += parseInt($(this).find('.discount').children('input').val()) * parseInt($(this).find('.count').children('input').val());
                                })
                                $('body').find('#orders-total_price').val(Math.round(ordersTotalPriceSum));
                                $('body').find('#orders-total_count').val(Math.round(ordersTotalCount));
                                $('body').find('#orders-total_price_before_discount').val(Math.round(ordersBeforTotalPriceSum));
                                $('body').find('#orders-total_discount').val(Math.round(totalDiscount));
                            }
                        }
                    }
                })
        }
        // console.log('13')
    })
    $('body').on('click', '.deleteItemsFromDB',function (){
        let confirmed =  confirm("Այս ապրանքը դուք ուզում եք ջնջե՞լ:");
        if (confirmed){
            var this_ = $(this);
            var itemId = this_.closest('.tableNomenclature').find('.orderItemsId').val();
            var nomId = this_.closest('.tableNomenclature').find('.nomId').val();
            var totalPriceBeforeDiscount = $('#orders-total_price_before_discount').val() - (this_.closest('tr').find('.beforePrice').find('input').val() * this_.closest('tr').find('.countProductForUpdate').val());
            var totalDiscount = $('#orders-total_discount').val() - (this_.closest('tr').find('.discount').find('input').val() * this_.closest('tr').find('.countProductForUpdate').val());
            var totalPrice = $('#orders-total_price').val() - (this_.closest('tr').find('.price').find('input').val() * this_.closest('tr').find('.countProductForUpdate').val());
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
                            ordersTotalPriceSum += parseInt($(this).find('.totalPrice').children('input').val());
                            ordersTotalCount += parseInt($(this).find('.count').children('input').val());
                            ordersBeforTotalPriceSum += parseInt($(this).find('.totalBeforePrice').children('input').val());
                            totalDiscount += parseInt($(this).find('.discount').children('input').val()) * parseInt($(this).find('.count').children('input').val());
                        })
                        $('body').find('#orders-total_price').val(Math.round(ordersTotalPriceSum));
                        $('body').find('#orders-total_count').val(Math.round(ordersTotalCount));
                        $('body').find('#orders-total_price_before_discount').val(Math.round(ordersBeforTotalPriceSum));
                        $('body').find('#orders-total_discount').val(Math.round(totalDiscount));
                    }else {
                        alert('Գոյություն չունի կամ հաջողությամբ չի կատարվել ջնջումը:');
                    }
                }
            })

        }
        // console.log('14')
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
                    // else if(parse.count === 'exists'){
                    //
                    // }
                }
            }
        })
        // console.log('15')
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
        // console.log('16')
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
        $('.ordersAddingTable tbody').html('');
        let orders_date = $('#orders-orders_date').val();
        let csrfToken = $('meta[name="csrf-token"]').attr("content");
        var ordersTotalPriceSum = 0;
        var ordersTotalCount = 0;
        var ordersBeforTotalPriceSum = 0;
        var totalDiscount = 0;
        var discount_desc = [];
        var discountBody = '';
        $('body').find('.loader').toggleClass('d-none');
        $('body').find('.ordersAddingTable').addClass('d-none');
        var ordersTableLength = 0;
        var sequenceNumber = 0;
        $('.addOrdersTableTr').each(function () {
            if ($(this).find('.ordersCountInput').val() != '') {
                countSum += parseInt($(this).children('.ordersAddCount').find('.ordersCountInput').val());
                totalSum += parseInt($(this).children('.ordersAddCount').find('.ordersPriceInput').val()) * parseInt($(this).children('.ordersAddCount').find('.ordersCountInput').val());
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
                        sequenceNumber++;
                        ordersBeforTotalPriceSum += Math.round(pars.format_before_price) * pars.count;
                        ordersTotalPriceSum += Math.round(pars.price) * pars.count;
                        ordersTotalCount += pars.count;
                        totalDiscount += Math.round(pars.discount) * pars.count;
                        trss[pars.product_id] = `<tr class="tableNomenclature">
                                     <td>
                                        <span>`+sequenceNumber+`</span>
                                        <input type="hidden" name="order_items[]" value="`+pars.product_id+`">
                                        <input type="hidden" name="nomenclature_id[]" value="`+pars.nomenclature_id+`">
                                        <input type="hidden" name="count_discount_id[]" value="`+pars.count_discount_id+`">
                                        <input type="hidden" name="cost[]" value="`+pars.cost+`">
                                     </td>
                                     <td  class="name">`+pars.name+`</td>
                                     <td class="count">
                                        <input type="number" name="count_[]" value="`+pars.count+`" class="form-control countProduct">
                                     </td>
                                     <td class="discount">
                                        <span>`+Math.round(pars.discount)+`</span>
                                        <input type="hidden" name="discount[]" value="`+Math.round(pars.discount)+`">
                                     </td>
                                     <td class="beforePrice">
                                        <span>`+Math.round(pars.format_before_price)+`</span>
                                        <input type="hidden" name="beforePrice[]" value="`+Math.round(pars.format_before_price)+`">
                                     </td>
                                     <td class="price">
                                        <span>`+Math.round(pars.price)+`</span>
                                        <input type="hidden" name="price[]" value="`+Math.round(pars.price)+`">
                                     </td>
                                     <td class="totalBeforePrice">
                                        <span>`+Math.round(pars.format_before_price) * pars.count+`</span>
                                        <input type="hidden" name="totalBeforePrice[]" value="`+Math.round(pars.format_before_price) * pars.count+`">
                                     </td>
                                     <td class="totalPrice">
                                        <span>`+Math.round(pars.price) * pars.count+`</span>
                                        <input type="hidden" name="totalPrice[]" value="`+Math.round(pars.price) * pars.count+`">
                                     </td>
                                     <td><button  type="button" class="btn rounded-pill btn-outline-danger deleteItems">Ջնջել</button></td>
                                 </tr>`
                        // $('.ordersAddingTable tbody').parent().append(aaa);
                        ordersTableLength--;
                        if(ordersTableLength == 0){
                            let uniquePairs = [...new Set(discount_desc.flat().map(item => item.id))].map(id => {
                                let matchingItems = discount_desc.flat().filter(item => item.id === id);
                                let uniqueName = [...new Set(matchingItems.map(item => item.name))];
                                let uniqueDiscount = [...new Set(matchingItems.map(item => item.discount))];
                                let uniqueDiscountType = [...new Set(matchingItems.map(item => item.type))];
                                return [id, uniqueName[0], uniqueDiscount[0],uniqueDiscountType[0]];
                            });

                            uniquePairs.forEach((item,index) => {
                                discountBody += `<tr>
                                                     <td>`+(parseInt(index) + 1) +`</td>
                                                     <td>`+item[1]+`</td>
                                                     <td>`+(item[3] == 'percent' ? item[2] + ' %' : item[2] + ' դր․')+`</td>
                                                </tr>`
                            })
                            $('.discountDesc tbody').parent().append(discountBody);

                            $('body').find('.ordersAddingTable').removeClass('d-none');
                            $('body').find('.loader').toggleClass('d-none');
                            $('body').find('#orders-total_price').val(Math.round(ordersTotalPriceSum));
                            $('body').find('#orders-total_count').val(Math.round(ordersTotalCount));
                            $('body').find('#orders-total_price_before_discount').val(Math.round(ordersBeforTotalPriceSum));
                            $('body').find('#orders-total_discount').val(Math.round(totalDiscount));
                        }
                    }
                })
            }
        })
        // console.log('17')
    })

    $('body').on('keyup', '.searchForOrder',function () {
        var nomenclature = $(this).val();
        let current_href = $('body').find('.active .by_ajax').data('href');
        getNom(current_href + '&nomenclature=' + nomenclature);
        // console.log('18')
    })

    $('body').on('click', '.by_ajax',function () {

        var href_ = $(this).attr('data-href');
        getNom(href_);
        let clientId = $('#singleClients').val();
        let totalSum = 0;
        let countSum = 0;
        $('.ordersAddingTable tbody').html('');
        let orders_date = $('#orders-orders_date').val();
        let csrfToken = $('meta[name="csrf-token"]').attr("content");
        var ordersTotalPriceSum = 0;
        var ordersTotalCount = 0;
        var ordersBeforTotalPriceSum = 0;
        var totalDiscount = 0;
        var discount_desc = [];
        var discountBody = '';
        $('body').find('.loader').toggleClass('d-none');
        $('body').find('.ordersAddingTable').addClass('d-none');
        var ordersTableLength = 0;
        var sequenceNumber = 0;
        $('.addOrdersTableTr').each(function () {
            if ($(this).find('.ordersCountInput').val() != '') {
                countSum += parseInt($(this).children('.ordersAddCount').find('.ordersCountInput').val());
                totalSum += parseInt($(this).children('.ordersAddCount').find('.ordersPriceInput').val()) * parseInt($(this).children('.ordersAddCount').find('.ordersCountInput').val());
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
                        // let uniquePairs = [...new Set(discount_desc.flat().map(item => item.id))].map(id => {
                        //     let matchingItems = discount_desc.flat().filter(item => item.id === id);
                        //     let uniqueName = [...new Set(matchingItems.map(item => item.name))];
                        //     return [id, uniqueName[0]];
                        // });
                        sequenceNumber++;
                        ordersBeforTotalPriceSum += Math.round(pars.format_before_price) * pars.count;
                        ordersTotalPriceSum += Math.round(pars.price) * pars.count;
                        ordersTotalCount += pars.count;
                        totalDiscount += Math.round(pars.discount) * pars.count;
                        trss[pars.product_id] = `<tr class="tableNomenclature">
                                     <td>
                                        <span>`+sequenceNumber+`</span>
                                        <input type="hidden" name="order_items[]" value="`+pars.product_id+`">
                                        <input type="hidden" name="nomenclature_id[]" value="`+pars.nomenclature_id+`">
                                        <input type="hidden" name="count_discount_id[]" value="`+pars.count_discount_id+`">
                                        <input type="hidden" name="cost[]" value="`+pars.cost+`">
                                     </td>
                                     <td  class="name">`+pars.name+`</td>
                                     <td class="count">
                                        <input type="number" name="count_[]" value="`+pars.count+`" class="form-control countProduct">
                                     </td>
                                     <td class="discount">
                                        <span>`+Math.round(pars.discount)+`</span>
                                        <input type="hidden" name="discount[]" value="`+Math.round(pars.discount)+`">
                                     </td>
                                     <td class="beforePrice">
                                        <span>`+Math.round(pars.format_before_price)+`</span>
                                        <input type="hidden" name="beforePrice[]" value="`+Math.round(pars.format_before_price)+`">
                                     </td>
                                     <td class="price">
                                        <span>`+Math.round(pars.price)+`</span>
                                        <input type="hidden" name="price[]" value="`+Math.round(pars.price)+`">
                                     </td>
                                     <td class="totalBeforePrice">
                                        <span>`+Math.round(pars.format_before_price) * pars.count+`</span>
                                        <input type="hidden" name="totalBeforePrice[]" value="`+Math.round(pars.format_before_price) * pars.count+`">
                                     </td>
                                     <td class="totalPrice">
                                        <span>`+Math.round(pars.price) * pars.count+`</span>
                                        <input type="hidden" name="totalPrice[]" value="`+Math.round(pars.price) * pars.count+`">
                                     </td>
                                     <td><button  type="button" class="btn rounded-pill btn-outline-danger deleteItems">Ջնջել</button></td>
                                 </tr>`
                        // $('.ordersAddingTable tbody').parent().append(aaa);
                        ordersTableLength--;
                        if(ordersTableLength == 0){
                            let uniquePairs = [...new Set(discount_desc.flat().map(item => item.id))].map(id => {
                                let matchingItems = discount_desc.flat().filter(item => item.id === id);
                                let uniqueName = [...new Set(matchingItems.map(item => item.name))];
                                let uniqueDiscount = [...new Set(matchingItems.map(item => item.discount))];
                                let uniqueDiscountType = [...new Set(matchingItems.map(item => item.type))];
                                return [id, uniqueName[0], uniqueDiscount[0],uniqueDiscountType[0]];
                            });

                            uniquePairs.forEach((item,index) => {
                                discountBody += `<tr>
                                                     <td>`+(parseInt(index) + 1) +`</td>
                                                     <td>`+item[1]+`</td>
                                                     <td>`+(item[3] == 'percent' ? item[2] + ' %' : item[2] + ' դր․')+`</td>
                                                </tr>`
                            })
                            $('.discountDesc tbody').parent().append(discountBody);

                            $('body').find('.ordersAddingTable').removeClass('d-none');
                            $('body').find('.loader').toggleClass('d-none');
                            $('body').find('#orders-total_price').val(Math.round(ordersTotalPriceSum));
                            $('body').find('#orders-total_count').val(Math.round(ordersTotalCount));
                            $('body').find('#orders-total_price_before_discount').val(Math.round(ordersBeforTotalPriceSum));
                            $('body').find('#orders-total_discount').val(Math.round(totalDiscount));
                        }
                    }
                })
            }
        })
        // console.log('20')
    })
    function getNom(href_) {
        let url_id = window.location.href;
        let url = new URL(url_id);
        let urlId = url.searchParams.get("id");
        $.ajax({
            url:href_,
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
        // console.log('21')
    }
})
