$(document).ready(function () {
    $('body').on('click','.create', function(){
        var addOrdersTableBody = '';
        $('.addOrdersTableTr').each(function () {
            if ($(this).find("input:checkbox").is(':checked')) {
                let id = $(this).find("input:checkbox").attr('data-id');
                let name = $(this).children(".nomenclatureName").text();
                let count = $(this).children('.ordersAddCount').children('input').val();
                addOrdersTableBody +=`<tr class="tableNomenclature">
                            <th>`+id+` <input type="hidden" name="productid[]" value="`+id+`"></th>
                            <td class="name">`+name+`</td>
                            <td class="count"><input type="number" name="count_[]" value="`+count+`" class="form-control countProduct"></td>
<!--                            <td class="price">`+price+` <input type="hidden" name="price[]" value="`+price+`"></td>-->

<!--                            <td class="total">`+total+`<input type="hidden" name="total[]" value="`+total+`"></td>-->
<!--                            <td class="cost">`+cost+` <input type="hidden" name="cost[]" value="`+cost+`"></td>-->
                            <td class="btnn"><button type="button" class="btn btn-outline-danger delItems">Delete</button></td>
                         </tr>`;
            }
        })
    })
})
