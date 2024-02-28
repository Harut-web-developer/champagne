$(document).ready(function () {
    var id_count = {};
    var warehouse_id = $('#documents-warehouse_id').val();
    $('body').on('change','#documents-warehouse_id',function () {
        warehouse_id = $(this).val();
        let csrfToken = $('meta[name="csrf-token"]').attr("content");
        $.ajax({
            url:'/documents/change-storekeeper',
            method:'get',
            datatype:'html',
            data:{
                warehouse_id:warehouse_id,
                _csrf:csrfToken,
            },
            success:function (data) {
                if (data != 'false'){
                    $('body').find('.changeKeeper').html(data);
                }
            }
        })

    })
    var documents_type = $('#documents-document_type').val();
    $('body').on('change','#documents-document_type',function () {
        documents_type = $(this).val();
    })
    $('body').on('input', '.documentsCountInput', function () {
        count_id($(this));
    });
    $('body').on('input', '.countDocuments', function () {
        let id = $(this).closest('tr').find('.itemsId').val();
        let count = $(this).val();
        if (count) {
            id_count[String(id).trim()] = parseInt(count.trim());
        }else{
            delete id_count[String(id).trim()];
        }
        console.log(id_count)

    });
    function count_id(el) {
        let id = el.closest('tr').find('.nom_id').data('id');
        let count = el.val();
        if (count) {
            id_count[String(id).trim()] = parseInt(count.trim());
        }else{
            delete id_count[String(id).trim()];
        }
        // console.log(id_count)

    }

    function check_delete(el){
        let id = el.closest('tr').find(".itemsId").val();
        // console.log(id)
        $('.documentsTableTr').find('.nom_id[data-id="id"]').siblings('.documentsCount').find('.documentsCountInput').val('');
        delete id_count[String(id).trim()]
        delete trs[id.trim()];
        // console.log(trs)
    }

    var newTbody = $('<tbody></tbody>');
    var trs = {};
    $('body').on('click', '.createDocuments', function () {
        let docType = $('body').find('#documents-document_type').val();
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
                if (docType == 1){
                    trs[id.trim()] = `<tr class="tableDocuments oldTr">
                                     <td>
                                        <span>` + id + `</span>
                                        <input type="hidden" name="document_items[]" value="` + id + `">
                                        <input class="itemsId" type="hidden" name="items[]" value="` + id  + `">
                                     </td>
                                     <td class="name">` + name + `</td>
                                     <td class="count"><input type="number" name="count_[]" value="` + count + `" class="form-control countDocuments" step="1" min="1" ></td>
                                     <td class="price"><input type="text" name="price[]" value="` + price + `" class="form-control PriceDocuments"></td>
                                     <td class="pricewithaah">
                                        <span>`+priceWithaah+`</span>
                                        <input type="hidden" name="pricewithaah[]" value="` + priceWithaah + `" class="form-control PriceWithaah">
                                     </td>
                                     <td><button  type="button" class="btn rounded-pill btn-outline-danger deleteItems">Ջնջել</button></td>
                                  </tr>`.trim();
                }else {
                    trs[id.trim()] = `<tr class="tableDocuments oldTr">
                                     <td>
                                        <span>` + id + `</span>
                                        <input type="hidden" name="document_items[]" value="` + id + `">
                                        <input class="itemsId" type="hidden" name="items[]" value="` + id  + `">
                                     </td>
                                     <td class="name">` + name + `</td>
                                     <td class="count"><input type="number" readonly name="count_[]" value="` + count + `" class="form-control countDocuments" step="1" min="1" ></td>
                                     <td class="price"><input type="text" name="price[]" value="` + price + `" class="form-control PriceDocuments"></td>
                                     <td class="pricewithaah">
                                        <span>`+priceWithaah+`</span>
                                        <input type="hidden" name="pricewithaah[]" value="` + priceWithaah + `" class="form-control PriceWithaah">
                                     </td>
                                     <td><button  type="button" class="btn rounded-pill btn-outline-danger deleteItems">Ջնջել</button></td>
                                  </tr>`.trim();
                }

            }
        })
        for (let i in trs) {
            if(trs[i] != ''){
                newTbody.append(trs[i]);
            }
        }
        newTbody.append(documentsTableBody);
        $('.documentsAddingTable tbody').replaceWith(newTbody);
            $('body').find('.saveAll').attr('disabled',false);
        trCounter($('body').find('.documentsAddingTable'));
    })

    $('body').on('click', '.deleteItems', function () {
        let confirmed =  confirm("Այս ապրանքը դուք ուզում եք ջնջե՞լ:");
        if (confirmed){
            check_delete($(this));
            $(this).closest('.tableDocuments').remove();
        }
        alert('Հաջողությամբ ջնջված է:');
    })
    $('body').on('click', '.deleteItemsRefuse', function () {
        let confirmed =  confirm("Այս ապրանքը դուք ուզում եք ջնջե՞լ:");
        if (confirmed){
            $(this).closest('tr').remove();
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
        let docType = $('body').find('#documents-document_type').val();
        $('.documentsAddingTable tbody').html('')
        $('.documentsTableTr').each(function () {
            if ($(this).find(".documentsCountInput").val() != '') {
                let nom_id = $(this).find(".nom_id").attr('data-id');
                let name = $(this).children(".documentsName").text();
                let count = parseFloat($(this).children('.documentsCount').find('.documentsCountInput').val());
                let price = +parseFloat($(this).children('.documentsCount').find('.documentsPriceInput').val()).toFixed(2);
                let priceWithaah = price + (price * 20) / 100;
                priceWithaah = priceWithaah.toFixed(2)
                if (docType == 'Մուտք'){
                    trs[nom_id.trim()] = `<tr class="tableDocuments oldTr">
                                         <td>
                                            <span>`+ nom_id +`</span>
                                            <input type="hidden" name="document_items[]" value="null">
                                            <input class="itemsId" type="hidden" name="items[]" value="` + nom_id  + `">
                                         </td>
                                         <td class="name">` + name + `</td>
                                         <td class="count"><input type="number" name="count_[]" value="` + count + `" class="form-control countDocuments" step="1" min="1" ></td>
                                         <td class="price"><input type="text" name="price[]" value="` + price + `" class="form-control PriceDocuments"></td>
                                         <td class="pricewithaah">
                                            <span>`+priceWithaah+`</span>
                                            <input type="hidden" name="pricewithaah[]" value="` + priceWithaah + `" class="form-control PriceWithaah">
                                         </td>
                                         <td><button  type="button" class="btn rounded-pill btn-outline-danger deleteItems">Ջնջել</button></td>
                                      </tr>`.trim();
                }else {
                    trs[nom_id.trim()] = `<tr class="tableDocuments oldTr">
                                         <td>
                                            <span>`+ nom_id +`</span>
                                            <input type="hidden" name="document_items[]" value="null">
                                            <input class="itemsId" type="hidden" name="items[]" value="` + nom_id  + `">
                                         </td>
                                         <td class="name">` + name + `</td>
                                         <td class="count"><input type="number" readonly name="count_[]" value="` + count + `" class="form-control countDocuments" step="1" min="1" ></td>
                                         <td class="price"><input type="text" name="price[]" value="` + price + `" class="form-control PriceDocuments"></td>
                                         <td class="pricewithaah">
                                            <span>`+priceWithaah+`</span>
                                            <input type="hidden" name="pricewithaah[]" value="` + priceWithaah + `" class="form-control PriceWithaah">
                                         </td>
                                         <td><button  type="button" class="btn rounded-pill btn-outline-danger deleteItems">Ջնջել</button></td>
                                      </tr>`.trim();
                }

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
        let docType = $('body').find('#documents-document_type').val();
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
                if (docType == 'Մուտք') {
                    trs[nom_id.trim()] = `<tr class="tableDocuments oldTr">
                                         <td>
                                            <span>` + nom_id +`</span>
                                            <input type="hidden" name="document_items[]" value="null">
                                            <input class="itemsId" type="hidden" name="items[]" value="` + nom_id  + `">
                                         </td>
                                         <td class="name">` + name + `</td>
                                         <td class="count"><input type="number" name="count_[]" value="` + count + `" class="form-control countDocuments" step="1" min="1" ></td>
                                         <td class="price"><input type="text" name="price[]" value="` + price + `" class="form-control PriceDocuments"></td>
                                         <td class="pricewithaah">
                                            <span>`+priceWithaah+`</span>
                                            <input type="hidden" name="pricewithaah[]" value="` + priceWithaah + `" class="form-control PriceWithaah">
                                         </td>
                                         <td><button  type="button" class="btn rounded-pill btn-outline-danger deleteItems">Ջնջել</button></td>
                                      </tr>`.trim();
                }else {
                    trs[nom_id.trim()] = `<tr class="tableDocuments oldTr">
                                         <td>
                                            <span>` + nom_id +`</span>
                                            <input type="hidden" name="document_items[]" value="null">
                                            <input class="itemsId" type="hidden" name="items[]" value="` + nom_id  + `">
                                         </td>
                                         <td class="name">` + name + `</td>
                                         <td class="count"><input type="number" readonly name="count_[]" value="` + count + `" class="form-control countDocuments" step="1" min="1" ></td>
                                         <td class="price"><input type="text" name="price[]" value="` + price + `" class="form-control PriceDocuments"></td>
                                         <td class="pricewithaah">
                                            <span>`+priceWithaah+`</span>
                                            <input type="hidden" name="pricewithaah[]" value="` + priceWithaah + `" class="form-control PriceWithaah">
                                         </td>
                                         <td><button  type="button" class="btn rounded-pill btn-outline-danger deleteItems">Ջնջել</button></td>
                                      </tr>`.trim();
                }

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
        let docType = $('body').find('#documents-document_type').val();
        let csrfToken = $('meta[name="csrf-token"]').attr("content");
        if (confirmed){
            $.ajax({
                url: '/documents/delete-document-items',
                method: 'post',
                datatype: 'json',
                data: {
                    docItemsId: docItemsId,
                    docType:docType,
                    _csrf: csrfToken
                },
                success: function (data) {
                    if (data === 'true') {
                        this_.closest('.oldTr').remove();
                        alert('Հաջողությամբ ջնջված է:');
                    }else if (data === 'false'){
                        alert('Մեկ անուն ապրանքի դեպքում պետք է ջնջել ամբողջ փաստաթուղթը:');
                    }
                }
            })
        }
    })

    $('body').on('click', '.PriceDocuments', function () {
        let input = $(this).val();
        let cleanedValue = input.replace(/[^0-9.]/g, '');
        if (parseFloat(cleanedValue) < 1) {
            $(this).closest('tr').remove();
        } else if(parseFloat(cleanedValue) == 0){
            $(this).val(1)
            let num = parseFloat(1) + (parseFloat(1) * 20) / 100;
            $(this).closest('.oldTr').find('.pricewithaah').children('span').text(num.toFixed(2))
            $(this).closest('.oldTr').find('.pricewithaah').children('input').val(num.toFixed(2))
        } else {
            let dotCount = cleanedValue.split('.').length - 1;
            if (dotCount > 1) {
                cleanedValue = cleanedValue.slice(0, cleanedValue.lastIndexOf('.'));
            }
            $(this).val(cleanedValue);
            let num = parseFloat(cleanedValue) + (parseFloat(cleanedValue) * 20) / 100;
            $(this).closest('.oldTr').find('.pricewithaah').children('span').text(num.toFixed(2))
            $(this).closest('.oldTr').find('.pricewithaah').children('input').val(num.toFixed(2))
            if (isNaN($(this).closest('.oldTr').find('.pricewithaah').children('span').text())){
                $(this).attr('required', true);
                $(this).css('border-color', 'red');

            }else {
                $(this).attr('required', false);
                $(this).css('border-color', '#D9DEE3');
            }
        }
    })

    $('body').on('keyup', '.PriceDocuments', function () {
        let input = $(this).val();
        let cleanedValue = input.replace(/[^0-9.]/g, '');
        if (parseFloat(cleanedValue) < 0) {
            $(this).closest('tr').remove();
        } else if(parseFloat(cleanedValue) == 0){
            $(this).val(1)
            let num = parseFloat(1) + (parseFloat(1) * 20) / 100;
            $(this).closest('.oldTr').find('.pricewithaah').children('span').text(num.toFixed(2))
            $(this).closest('.oldTr').find('.pricewithaah').children('input').val(num.toFixed(2))
        } else {
            let dotCount = cleanedValue.split('.').length - 1;
            if (dotCount > 1) {
                cleanedValue = cleanedValue.slice(0, cleanedValue.lastIndexOf('.'));
            }
            $(this).val(cleanedValue);
            let num = parseFloat(cleanedValue) + (parseFloat(cleanedValue) * 20) / 100;
            $(this).closest('.oldTr').find('.pricewithaah').children('span').text(num.toFixed(2))
            $(this).closest('.oldTr').find('.pricewithaah').children('input').val(num.toFixed(2))
            if (isNaN($(this).closest('.oldTr').find('.pricewithaah').children('span').text())){
                $(this).attr('required', true);
                $(this).css('border-color', 'red');

            }else {
                $(this).attr('required', false);
                $(this).css('border-color', '#D9DEE3');
            }
        }

    })

    var arr_carent_page = [];
    $('body').on('keyup', '.searchForDocument', function () {
        let docType = $('body').find('#documents-document_type').val();
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
                if (docType == 1){
                    trs[id.trim()] = `<tr class="tableDocuments oldTr">
                     <td>
                        <span>` + id + `</span>
                        <input type="hidden" name="document_items[]" value="` + id + `">
                        <input class="itemsId" type="hidden" name="items[]" value="` + id  + `">
                        
                     </td>
                     <td class="name">` + name + `</td>
                     <td class="count"><input type="number" name="count_[]" value="` + count + `" class="form-control countDocuments" step="1" min="1" ></td>
                     <td class="price"><input type="text" name="price[]" value="` + price + `" class="form-control PriceDocuments"></td>
                     <td class="pricewithaah">
                        <span>`+priceWithaah+`</span>
                        <input type="hidden" name="pricewithaah[]" value="` + priceWithaah + `" class="form-control PriceWithaah">
                     </td>
                     <td><button  type="button" class="btn rounded-pill btn-outline-danger deleteItems">Ջնջել</button></td>
                  </tr>`.trim();
                }else {
                    trs[id.trim()] = `<tr class="tableDocuments oldTr">
                     <td>
                        <span>` + id + `</span>
                        <input type="hidden" name="document_items[]" value="` + id + `">
                        <input class="itemsId" type="hidden" name="items[]" value="` + id  + `">
                        
                     </td>
                     <td class="name">` + name + `</td>
                     <td class="count"><input type="number" readonly name="count_[]" value="` + count + `" class="form-control countDocuments" step="1" min="1" ></td>
                     <td class="price"><input type="text" name="price[]" value="` + price + `" class="form-control PriceDocuments"></td>
                     <td class="pricewithaah">
                        <span>`+priceWithaah+`</span>
                        <input type="hidden" name="pricewithaah[]" value="` + priceWithaah + `" class="form-control PriceWithaah">
                     </td>
                     <td><button  type="button" class="btn rounded-pill btn-outline-danger deleteItems">Ջնջել</button></td>
                  </tr>`.trim();
                }

            }
        })
        // console.log('searchForDocument')
    })

    var arr_carent_page_update = [];
    $('body').on('keyup', '.searchForDocumentUpdate', function () {
        let docType = $('body').find('#documents-document_type').val();
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
                if (docType == 'Մուտք') {
                    trs[nom_id.trim()] = `<tr class="tableDocuments oldTr">
                                         <td>
                                            <span>` + nom_id +`</span>
                                            <input type="hidden" name="document_items[]" value="null">
                                            <input class="itemsId" type="hidden" name="items[]" value="` + nom_id  + `">
                                         </td>
                                         <td class="name">` + name + `</td>
                                         <td class="count"><input type="number" name="count_[]" value="` + count + `" class="form-control countDocuments" step="1" min="1" ></td>
                                         <td class="price"><input type="text" name="price[]" value="` + price + `" class="form-control PriceDocuments"></td>
                                         <td class="pricewithaah">
                                             <span>`+priceWithaah+`</span>
                                             <input type="hidden" name="pricewithaah[]" value="` + priceWithaah + `" class="form-control PriceWithaah">
                                         </td>
                                         <td><button  type="button" class="btn rounded-pill btn-outline-danger deleteItems">Ջնջել</button></td>
                                      </tr>`.trim();
                }else {
                    trs[nom_id.trim()] = `<tr class="tableDocuments oldTr">
                                         <td>
                                            <span>` + nom_id +`</span>
                                            <input type="hidden" name="document_items[]" value="null">
                                            <input class="itemsId" type="hidden" name="items[]" value="` + nom_id  + `">
                                         </td>
                                         <td class="name">` + name + `</td>
                                         <td class="count"><input type="number" readonly name="count_[]" value="` + count + `" class="form-control countDocuments" step="1" min="1" ></td>
                                         <td class="price"><input type="text" name="price[]" value="` + price + `" class="form-control PriceDocuments"></td>
                                         <td class="pricewithaah">
                                             <span>`+priceWithaah+`</span>
                                             <input type="hidden" name="pricewithaah[]" value="` + priceWithaah + `" class="form-control PriceWithaah">
                                         </td>
                                         <td><button  type="button" class="btn rounded-pill btn-outline-danger deleteItems">Ջնջել</button></td>
                                      </tr>`.trim();
                }

            }
        })
        // console.log('searchForDocumentUpdate')
    })

    $('body').on('click', '.by_ajax', function () {
        let docType = $('body').find('#documents-document_type').val();
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
                if (docType == 1){
                    trs[id.trim()] = `<tr class="tableDocuments oldTr">
                     <td>
                        <span>` + id + `</span>
                        <input type="hidden" name="document_items[]" value="` + id + `">
                        <input class="itemsId" type="hidden" name="items[]" value="` + id  + `">
                     </td>
                     <td class="name">` + name + `</td>
                     <td class="count"><input type="number" name="count_[]" value="` + count + `" class="form-control countDocuments" step="1" min="1" ></td>
                     <td class="price"><input type="text" name="price[]" value="` + price + `" class="form-control PriceDocuments"></td>
                     <td class="pricewithaah">
                         <span>`+priceWithaah+`</span>
                         <input type="hidden" name="pricewithaah[]" value="` + priceWithaah + `" class="form-control PriceWithaah">
                     </td>
                     <td><button  type="button" class="btn rounded-pill btn-outline-danger deleteItems">Ջնջել</button></td>
                  </tr>`.trim();
                }else {
                    trs[id.trim()] = `<tr class="tableDocuments oldTr">
                     <td>
                        <span>` + id + `</span>
                        <input type="hidden" name="document_items[]" value="` + id + `">
                        <input class="itemsId" type="hidden" name="items[]" value="` + id  + `">
                     </td>
                     <td class="name">` + name + `</td>
                     <td class="count"><input type="number" readonly name="count_[]" value="` + count + `" class="form-control countDocuments" step="1" min="1" ></td>
                     <td class="price"><input type="text" name="price[]" value="` + price + `" class="form-control PriceDocuments"></td>
                     <td class="pricewithaah">
                         <span>`+priceWithaah+`</span>
                         <input type="hidden" name="pricewithaah[]" value="` + priceWithaah + `" class="form-control PriceWithaah">
                     </td>
                     <td><button  type="button" class="btn rounded-pill btn-outline-danger deleteItems">Ջնջել</button></td>
                  </tr>`.trim();
                }

            }
        })
    })
    function getNomDocument(href_) {
        let url_id = window.location.href;
        let url = new URL(url_id);
        let urlId = url.searchParams.get("id");
        $.ajax({
            url:href_+'&warehouse_id='+warehouse_id+'&documents_type='+documents_type,
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
        $('body').find('.forDocTypeTen').addClass('d-none');
        if ($(this).val() == 3){
            $('body').find('.toWarehouse').addClass('activeForInput');
            $('body').find('.docType').removeClass('activeForInputDocument');
            $('body').find('.deliveredOrders').removeClass('activeForInputDocument');
            $("#documents-to_warehouse").attr('required',true);
            $('body').find('.documentsAddingTable tbody').html('');
            $('body').find('.saveAll').attr('disabled',true);
            $('body').find('.addDocuments').attr('disabled',true);

        }else if($(this).val() == 10){
            $('body').find('.documentsAddingTable tbody').html('');
            $('body').find('.forDocTypeTen').removeClass('d-none');
            $('body').find('.docType').addClass('activeForInputDocument');
            $('body').find('.toWarehouse').removeClass('activeForInput');
            $("#documents-to_warehouse").removeAttr('required');
            // $('body').find('.addDocuments').attr('disabled',true);
        }
        else {
            $('body').find('.addDocuments').attr('disabled',true);
            $('body').find('.toWarehouse').removeClass('activeForInput');
            $('body').find('.docType').removeClass('activeForInputDocument');
            $('body').find('.deliveredOrders').removeClass('activeForInputDocument');
            $("#documents-to_warehouse").removeAttr('required');
            $('body').find('.documentsAddingTable tbody').html('')
            $('body').find('.saveAll').attr('disabled',true);
        }
    })
    $('body').on('change', '#singleClients', function () {
        let clientId = $(this).val();
        let csrfToken = $('meta[name="csrf-token"]').attr("content");
        $('body').find('.documentsAddingTable tbody').html('');
        $('body').find('.saveAll').attr('disabled',true);

        $.ajax({
            url:'/documents/change-orders',
            method:'get',
            datatype:'html',
            data:{
              clientId:clientId,
              _csrf:csrfToken
            },
            success:function (data) {
                $('body').find('.deliveredOrders').html(data);
                $('body').find('.deliveredOrders').addClass('activeForInputDocument');
            }
        })
    })
    $('body').on('change', '#deliveredorders', function () {
        let ordersId = $(this).val();
        let csrfToken = $('meta[name="csrf-token"]').attr("content");
        $.ajax({
            url:'/documents/delivered-orders',
            method:'get',
            datatype:'json',
            data:{
                ordersId:ordersId,
                _csrf:csrfToken
            },
            success:function (data) {
                let param = JSON.parse(data);
                $('body').find('.documentsAddingTable tbody').html('')
                // console.log(param)
                let td_string = '';
                for (let m = 0; m < param.length; m++){
                    if (param[m].AAH == 1){
                        td_string += `<tr>
                                    <td>
                                        <span>` + (m + 1) + `</span>
                                        <input type="hidden" name="document_items[]" value="` + param[m].nom_id_for_name + `">
                                        <input class="itemsId" type="hidden" name="items[]" value="` + param[m].id + `">
                                    </td>
                                    <td class="name">`+ param[m].name +`</td>
                                    <td class="count"><input type="number" name="count_[]" value="` + param[m].count_by + `" class="form-control refuseCountDocuments" step="1" min="1" max="` + param[m].count_by + `"></td>
                                    <td class="raw"><input type="number" name="raw[]" value="" class="form-control rawInput" step="1" min="0" max="` + param[m].count_by + `"></td>
                                    <td class="price"><input type="text" name="price[]" value="` + ((param[m].price * 5)/6).toFixed(2) + `" class="form-control refusePriceDocuments"></td>
                                    <td class="pricewithaah">
                                        <span>` + param[m].price.toFixed(2) + `</span>
                                        <input type="hidden" name="pricewithaah[]" value="` + param[m].price.toFixed(2) + `" class="form-control PriceWithaah">
                                    </td>
                                    <td><button  type="button" class="btn rounded-pill btn-outline-danger deleteItemsRefuse">Ջնջել</button></td>
                                </tr>`;
                    }else {
                        let num = param[m].price + (param[m].price * 20)/100;
                        td_string += `<tr>
                                    <td>
                                        <span>` + (m + 1) + `</span>
                                        <input type="hidden" name="document_items[]" value="` + param[m].nom_id_for_name + `">
                                        <input class="itemsId" type="hidden" name="items[]" value="` + param[m].id + `">
                                    </td>
                                    <td class="name">`+ param[m].name +`</td>
                                    <td class="count"><input type="number" name="count_[]" value="` + param[m].count_by + `" class="form-control refuseCountDocuments" step="1" min="1" max="` + param[m].count_by + `"></td>
                                    <td class="raw"><input type="number" name="raw[]" value="" class="form-control rawInput" step="1" min="0" max="` + param[m].count_by + `"></td>
                                    <td class="price"><input type="text" name="price[]" value="` + param[m].price.toFixed(2) + `" class="form-control refusePriceDocuments"></td>
                                    <td class="pricewithaah">
                                        <span>` + num.toFixed(2) + `</span>
                                        <input type="hidden" name="pricewithaah[]" value="` + num.toFixed(2) + `" class="form-control PriceWithaah">
                                    </td>
                                    <td><button  type="button" class="btn rounded-pill btn-outline-danger deleteItemsRefuse">Ջնջել</button></td>
                                </tr>`;
                    }
                }
                $('body').find('.documentsAddingTable tbody').append(td_string)
                $('body').find('.saveAll').attr('disabled',false);
            }
        })
    })

    $('body').on('keyup','.refusePriceDocuments', function () {
        let inputValue = $(this).val();
        let sanitizedValue = inputValue.replace(/[^0-9.]/g, '');
        let parts = sanitizedValue.split('.');
        if (parts.length > 1) {
            parts[1] = parts[1].replace(/\./g, '');
            sanitizedValue = parts[0] + '.' + parts[1];
        }
        // $(this).val(sanitizedValue);
        if (sanitizedValue < 1 || $(this).val() == '') {
            $(this).attr('required', true);
        }else {
            let num = parseFloat(sanitizedValue) + (parseFloat(sanitizedValue) * 20) / 100;
            $(this).closest('tr').find('.pricewithaah').children('span').text(num.toFixed(2))
            $(this).closest('tr').find('.pricewithaah').children('input').val(num.toFixed(2))
        }
    })

    $('body').on('keyup','.refuseCountDocuments',function (){
        $(this).val(function(index, value) {
            return value.replace(/-/g, '');
        });
        let inputValue = parseInt($(this).val());
        let maxValue = parseInt($(this).attr('max'));
        if (inputValue < 1 || inputValue === "") {
            $(this).val('');
            $(this).attr('required',true);
        }else if (inputValue > maxValue){
            $(this).val('');
            $(this).attr('required',true);
        }else {
            $(this).closest('tr').find('.rawInput').attr('max',$(this).val());
        }
    })
    $('body').on('keyup','.rawInput',function (){
        $(this).val(function(index, value) {
            return value.replace(/-/g, '');
        });
        let inputValue = parseInt($(this).val());
        let maxValue = parseInt($(this).attr('max'));
        if (inputValue < 0 || inputValue === "") {
            $(this).val('');
            $(this).attr('required',true);
        }else if (inputValue > maxValue){
            $(this).val('');
            $(this).attr('required',true);
        }
    })

    $('body').on('click','.documentsCountInput',function (){
        if ($(this).val() < 1) {
            $(this).val('');
        }else{
            let itemId = $(this).closest('.documentsTableTr').find('.nom_id').data('id');
            getCount($(this),itemId);
        }
    })
    $('body').on('keyup','.documentsCountInput',function (){
        if ($(this).val() < 1) {
            $(this).val('');
        }else{
            let itemId = $(this).closest('.documentsTableTr').find('.nom_id').data('id');
            getCount($(this),itemId);
        }

    })
    function getCount(element,item){
        let document_type = $('body').find('#documents-document_type').val();
        let this_ = element;
        let itemId = item;
        let countProduct = element.val();
            if (document_type == 3 || document_type == 'Տեղափոխություն' || document_type == 2 || document_type == 'Ելք' || document_type == 4 || document_type == 'Խոտան'){
                let fromWarehouseId = $('body').find('#documents-warehouse_id').val();
                let csrfToken = $('meta[name="csrf-token"]').attr("content");
                $.ajax({
                    url:'/products/get-products',
                    method:'post',
                    datatype:'json',
                    data:{
                        itemId:itemId,
                        warehouse_id:fromWarehouseId,
                        count:countProduct,
                        _csrf:csrfToken,
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
            }

    }
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
    $('body').find('.card thead th').each(function () {
        if ($(this).has('a')){
            $(this).html( $(this).find('a').html())
        }
    })

    $('body').on('click', '.addDocuments_get_type_val', function (e) {
        var csrfToken = $('meta[name="csrf-token"]').attr("content");
        $.ajax({
            url:'/documents/get-nomiclature',
            method:'post',
            datatype:'html',
            data:{
                warehouse_id:warehouse_id,
                documents_type:documents_type,
                id_count:id_count,
                csrfToken:csrfToken,
            },
            success:function(data){
                $('#ajax_content').html(data);
            }
        })
    })

    $('body').on('click', '.addDocuments_get_type_val_update', function (e) {
        var csrfToken = $('meta[name="csrf-token"]').attr("content");
        let url_id = window.location.href;
        let url = new URL(url_id);
        let urlId = url.searchParams.get("id");
        $.ajax({
            url:'/documents/get-nomiclature-update',
            method:'post',
            datatype:'html',
            data:{
                warehouse_id:warehouse_id,
                documents_type:documents_type,
                id_count:id_count,
                urlId: urlId,
                csrfToken:csrfToken,
            },
            success:function(data){
                $('#ajax_content').html(data);
            }
        })
    })

    $('body').on('change','#documents-warehouse_id, #documents-document_type, #documents-date',function(){
        if($('#documents-warehouse_id').val() != '' && $('#documents-document_type').val() != 10 && $('#documents-date').val() != '' ){
            $('body').find('.addDocuments').attr('disabled',false);
        }
        else if($('#documents-document_type').val() == 10){
            $('body').find('.addDocuments').attr('disabled',true);
            // $('body').find('.saveAll').attr('disabled',true);
        }
    })

    $(window).on('load', function () {

        if($('body').find('.documentsAddingTable body').length == 0){
            $('body').find('.saveAll').attr('disabled',true);
        }
    })

})