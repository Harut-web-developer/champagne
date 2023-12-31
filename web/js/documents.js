$(document).ready(function () {
    $('body').on('click','.createDocuments', function(){
        var documentsTableBody = '';
        var newTbody = $('<tbody></tbody>');
        $('.documentsTableTr').each(function () {
            if ($(this).find("input:checkbox").is(':checked')) {
                let id = $(this).find("input:checkbox").attr('data-id');
                let name = $(this).children(".documentsName").text();
                let count = parseFloat($(this).children('.documentsCount').find('.documentsCountInput').val());
                let price = +parseFloat($(this).children('.documentsCount').find('.documentsPriceInput').val()).toFixed(2);
                documentsTableBody +=`<tr class="tableDocuments">
                                         <td>`+id+`<input type="hidden" name="document_items[]" value="`+id+`"></td>
                                         <td class="name">`+name+`</td>
                                         <td class="count"><input type="number" name="count_[]" value="`+count+`" class="form-control countDocuments"></td>
                                         <td class="price"><input type="text" name="price[]" value="`+price+`" class="form-control priceDocuments"></td>
                                         <td><button  type="button" class="btn rounded-pill btn-outline-danger deleteItems">Ջնջել</button></td>
                                      </tr>`;
            }
        })
        newTbody.append(documentsTableBody);
        $('.documentsAddingTable tbody').replaceWith(newTbody);
    })
    $('body').on('click','.deleteItems', function () {
        $(this).closest('.tableDocuments').remove();
    })

    $('body').on('click','.updateDocuments', function(){
        var documentsTableBody = '';
        $('.documentsTableTr').each(function () {
            if ($(this).find("input:checkbox").is(':checked')) {
                let id = $(this).find("input:checkbox").attr('data-id');
                let name = $(this).children(".documentsName").text();
                let count = parseFloat($(this).children('.documentsCount').find('.documentsCountInput').val());
                let price = +parseFloat($(this).children('.documentsCount').find('.documentsPriceInput').val()).toFixed(2);
                documentsTableBody +=`<tr class="tableDocuments">
                                         <td>`+id+`<input type="hidden" name="document_items[]" value="null"><input type="hidden" name="items[]" value="`+id+`"></td>
                                         <td class="name">`+name+`</td>
                                         <td class="count"><input type="number" name="count_[]" value="`+count+`" class="form-control countDocuments"></td>
                                         <td class="price"><input type="text" name="price[]" value="`+price+`" class="form-control priceDocuments"></td>
                                         <td><button  type="button" class="btn rounded-pill btn-outline-danger deleteItems">Ջնջել</button></td>
                                      </tr>`;
            }
        })
        $('.documentsAddingTable tbody').parent().append(documentsTableBody);
    })
    $('body').on('click', '.deleteDocumentItems', function (){
        var this_ = $(this);
        let id = this_.closest('.oldTr').find('.itemsId').val()
        let csrfToken = $('meta[name="csrf-token"]').attr("content");
        $.ajax({
            url:'/documents/delete-document-items',
            method:'post',
            datatype:'post',
            data:{
                id:id,
                _csrf:csrfToken
            },
            success:function (data){
                if (data === 'true'){
                    this_.closest('.oldTr').remove();
                }
            }
        })
    })

    $('body').on('click','.priceDocuments',function () {
        $(this).val($(this).val().replace(/[^0-9.]/g, ''));
    })
    $('body').on('keyup','.priceDocuments',function () {
        $(this).val($(this).val().replace(/[^0-9.]/g, ''));
    })

    $('body').on('keyup', '.searchForDocument',function () {
        var nomenclature = $(this).val();
        let current_href = $('body').find('.active .by_ajax').data('href');
        getNomDocument( current_href+'&nomenclature='+nomenclature);
        // $.ajax({
        //     url:'/documents/search',
        //     method:'post',
        //     datatype:'json',
        //     data:{
        //         nomenclature:nomenclature,
        //         _csrf:csrfToken
        //     },
        //     success:function (data) {
        //         let parse = JSON.parse(data);
        //         $('.tbody_').html('');
        //         var html_ = '';
        //         parse.nomenclature.forEach(function (item) {
        //             html_ = `<tr class="documentsTableTr">
        //                 <td>`+item.id+`</td>
        //                 <td><input data-id="`+item.id+`" type="checkbox"></td>
        //                 <td class="documentsName">`+item.name+`</td>
        //                 <td class="documentsCount">
        //                     <input type="number" class="form-control documentsCountInput">
        //                     <input class="documentsPriceInput" type="hidden" value="`+item.price+`">
        //                 </td>
        //             </tr>`;
        //             $('.tbody_').append(html_);
        //         })
        //     }
        // })
    })

    $('body').on('click', '.by_ajax',function () {
        var href_ = $(this).attr('data-href');
        getNomDocument(href_);
    })
    function getNomDocument(href_) {
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
