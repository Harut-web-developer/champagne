$(document).ready(function () {
    $('.js-example-basic-single').select2();

    $('body').on('click','.create', function(){
        var totalSum = 0;
        var countSum = 0;
        var addOrdersTableBody = '';
        var newTbody = $('<tbody></tbody>');
        $('.addOrdersTableTr').each(function () {
            if ($(this).find("input:checkbox").is(':checked') && $(this).find('.ordersCountInput').val() != '') {
                let id = $(this).find("input:checkbox").attr('data-id');
                let nomenclature_id = $(this).find('.productIdInput').data('product');
                let name = $(this).children(".nomenclatureName").text();
                let count = parseFloat($(this).children('.ordersAddCount').find('.ordersCountInput').val());
                let price = +parseFloat($(this).children('.ordersAddCount').find('.ordersPriceInput').val()).toFixed(2);
                let cost = $(this).children('.ordersAddCount').find('.ordersCostInput').val();
                let total = +parseFloat(price * count).toFixed(2);
                addOrdersTableBody +=`<tr class="tableNomenclature">
                                        <th>`+id+` <input type="hidden" name="order_items[]" value="`+id+`"><input type="hidden" name="nomenclature_id[]" value="`+nomenclature_id+`"></th>
                                        <td class="name">`+name+`</td>
                                        <td class="count"><input type="number" name="count_[]" value="`+count+`" class="form-control countProduct"></td>
                                        <td class="price">`+price+` <input type="hidden" name="price[]" value="`+price+`"></td>
                                        <td class="cost">`+cost+` <input type="hidden" name="cost[]" value="`+cost+`"></td>
                                        <td class="total"><span>`+total+`</span><input type="hidden" name="total[]" value="`+total+`"></td>
                                        <td><button  type="button" class="btn rounded-pill btn-outline-danger deleteItems">Ջնջել</button></td>
                                     </tr>`;
            }else if($(this).find("input:checkbox").is(':checked') && $(this).find('.ordersCountInput').val() == ''){
                alert('Դուք չեք նշել ընտրված ապրանքի քանակը:')
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
                $(this).val(1)
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
            $(this).val(1)
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
        let confirmed =  confirm("Այս ապրանքը դուք ուզում եք ջնջե՞լ:");
        if (confirmed){
            $(this).closest('.tableNomenclature').remove();
            alert('Հաջողությամբ ջնջված է:');
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
            if ($(this).find("input:checkbox").is(':checked') && $(this).find('.ordersCountInput').val() != '') {
                let id = $(this).find("input:checkbox").attr('data-id');
                let nomenclature_id = $(this).find('.productIdInput').data('product');
                let name = $(this).children(".nomenclatureName").text();
                let count = parseFloat($(this).children('.ordersAddCount').find('.ordersCountInput').val());
                let price = +parseFloat($(this).children('.ordersAddCount').find('.ordersPriceInput').val()).toFixed(2);
                let cost = $(this).children('.ordersAddCount').find('.ordersCostInput').val();
                let total = +parseFloat(price * count).toFixed(2);
                addOrdersTableBody +=`<tr class="tableNomenclature">
                                        <td>`+nomenclature_id+` <input type="hidden" name="order_items[]" value="null">
                                            <input type="hidden" name="product_id[]" value="`+id+`">
                                            <input type="hidden" name="nom_id[]" value="`+nomenclature_id+`">
                                        </td>
                                        <td class="name">`+name+`</td>
                                        <td class="count"><input type="number" name="count_[]" value="`+count+`" class="form-control countProduct"></td>
                                        <td class="price">`+price+` <input type="hidden" name="price[]" value="`+price+`"></td>
                                        <td class="cost">`+cost+` <input type="hidden" name="cost[]" value="`+cost+`"></td>
                                        <td class="total"><span>`+total+`</span><input type="hidden" name="total[]" value="`+total+`"></td>
                                        <td><button  type="button" class="btn rounded-pill btn-outline-danger deleteItems">Ջնջել</button></td>
                                     </tr>`;
            }else if($(this).find("input:checkbox").is(':checked') && $(this).find('.ordersCountInput').val() == ''){
                alert('Դուք չեք նշել ընտրված ապրանքի քանակը:')
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
        if ($(this).val() === "" || $(this).val() < 1){
            alert('Տվյալ դաշտը չպետք է բացասական արժեք ունենա կամ դատարկ լինի։');
            $(this).val(1)
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
        if ($(this).val() === "" || $(this).val() < 1){
            alert('Տվյալ դաշտը չպետք է բացասական արժեք ունենա կամ դատարկ լինի։');
            $(this).val(1)
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
        let confirmed =  confirm("Այս ապրանքը դուք ուզում եք ջնջե՞լ:");
        if (confirmed){
            var this_ = $(this);
            var itemId = this_.closest('.tableNomenclature').find('.orderItemsId').val();
            var nomId = this_.closest('.tableNomenclature').find('.nomId').val();
            var totalPrice = $('.totalPrice').val() - (this_.closest('tr').find('.price').find('input').val() * this_.closest('tr').find('.countProductForUpdate').val());
            var totalCount = $('.totalCount').val() - (this_.closest('tr').find('.countProductForUpdate').val());
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
                    _csrf:csrfToken
                },
                success:function (data){
                    if (data === 'true'){
                        this_.closest('.tableNomenclature').remove();
                        alert('Հաջողությամբ ջնջված է:');
                        let totalSum = 0;
                        var countSum = 0;
                        $('.tableNomenclature').each(function () {
                            totalSum += +parseFloat($(this).find('.total').children('input').val()).toFixed(2);
                            countSum += +parseFloat($(this).find('.count').children('input').val()).toFixed(2);
                        });
                        $('#orders-total_price').attr('value', totalSum);
                        $('#orders-total_count').attr('value', countSum);
                    }else {
                        alert('Գոյություն չունի կամ հաջողությամբ չի կատարվել ջնջումը:');
                    }
                }
            })

        }

    })
    $('body').on('keyup','.ordersCountInput',function (){
        var this_ = $(this);
        var id = this_.closest('.addOrdersTableTr').find(".productIdInput").attr('data-product');
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
                        this_.val('')
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
        var id = this_.closest('.addOrdersTableTr').find(".productIdInput").attr('data-product');
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

    $('body').on('keyup', '.searchForOrder',function () {
        var nomenclature = $(this).val();
        let current_href = $('body').find('.active .by_ajax').data('href');
        getNom( current_href+'&nomenclature='+nomenclature);
    })

    $('body').on('click', '.by_ajax',function () {
        var href_ = $(this).attr('data-href');
        getNom(href_);
    })
    function getNom(href_) {
        $.ajax({
            url:href_,
            method:'get',
            datatype:'html',
            success:function (data) {
                $('#ajax_content').html(data);
            }
        })

    }







})
