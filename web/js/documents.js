$(document).ready(function () {
    var id_count = {};
    $('body').on('input', '.documentsCountInput', function () {
        count_id_mariam($(this));
    });
    function count_id_mariam(el) {
        let id = el.closest('tr').find('.nom_id').data('id');
        let count = el.val();
        if (count) {
            id_count[String(id).trim()] = parseInt(count.trim());
        }else{
            delete id_count[String(id).trim()];
        }
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
                trs[id.trim()] = `<tr class="tableDocuments">
                     <td>` + id + `<input type="hidden" name="document_items[]" value="` + id + `"></td>
                     <td class="name">` + name + `</td>
                     <td class="count"><input type="number" name="count_[]" value="` + count + `" class="form-control countDocuments"></td>
                     <td class="price"><input type="text" name="price[]" value="` + price + `" class="form-control priceDocuments"></td>
                     <td><button  type="button" class="btn rounded-pill btn-outline-danger deleteItems">Ջնջել</button></td>
                  </tr>`.trim();
            }
        })
        console.log(trs)
        for (let i in trs) {
            if(trs[i] != ''){
                newTbody.append(trs[i]);
            }
        }
        newTbody.append(documentsTableBody);
        $('.documentsAddingTable tbody').replaceWith(newTbody);
    })
    $('body').on('click', '.deleteItems', function () {
        $(this).closest('.tableDocuments').remove();
    })

    const old_table = $('.table.documentsAddingTable').find('.old_tbody').html();
    $('body').on('click', '.updateDocuments', function () {
        console.log(old_table)
        var documentsTableBody = '';
        $('.documentsAddingTable tbody').html('')
        $('.documentsTableTr').each(function () {
            if ($(this).find(".documentsCountInput").val() != '') {
                let nom_id = $(this).find(".nom_id").attr('data-id');
                let name = $(this).children(".documentsName").text();
                let count = parseFloat($(this).children('.documentsCount').find('.documentsCountInput').val());
                let price = +parseFloat($(this).children('.documentsCount').find('.documentsPriceInput').val()).toFixed(2);
                trs[nom_id.trim()] = `<tr class="tableDocuments oldTr">
                                     <td> new
                                      <input type="hidden" name="document_items[]" value="new">
                                      <input class="itemsId" type="hidden" name="items[]" value="` + nom_id  + `">
                                     </td>
                                     <td class="name">` + name + `</td>
                                     <td class="count"><input type="number" name="count_[]" value="` + count + `" class="form-control countDocuments"></td>
                                     <td class="price"><input type="text" name="price[]" value="` + price + `" class="form-control priceDocuments"></td>
                                     <td><button  type="button" class="btn rounded-pill btn-outline-danger deleteItems">Ջնջել</button></td>
                                  </tr>`.trim();
            }
        })
        console.log(trs)
        newTbody.append(old_table);
        for (let i in trs) {
            if(trs[i] != ''){
                newTbody.append(trs[i]);
            }
        }
        newTbody.append(documentsTableBody);
        $('.documentsAddingTable tbody').replaceWith(newTbody);
    })
    $('body').on('click', '.deleteDocumentItems', function () {
        var this_ = $(this);
        let id = this_.closest('.oldTr').find('.itemsId').val()
        let csrfToken = $('meta[name="csrf-token"]').attr("content");
        $.ajax({
            url: '/documents/delete-document-items',
            method: 'post',
            datatype: 'post',
            data: {
                id: id,
                _csrf: csrfToken
            },
            success: function (data) {
                if (data === 'true') {
                    this_.closest('.oldTr').remove();
                }
            }
        })
    })
    $('body').on('click', '.priceDocuments', function () {
        $(this).val($(this).val().replace(/[^0-9.]/g, ''));
    })
    $('body').on('keyup', '.priceDocuments', function () {
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
                trs[id.trim()] = `<tr class="tableDocuments">
                     <td>` + id + `<input type="hidden" name="document_items[]" value="` + id + `"></td>
                     <td class="name">` + name + `</td>
                     <td class="count"><input type="number" name="count_[]" value="` + count + `" class="form-control countDocuments"></td>
                     <td class="price"><input type="text" name="price[]" value="` + price + `" class="form-control priceDocuments"></td>
                     <td><button  type="button" class="btn rounded-pill btn-outline-danger deleteItems">Ջնջել</button></td>
                  </tr>`.trim();
            }
        })
    })
    $('body').on('click', '.by_ajax', function () {
        var href_ = $(this).attr('data-href');
        getNomDocument(href_);

        var documentsTableBody = '';
        $('.documentsTableTr').each(function () {
            if ($(this).find(".documentsCountInput").val() != '') {
                let id = $(this).find(".nom_id").attr('data-id');
                let name = $(this).children(".documentsName").text();
                let count = parseFloat($(this).children('.documentsCount').find('.documentsCountInput').val());
                let price = +parseFloat($(this).children('.documentsCount').find('.documentsPriceInput').val()).toFixed(2);
                trs[id.trim()] = `<tr class="tableDocuments">
                     <td>` + id + `<input type="hidden" name="document_items[]" value="` + id + `"></td>
                     <td class="name">` + name + `</td>
                     <td class="count"><input type="number" name="count_[]" value="` + count + `" class="form-control countDocuments"></td>
                     <td class="price"><input type="text" name="price[]" value="` + price + `" class="form-control priceDocuments"></td>
                     <td><button  type="button" class="btn rounded-pill btn-outline-danger deleteItems">Ջնջել</button></td>
                  </tr>`.trim();
            }
        })
    })

    function getNomDocument(href_) {
        $.ajax({
            url: href_,
            method: 'post',
            datatype: 'html',
            data:{
                id_count:id_count
            },
            success: function (data) {
                $('#ajax_content').html(data);
            }
        })
    }
})