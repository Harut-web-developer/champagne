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
                trs[id.trim()] = `<tr class="tableDocuments oldTr">
                                     <td>` + id + `<input type="hidden" name="document_items[]" value="` + id + `"></td>
                                     <td class="name">` + name + `</td>
                                     <td class="count"><input type="number" name="count_[]" value="` + count + `" class="form-control countDocuments"></td>
                                     <td class="price"><input type="text" name="price[]" value="` + price + `" class="form-control PriceDocuments"></td>
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
        console.log('1')
    })

    $('body').on('click', '.deleteItems', function () {
        $(this).closest('.tableDocuments').remove();
        console.log('2')
    })

    var old_table = $('.table.documentsAddingTable').find('.old_tbody').html();
    var old_attrs = {};
    $('body').on('input', '.documentsAddingTable td input', function () {
        // let el = $(this);
        let id = $(this).closest('tr').find('.itemsId').val(); // iitem_id
        let count = $(this).closest('tr').find('.countDocuments').val(); // iitem_id
        let price = $(this).closest('tr').find('.PriceDocuments').val(); // iitem_id
        old_attrs[id]= { count:count , price:price};
        console.log('3')
    })

    $('body').on('click', '.updateDocuments', function () {
        var documentsTableBody = '';
        documentsCountInputReadOnly();
        $('.documentsAddingTable tbody').html('')
        $('.documentsTableTr').each(function () {
            if ($(this).find(".documentsCountInput").val() != '') {
                let nom_id = $(this).find(".nom_id").attr('data-id');
                let name = $(this).children(".documentsName").text();
                let count = parseFloat($(this).children('.documentsCount').find('.documentsCountInput').val());
                let price = +parseFloat($(this).children('.documentsCount').find('.documentsPriceInput').val()).toFixed(2);
                trs[nom_id.trim()] = `<tr class="tableDocuments oldTr">
                                         <td> `+ nom_id +`
                                          <input type="hidden" name="document_items[]" value="new_`+ nom_id +`">
                                          <input class="itemsId" type="hidden" name="items[]" value="` + nom_id  + `">
                                         </td>
                                         <td class="name">` + name + `</td>
                                         <td class="count"><input type="number" name="count_[]" value="` + count + `" class="form-control countDocuments"></td>
                                         <td class="price"><input type="text" name="price[]" value="` + price + `" class="form-control PriceDocuments"></td>
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
        console.log('4')
    })
    function giveOldValues() {
        for (let argumentsKey in old_attrs) {
           let tr = $('body').find('#tr_'+argumentsKey);
           tr.find('.count').find('.countDocuments').val(old_attrs[argumentsKey].count);
           tr.find('.price').find('.PriceDocuments').val(old_attrs[argumentsKey].price);
            console.log('5')

        }
    }
    // function documentsCountInputReadOnly(){
    //     if ($(this).find(".documentsCountInput").val() != ''){
    //         $('.documentsCountInput').prop('readonly', true);
    //     }
    //     console.log('6')
    // }

    $('body').on('click', '.by_ajax_update', function () {
        var href_ = $(this).attr('data-href');
        getNomDocument(href_);
        // documentsCountInputReadOnly();
        $('.documentsTableTr').each(function () {
            if ($(this).find(".documentsCountInput").val() != '') {
                let nom_id = $(this).find(".nom_id").attr('data-id');
                let name = $(this).children(".documentsName").text();
                let count = parseFloat($(this).children('.documentsCount').find('.documentsCountInput').val());
                let price = +parseFloat($(this).children('.documentsCount').find('.documentsPriceInput').val()).toFixed(2);
                trs[nom_id.trim()] = `<tr class="tableDocuments oldTr">
                                         <td> ` + nom_id +`
                                          <input type="hidden" name="document_items[]" value="new_`+ nom_id +`">
                                          <input class="itemsId" type="hidden" name="items[]" value="` + nom_id  + `">
                                         </td>
                                         <td class="name">` + name + `</td>
                                         <td class="count"><input type="number" name="count_[]" value="` + count + `" class="form-control countDocuments"></td>
                                         <td class="price"><input type="text" name="price[]" value="` + price + `" class="form-control PriceDocuments"></td>
                                         <td><button  type="button" class="btn rounded-pill btn-outline-danger deleteItems">Ջնջել</button></td>
                                      </tr>`.trim();
            }
        })
        console.log('7')
    })

    $('body').on('click', '.deleteDocumentItems', function () {
        var this_ = $(this);
        let id = this_.closest('.oldTr').find('.itemsId').val()
        let csrfToken = $('meta[name="csrf-token"]').attr("content");
        let url_id = window.location.href;
        let url = new URL(url_id);
        let urlId = url.searchParams.get("id");
        $.ajax({
            url: '/documents/delete-document-items',
            method: 'post',
            datatype: 'post',
            data: {
                id: id,
                urlId: urlId,
                _csrf: csrfToken
            },
            success: function (data) {
                if (data === 'true') {
                    this_.closest('.oldTr').remove();
                }
            }
        })
        console.log('8')
    })

    $('body').on('click', '.PriceDocuments', function () {
        $(this).val($(this).val().replace(/[^0-9.]/g, ''));
    })

    $('body').on('keyup', '.PriceDocuments', function () {
        $(this).val($(this).val().replace(/[^0-9.]/g, ''));
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
                trs[id.trim()] = `<tr class="tableDocuments oldTr">
                     <td>` + id + `<input type="hidden" name="document_items[]" value="` + id + `"></td>
                     <td class="name">` + name + `</td>
                     <td class="count"><input type="number" name="count_[]" value="` + count + `" class="form-control countDocuments"></td>
                     <td class="price"><input type="text" name="price[]" value="` + price + `" class="form-control PriceDocuments"></td>
                     <td><button  type="button" class="btn rounded-pill btn-outline-danger deleteItems">Ջնջել</button></td>
                  </tr>`.trim();
            }
        })
        console.log('9')
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
                trs[nom_id.trim()] = `<tr class="tableDocuments oldTr">
                                         <td> ` + nom_id +`
                                          <input type="hidden" name="document_items[]" value="new_`+ nom_id +`">
                                          <input class="itemsId" type="hidden" name="items[]" value="` + nom_id  + `">
                                         </td>
                                         <td class="name">` + name + `</td>
                                         <td class="count"><input type="number" name="count_[]" value="` + count + `" class="form-control countDocuments"></td>
                                         <td class="price"><input type="text" name="price[]" value="` + price + `" class="form-control PriceDocuments"></td>
                                         <td><button  type="button" class="btn rounded-pill btn-outline-danger deleteItems">Ջնջել</button></td>
                                      </tr>`.trim();
            }
        })
        console.log('10')
    })

    // $('body').on('click', '.by_ajax', function () {
    //     var href_ = $(this).attr('data-href');
    //     getNomDocument(href_);
    //     $('.documentsTableTr').each(function () {
    //         if ($(this).find(".documentsCountInput").val() != '') {
    //             let id = $(this).find(".nom_id").attr('data-id');
    //             let name = $(this).children(".documentsName").text();
    //             let count = parseFloat($(this).children('.documentsCount').find('.documentsCountInput').val());
    //             let price = +parseFloat($(this).children('.documentsCount').find('.documentsPriceInput').val()).toFixed(2);
    //             trs[id.trim()] = `<tr class="tableDocuments oldTr">
    //                  <td>` + id + `<input type="hidden" name="document_items[]" value="` + id + `"></td>
    //                  <td class="name">` + name + `</td>
    //                  <td class="count"><input type="number" name="count_[]" value="` + count + `" class="form-control countDocuments"></td>
    //                  <td class="price"><input type="text" name="price[]" value="` + price + `" class="form-control PriceDocuments"></td>
    //                  <td><button  type="button" class="btn rounded-pill btn-outline-danger deleteItems">Ջնջել</button></td>
    //               </tr>`.trim();
    //         }
    //     })
    // })
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
        console.log('11')
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
                    // alert(2222)
                    $('body').find('#documents-rate_value').attr('readonly', false);
                    $('body').find('#documents-rate_value').val('');
                }else if(param == 'amd'){
                    $('body').find('#documents-rate_value').attr('readonly', true);
                    $('body').find('#documents-rate_value').val(1);
                }
            }
        })
    })
})