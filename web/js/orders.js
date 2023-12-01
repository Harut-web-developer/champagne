$(document).ready(function () {
    $('body').on('click','.create', function(){
        var totalSum = 0;
        var countSum = 0;
        var addOrdersTableBody = '';
        var newTbody = $('<tbody></tbody>');
        $('.addOrdersTableTr').each(function () {
            if ($(this).find("input:checkbox").is(':checked')) {
                let id = $(this).find("input:checkbox").attr('data-id');
                let product_id = $(this).find('.productIdInput').data('product');
                let name = $(this).children(".nomenclatureName").text();
                let count = parseFloat($(this).children('.ordersAddCount').find('.ordersCountInput').val());
                let price = +parseFloat($(this).children('.ordersAddCount').find('.ordersPriceInput').val()).toFixed(2);
                let cost = $(this).children('.ordersAddCount').find('.ordersCostInput').val();
                let discount = $(this).children('.ordersAddCount').find('.ordersDiscountInput').val();
                let priceBeforeDiscount = $(this).children('.ordersAddCount').find('.ordersPriceBrforeDiscount').val();
                let total = +parseFloat(price * count).toFixed(2);
                addOrdersTableBody +=`<tr class="tableNomenclature">
                                        <th>`+id+` <input type="hidden" name="order_items[]" value="`+id+`"><input type="hidden" name="product_id[]" value="`+product_id+`"></th>
                                        <td class="name">`+name+`</td>
                                        <td class="count"><input type="number" name="count_[]" value="`+count+`" class="form-control countProduct"></td>
                                        <td class="price">`+price+` <input type="hidden" name="price[]" value="`+price+`"></td>
                                        <td class="cost">`+cost+` <input type="hidden" name="cost[]" value="`+cost+`"></td>
<!--                                        <td class="discount">`+discount+` <input type="hidden" name="discount[]" value="`+discount+`"></td>-->
<!--                                        <td class="priceBeforeDiscount">`+priceBeforeDiscount+` <input type="hidden" name="priceBeforeDiscount[]" value="`+priceBeforeDiscount+`"></td>-->
                                        <td class="total"><span>`+total+`</span><input type="hidden" name="total[]" value="`+total+`"></td>
                                        <td><button  type="button" class="btn rounded-pill btn-outline-danger deleteItems">Ջնջել</button></td>
                                     </tr>`;
            }
        })
        newTbody.append(addOrdersTableBody);
        $('.ordersAddingTable tbody').replaceWith(newTbody);
            $('.tableNomenclature').each(function (){
                totalSum += +parseFloat($(this).find('.total').children('input').val()).toFixed(2);
                countSum += +parseFloat($(this).find('.count').children('input').val()).toFixed(2);
            })
            $('#orders-total_price').attr('value', totalSum);
            $('#orders-total_count').attr('value', countSum);


    })
        $('body').on('keyup','.countProduct', function (){
            if ($(this).val() < 1 || $(this).val() === ""){
                $(this).closest('.tableNomenclature').remove();
            }
                var totalSum = 0;
                var countSum = 0;
                $(this).closest('.tableNomenclature').find('.total').children('span').text(parseFloat($(this).val() * $(this).closest('.tableNomenclature').find('.price').children('input').val()).toFixed(2))
                $(this).closest('.tableNomenclature').find('.total').children('input').val(parseFloat($(this).val() * $(this).closest('.tableNomenclature').find('.price').children('input').val()).toFixed(2))
                $('.tableNomenclature').each(function (){
                    totalSum += +parseFloat($(this).find('.total').children('input').val()).toFixed(2);
                    countSum += +parseFloat($(this).find('.count').children('input').val()).toFixed(2);
                })
                $('#orders-total_price').attr('value', totalSum);
                $('#orders-total_count').attr('value', countSum);
        })
    $('body').on('click','.countProduct', function (){
        if ($(this).val() < 1 || $(this).val() === ""){
            $(this).closest('.tableNomenclature').remove();
        }
            var totalSum = 0;
            var countSum = 0;
            $(this).closest('.tableNomenclature').find('.total').children('span').text(parseFloat($(this).val() * $(this).closest('.tableNomenclature').find('.price').children('input').val()).toFixed(2))
            $(this).closest('.tableNomenclature').find('.total').children('input').val(parseFloat($(this).val() * $(this).closest('.tableNomenclature').find('.price').children('input').val()).toFixed(2))
            $('.tableNomenclature').each(function (){
                totalSum += +parseFloat($(this).find('.total').children('input').val()).toFixed(2);
                countSum += +parseFloat($(this).find('.count').children('input').val()).toFixed(2);
            })
            // console.log(totalSum)
            $('#orders-total_price').attr('value', totalSum);
            $('#orders-total_count').attr('value', countSum);
    })
    $('body').on('click', '.deleteItems',function (){
        let confirmed =  confirm("Are you sure want to delete this item");
        if (confirmed){
            $(this).closest('.tableNomenclature').remove();
            alert('deleted successfully');
            let totalSum = 0;
            var countSum = 0;
            $('.tableNomenclature').each(function () {
                totalSum += +parseFloat($(this).find('.total').children('input').val()).toFixed(2);
                countSum += +parseFloat($(this).find('.count').children('input').val()).toFixed(2);
            });
            $('#orders-total_price').attr('value', totalSum);
            $('#orders-total_count').attr('value', countSum);
        }
    })
    $('body').on('click','.update', function(){
        var totalSum = 0;
        var countSum = 0;
        var addOrdersTableBody = '';
        $('.addOrdersTableTr').each(function () {
            if ($(this).find("input:checkbox").is(':checked')) {
                let id = $(this).find("input:checkbox").attr('data-id');
                let product_id = $(this).find('.productIdInput').data('product');
                let name = $(this).children(".nomenclatureName").text();
                let count = parseFloat($(this).children('.ordersAddCount').find('.ordersCountInput').val());
                let price = +parseFloat($(this).children('.ordersAddCount').find('.ordersPriceInput').val()).toFixed(2);
                let cost = $(this).children('.ordersAddCount').find('.ordersCostInput').val();
                let discount = $(this).children('.ordersAddCount').find('.ordersDiscountInput').val();
                let priceBeforeDiscount = $(this).children('.ordersAddCount').find('.ordersPriceBrforeDiscount').val();
                let total = +parseFloat(price * count).toFixed(2);
                addOrdersTableBody +=`<tr class="tableNomenclature">
                                        <th>`+id+` <input type="hidden" name="order_items[]" value="null"><input type="hidden" name="product_id[]" value="`+product_id+`"></th>
                                        <td class="name">`+name+`</td>
                                        <td class="count"><input type="number" name="count_[]" value="`+count+`" class="form-control countProduct"></td>
                                        <td class="price">`+price+` <input type="hidden" name="price[]" value="`+price+`"></td>
                                        <td class="cost">`+cost+` <input type="hidden" name="cost[]" value="`+cost+`"></td>
<!--                                        // <td class="discount">`+discount+` <input type="hidden" name="discount[]" value="`+discount+`"></td>-->
<!--                                        // <td class="priceBeforeDiscount">`+priceBeforeDiscount+` <input type="hidden" name="priceBeforeDiscount[]" value="`+priceBeforeDiscount+`"></td>-->
                                        <td class="total"><span>`+total+`</span><input type="hidden" name="total[]" value="`+total+`"></td>
                                        <td><button  type="button" class="btn rounded-pill btn-outline-danger deleteItems">Ջնջել</button></td>
                                     </tr>`;
            }
        })
        $('.ordersAddingTable tbody').parent().append(addOrdersTableBody);
        $('.tableNomenclature').each(function (){
            totalSum += +parseFloat($(this).find('.total').children('input').val()).toFixed(2);
            countSum += +parseFloat($(this).find('.count').children('input').val()).toFixed(2);
        })
        $('#orders-total_price').attr('value', totalSum);
        $('#orders-total_count').attr('value', countSum);
    })
    $('body').on('keyup','.countProductForUpdate', function (){
        if ($(this).val() < 1 || $(this).val() === ""){
            var this_ = $(this);
            var itemId = this_.closest('.tableNomenclature').find('.orderItemsId').val();
            var csrfToken = $('meta[name="csrf-token"]').attr("content");
            $.ajax({
                url:'/orders/delete-items',
                method:'post',
                datatype:'json',
                data:{
                  itemId:itemId,
                    _csrf:csrfToken
                },
                success:function (data){
                    if (data === 'true'){
                        this_.closest('.tableNomenclature').remove();
                    }else {
                        alert('dont exist item or unsuccessfuly deleted');
                    }
                }
            })
        }
            var totalSum = 0;
            var countSum = 0;
            $(this).closest('.tableNomenclature').find('.total').children('span').text(parseFloat($(this).val() * $(this).closest('.tableNomenclature').find('.price').children('input').val()).toFixed(2))
            $(this).closest('.tableNomenclature').find('.total').children('input').val(parseFloat($(this).val() * $(this).closest('.tableNomenclature').find('.price').children('input').val()).toFixed(2))
            $('.tableNomenclature').each(function (){
                totalSum += +parseFloat($(this).find('.total').children('input').val()).toFixed(2);
                countSum += +parseFloat($(this).find('.count').children('input').val()).toFixed(2);
            })
            $('#orders-total_price').attr('value', totalSum);
            $('#orders-total_count').attr('value', countSum);

    })
    $('body').on('click','.countProductForUpdate', function (){
        if ($(this).val() < 1 || $(this).val() === ""){
            var this_ = $(this);
            var itemId = this_.closest('.tableNomenclature').find('.orderItemsId').val();
            var csrfToken = $('meta[name="csrf-token"]').attr("content");
            $.ajax({
                url:'/orders/delete-items',
                method:'post',
                datatype:'json',
                data:{
                    itemId:itemId,
                    _csrf:csrfToken
                },
                success:function (data){
                    if (data === 'true'){
                        this_.closest('.tableNomenclature').remove();
                    }else {
                        alert('dont exist item or unsuccessfuly deleted');
                    }
                }
            })
        }
        var totalSum = 0;
        var countSum = 0;
        $(this).closest('.tableNomenclature').find('.total').children('span').text(parseFloat($(this).val() * $(this).closest('.tableNomenclature').find('.price').children('input').val()).toFixed(2))
        $(this).closest('.tableNomenclature').find('.total').children('input').val(parseFloat($(this).val() * $(this).closest('.tableNomenclature').find('.price').children('input').val()).toFixed(2))
        $('.tableNomenclature').each(function (){
            totalSum += +parseFloat($(this).find('.total').children('input').val()).toFixed(2);
            countSum += +parseFloat($(this).find('.count').children('input').val()).toFixed(2);
        })
        // console.log(totalSum)
        $('#orders-total_price').attr('value', totalSum);
        $('#orders-total_count').attr('value', countSum);
    })
    $('body').on('click', '.deleteItemsFromDB',function (){
        let confirmed =  confirm("Are you sure want to delete this item");
        if (confirmed){
            var this_ = $(this);
            var itemId = this_.closest('.tableNomenclature').find('.orderItemsId').val();
            var csrfToken = $('meta[name="csrf-token"]').attr("content");
            $.ajax({
                url:'/orders/delete-items',
                method:'post',
                datatype:'json',
                data:{
                    itemId:itemId,
                    _csrf:csrfToken
                },
                success:function (data){
                    if (data === 'true'){
                        this_.closest('.tableNomenclature').remove();
                        alert('deleted successfully');
                        let totalSum = 0;
                        var countSum = 0;
                        $('.tableNomenclature').each(function () {
                            totalSum += +parseFloat($(this).find('.total').children('input').val()).toFixed(2);
                            countSum += +parseFloat($(this).find('.count').children('input').val()).toFixed(2);
                        });
                        $('#orders-total_price').attr('value', totalSum);
                        $('#orders-total_count').attr('value', countSum);
                    }else {
                        alert('dont exist item or unsuccessfuly deleted');
                    }
                }
            })

        }

    })
    $('body').on('keyup','.ordersCountInput',function (){
        var this_ = $(this);
        var id = this_.closest('.addOrdersTableTr').find("input:checkbox").attr('data-id');
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
                let parse = JSON.parse(data)
                if (data){
                    if (parse.count === 'nullable'){
                        this_.val('')
                    }else if (parse.count === 'countMore'){
                        this_.val('')
                        alert('Պահեստում նման քանակի ապրանք չկա');
                    }else if (parse.count === 'dontExists'){
                        alert('Նման ապրանք պահեստում գոյություն չունի')
                    }
                    // else if(parse.count === 'exists'){
                    //
                    // }
                }
            }
        })

    })
    $('body').on('click','.ordersCountInput',function (){
        var this_ = $(this);
        var id = this_.closest('.addOrdersTableTr').find("input:checkbox").attr('data-id');
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
                let parse = JSON.parse(data)
                if (data){
                    if (parse.count === 'nullable'){
                        this_.val('')
                    }else if (parse.count === 'countMore'){
                        this_.val('')
                        alert('Պահեստում նման քանակի ապրանք չկա');
                    }else if (parse.count === 'dontExists'){
                        alert('Նման ապրանք պահեստում գոյություն չունի')
                    }
                    // else if(parse.count === 'exists'){
                    //
                    // }
                }
            }
        })

    })













})
