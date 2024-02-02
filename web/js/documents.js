$(document).ready(function () {
    var id_count = {};
    $('body').on('input', '.documentsCountInput', function () {
        count_id($(this));
    });
    function count_id(el) {
        let id = el.closest('tr').find('.nom_id').data('id');
        let count = el.val();
        if (count) {
            id_count[String(id).trim()] = parseInt(count.trim());
        }else{
            delete id_count[String(id).trim()];
        }
    }

    function check_delete(){
        var arj = $('.deleteItems').closest('tr').find(".itemsId").val();
        $('.documentsTableTr').find('.nom_id[data-id="arj"]').siblings('.documentsCount').find('.documentsCountInput').val('');
    }

    var newTbody = $('<tbody></tbody>');
    var trs = {};
    $('body').on('click', '.createDocuments', function () {
        var documentsTableBody = '';
        $('.documentsAddingTable tbody').html('')
        $('.documentsTableTr').each(function () {
            if ($(this).find(".documentsCountInput").val() != '') {
                let id = $(this).find(".nom_id").attr('data-id');
                let name = $(this).children(".documentsName").text();
                let count = parseFloat($(this).children('.documentsCount').find('.documentsCountInput').val());
                let price = +parseFloat($(this).children('.documentsCount').find('.documentsPriceInput').val()).toFixed(2);
                let priceWithaah = price + (price * 20) / 100;
                priceWithaah = priceWithaah.toFixed(2)
                trs[id.trim()] = `<tr class="tableDocuments oldTr">
                                     <td>
                                        <span>` + id + `</span>
                                        <input type="hidden" name="document_items[]" value="` + id + `">
                                     </td>
                                     <td class="name">` + name + `</td>
                                     <td class="count"><input type="number" name="count_[]" value="` + count + `" class="form-control countDocuments"></td>
                                     <td class="price"><input type="text" name="price[]" value="` + price + `" class="form-control PriceDocuments"></td>
                                     <td class="pricewithaah">
                                        <span>`+priceWithaah+`</span>
                                        <input type="hidden" name="pricewithaah[]" value="` + priceWithaah + `" class="form-control PriceWithaah">
                                     </td>
                                     <td><button  type="button" class="btn rounded-pill btn-outline-danger deleteItems">Ջնջել</button></td>
                                  </tr>`.trim();
            }
        })
        for (let i in trs) {
            if(trs[i] != ''){
                newTbody.append(trs[i]);
            }
        }
        newTbody.append(documentsTableBody);
        $('.documentsAddingTable tbody').replaceWith(newTbody);
        trCounter($('body').find('.documentsAddingTable'));
    })

    $('body').on('click', '.deleteItems', function () {
        let confirmed =  confirm("Այս ապրանքը դուք ուզում եք ջնջե՞լ:");
        if (confirmed){
            $(this).closest('.tableDocuments').remove();
        }
        alert('Հաջողությամբ ջնջված է:');

    })

    var old_table = $('.table.documentsAddingTable').find('.old_tbody').html();
    var old_attrs = {};
    $('body').on('input', '.documentsAddingTable td input', function () {
        // let el = $(this);
        let id = $(this).closest('tr').find('.itemsId').val(); // iitem_id
        let count = $(this).closest('tr').find('.countDocuments').val(); // iitem_id
        let price = $(this).closest('tr').find('.PriceDocuments').val(); // iitem_id
        old_attrs[id]= { count:count , price:price};
    })

    $('body').on('click', '.updateDocuments', function () {
        var documentsTableBody = '';
        $('.documentsAddingTable tbody').html('')
        $('.documentsTableTr').each(function () {
            if ($(this).find(".documentsCountInput").val() != '') {
                let nom_id = $(this).find(".nom_id").attr('data-id');
                let name = $(this).children(".documentsName").text();
                let count = parseFloat($(this).children('.documentsCount').find('.documentsCountInput').val());
                let price = +parseFloat($(this).children('.documentsCount').find('.documentsPriceInput').val()).toFixed(2);
                let priceWithaah = price + (price * 20) / 100;
                priceWithaah = priceWithaah.toFixed(2)
                trs[nom_id.trim()] = `<tr class="tableDocuments oldTr">
                                         <td>
                                            <span>`+ nom_id +`</span>
                                            <input type="hidden" name="document_items[]" value="null">
                                            <input class="itemsId" type="hidden" name="items[]" value="` + nom_id  + `">
                                         </td>
                                         <td class="name">` + name + `</td>
                                         <td class="count"><input type="number" name="count_[]" value="` + count + `" class="form-control countDocuments"></td>
                                         <td class="price"><input type="text" name="price[]" value="` + price + `" class="form-control PriceDocuments"></td>
                                         <td class="pricewithaah">
                                            <span>`+priceWithaah+`</span>
                                            <input type="hidden" name="pricewithaah[]" value="` + priceWithaah + `" class="form-control PriceWithaah">
                                         </td>
                                         <td><button  type="button" class="btn rounded-pill btn-outline-danger deleteItems">Ջնջել</button></td>
                                      </tr>`.trim();
            }
        })
        newTbody.append(old_table);
        for (let i in trs) {
            if(trs[i] != ''){
                newTbody.append(trs[i]);
            }
        }
        newTbody.append(documentsTableBody);
        $('.documentsAddingTable tbody').replaceWith(newTbody);
        giveOldValues();
        trCounter($('body').find('.documentsAddingTable'));

    })
    function giveOldValues() {
        for (let argumentsKey in old_attrs) {
           let tr = $('body').find('#tr_'+argumentsKey);
           tr.find('.count').find('.countDocuments').val(old_attrs[argumentsKey].count);
           tr.find('.price').find('.PriceDocuments').val(old_attrs[argumentsKey].price);
        }
    }


    $('body').on('click', '.by_ajax_update', function () {
        var href_ = $(this).attr('data-href');
        getNomDocument(href_);
        $('.documentsTableTr').each(function () {
            if ($(this).find(".documentsCountInput").val() != '') {
                let nom_id = $(this).find(".nom_id").attr('data-id');
                let name = $(this).children(".documentsName").text();
                let count = parseFloat($(this).children('.documentsCount').find('.documentsCountInput').val());
                let price = +parseFloat($(this).children('.documentsCount').find('.documentsPriceInput').val()).toFixed(2);
                let priceWithaah = price + (price * 20) / 100;
                priceWithaah = priceWithaah.toFixed(2)
                trs[nom_id.trim()] = `<tr class="tableDocuments oldTr">
                                         <td>
                                            <span>` + nom_id +`</span>
                                            <input type="hidden" name="document_items[]" value="null">
                                            <input class="itemsId" type="hidden" name="items[]" value="` + nom_id  + `">
                                         </td>
                                         <td class="name">` + name + `</td>
                                         <td class="count"><input type="number" name="count_[]" value="` + count + `" class="form-control countDocuments"></td>
                                         <td class="price"><input type="text" name="price[]" value="` + price + `" class="form-control PriceDocuments"></td>
                                         <td class="pricewithaah">
                                            <span>`+priceWithaah+`</span>
                                            <input type="hidden" name="pricewithaah[]" value="` + priceWithaah + `" class="form-control PriceWithaah">
                                         </td>
                                         <td><button  type="button" class="btn rounded-pill btn-outline-danger deleteItems">Ջնջել</button></td>
                                      </tr>`.trim();
            }
        })
        // console.log('by_ajax_update')

    })

    $('body').on('change','#documents-warehouse_id, #documents-to_warehouse',function(){
        if('documents-warehouse_id' === $(this).attr('id')){
            let first = $('body').find('#documents-warehouse_id').val();
            $('body').find('#documents-to_warehouse option').removeAttr('disabled');
            $('body').find('#documents-to_warehouse').find('option[value="'+first+'"]').attr('disabled',true);
        }
        else if('documents-to_warehouse' === $(this).attr('id')){
            let first = $('body').find('#documents-to_warehouse').val();
            $('body').find('#documents-warehouse_id option').removeAttr('disabled');
            $('body').find('#documents-warehouse_id').find('option[value="'+first+'"]').attr('disabled',true);
        }
    })
    $('body').on('click', '.deleteDocumentItems', function () {
        let confirmed =  confirm("Այս ապրանքը դուք ուզում եք ջնջե՞լ:");
        var this_ = $(this);
        let docItemsId = this_.closest('.oldTr').find('.docItemsId').val()
        let nomId = this_.closest('.oldTr').find('.itemsId').val()
        let csrfToken = $('meta[name="csrf-token"]').attr("content");
        let url_id = window.location.href;
        let url = new URL(url_id);
        let urlId = url.searchParams.get("id");
        if (confirmed){
            $.ajax({
                url: '/documents/delete-document-items',
                method: 'post',
                datatype: 'json',
                data: {
                    docItemsId: docItemsId,
                    nomId:nomId,
                    urlId: urlId,
                    _csrf: csrfToken
                },
                success: function (data) {
                    if (data === 'true') {
                        this_.closest('.oldTr').remove();
                    }
                }
            })
        }
        alert('Հաջողությամբ ջնջված է:');
    })

    $('body').on('click', '.PriceDocuments', function () {
        let cleanedValue = $(this).val().replace(/[^0-9.]/g, '');
        if (cleanedValue === '' || parseFloat(cleanedValue) < 1) {
            cleanedValue = '1';
            $(this).val(cleanedValue);
        }
        let num = parseFloat(cleanedValue) + (parseFloat(cleanedValue) * 20) / 100;
        $(this).closest('.oldTr').find('.pricewithaah').children('span').text(num.toFixed(2))
        $(this).closest('.oldTr').find('.pricewithaah').children('input').val(num.toFixed(2))
    })

    $('body').on('keyup', '.PriceDocuments', function () {
        let cleanedValue = $(this).val().replace(/[^0-9.]/g, '');
        if (cleanedValue === '' || parseFloat(cleanedValue) < 1) {
            cleanedValue = '1';
            $(this).val(cleanedValue);
        }
        let num = parseFloat(cleanedValue) + (parseFloat(cleanedValue) * 20) / 100;
        $(this).closest('.oldTr').find('.pricewithaah').children('span').text(num.toFixed(2))
        $(this).closest('.oldTr').find('.pricewithaah').children('input').val(num.toFixed(2))
    })

    var arr_carent_page = [];
    $('body').on('keyup', '.searchForDocument', function () {
        var nomenclature = $(this).val();
        let current_href = $('body').find('.active .by_ajax').data('href');
        arr_carent_page.push(current_href);
        let lastValidValue;
        for (let i = arr_carent_page.length - 1; i >= 0; i--) {
            const currentValue = arr_carent_page[i];
            if (currentValue !== undefined) {
                current_href = currentValue;
                break;
            }
        }
        getNomDocument(current_href + '&nomenclature=' + nomenclature);
        var documentsTableBody = '';
        $('.documentsTableTr').each(function () {
            if ($(this).find(".documentsCountInput").val() != '') {
                let id = $(this).find(".nom_id").attr('data-id');
                let name = $(this).children(".documentsName").text();
                let count = parseFloat($(this).children('.documentsCount').find('.documentsCountInput').val());
                let price = +parseFloat($(this).children('.documentsCount').find('.documentsPriceInput').val()).toFixed(2);
                let priceWithaah = price + (price * 20) / 100;
                priceWithaah = priceWithaah.toFixed(2)
                trs[id.trim()] = `<tr class="tableDocuments oldTr">
                     <td>
                        <span>` + id + `</span>
                        <input type="hidden" name="document_items[]" value="` + id + `">
                     </td>
                     <td class="name">` + name + `</td>
                     <td class="count"><input type="number" name="count_[]" value="` + count + `" class="form-control countDocuments"></td>
                     <td class="price"><input type="text" name="price[]" value="` + price + `" class="form-control PriceDocuments"></td>
                     <td class="pricewithaah">
                        <span>`+priceWithaah+`</span>
                        <input type="hidden" name="pricewithaah[]" value="` + priceWithaah + `" class="form-control PriceWithaah">
                     </td>
                     <td><button  type="button" class="btn rounded-pill btn-outline-danger deleteItems">Ջնջել</button></td>
                  </tr>`.trim();
            }
        })
        // console.log('searchForDocument')
    })

    var arr_carent_page_update = [];
    $('body').on('keyup', '.searchForDocumentUpdate', function () {
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
        getNomDocument(current_href + '&nomenclature=' + nomenclature);
        var documentsTableBody = '';
        $('.documentsTableTr').each(function () {
            if ($(this).find(".documentsCountInput").val() != '') {
                let nom_id = $(this).find(".nom_id").attr('data-id');
                let name = $(this).children(".documentsName").text();
                let count = parseFloat($(this).children('.documentsCount').find('.documentsCountInput').val());
                let price = +parseFloat($(this).children('.documentsCount').find('.documentsPriceInput').val()).toFixed(2);
                let priceWithaah = price + (price * 20) / 100;
                priceWithaah = priceWithaah.toFixed(2)
                trs[nom_id.trim()] = `<tr class="tableDocuments oldTr">
                                         <td>
                                            <span>` + nom_id +`</span>
                                            <input type="hidden" name="document_items[]" value="null">
                                            <input class="itemsId" type="hidden" name="items[]" value="` + nom_id  + `">
                                         </td>
                                         <td class="name">` + name + `</td>
                                         <td class="count"><input type="number" name="count_[]" value="` + count + `" class="form-control countDocuments"></td>
                                         <td class="price"><input type="text" name="price[]" value="` + price + `" class="form-control PriceDocuments"></td>
                                         <td class="pricewithaah">
                                             <span>`+priceWithaah+`</span>
                                             <input type="hidden" name="pricewithaah[]" value="` + priceWithaah + `" class="form-control PriceWithaah">
                                         </td>
                                         <td><button  type="button" class="btn rounded-pill btn-outline-danger deleteItems">Ջնջել</button></td>
                                      </tr>`.trim();
            }
        })
        // console.log('searchForDocumentUpdate')
    })

    $('body').on('click', '.by_ajax', function () {
        var href_ = $(this).attr('data-href');
        getNomDocument(href_);
        $('.documentsTableTr').each(function () {
            if ($(this).find(".documentsCountInput").val() != '') {
                let id = $(this).find(".nom_id").attr('data-id');
                let name = $(this).children(".documentsName").text();
                let count = parseFloat($(this).children('.documentsCount').find('.documentsCountInput').val());
                let price = +parseFloat($(this).children('.documentsCount').find('.documentsPriceInput').val()).toFixed(2);
                let priceWithaah = price + (price * 20) / 100;
                priceWithaah = priceWithaah.toFixed(2)

                trs[id.trim()] = `<tr class="tableDocuments oldTr">
                     <td>
                        <span>` + id + `</span>
                        <input type="hidden" name="document_items[]" value="` + id + `">
                     </td>
                     <td class="name">` + name + `</td>
                     <td class="count"><input type="number" name="count_[]" value="` + count + `" class="form-control countDocuments"></td>
                     <td class="price"><input type="text" name="price[]" value="` + price + `" class="form-control PriceDocuments"></td>
                     <td class="pricewithaah">
                         <span>`+priceWithaah+`</span>
                         <input type="hidden" name="pricewithaah[]" value="` + priceWithaah + `" class="form-control PriceWithaah">
                     </td>
                     <td><button  type="button" class="btn rounded-pill btn-outline-danger deleteItems">Ջնջել</button></td>
                  </tr>`.trim();
            }
        })
    })
    function getNomDocument(href_) {
        let url_id = window.location.href;
        let url = new URL(url_id);
        let urlId = url.searchParams.get("id");
        $.ajax({
            url: href_,
            method: 'post',
            datatype: 'html',
            data:{
                id_count:id_count,
                urlId: urlId,
            },
            success: function (data) {
                $('#ajax_content').html(data);
            }
        })
    }

    $('body').on('change','#documents-rate_id',function () {
        let id = $(this).val();
        let csrfToken = $('meta[name="csrf-token"]').attr("content");
        $.ajax({
            url: '/documents/change-rates',
            method: 'post',
            datatype: 'json',
            data: {
                id: id,
                _csrf: csrfToken
            },
            success: function (data) {
                let param = JSON.parse(data)
                if (param == 'others') {
                    $('body').find('#documents-rate_value').attr('readonly', false);
                    $('body').find('#documents-rate_value').val('');
                }else if(param == 'amd'){
                    $('body').find('#documents-rate_value').attr('readonly', true);
                    $('body').find('#documents-rate_value').val(1);
                }
            }
        })
    })
    $('body').on('change','#documents-document_type', function () {
        if ($(this).val() == 3){
            $('body').find('.toWarehouse').addClass('activeForInput');
            $("#documents-to_warehouse").attr('required',true);
        }else {
            $('body').find('.toWarehouse').removeClass('activeForInput');
            $("#documents-to_warehouse").removeAttr('required');
        }
    })

    function trCounter(table){
        let i = 0;
        table.find('tbody').find('tr').each(function () {
            $(this).find('td:first').find('span').text(++i);
        })
    }
    let currentUrl = window.location.href;
    let hasUpdate = currentUrl.includes('update');
    if (hasUpdate){
        if ($('body').find('#documents-document_type').val() == 'Տեղափոխություն'){
            $('body').find('.toWarehouse').addClass('activeForInput');
        }else {
            $('body').find('.toWarehouse').removeClass('activeForInput');
        }
    }
})