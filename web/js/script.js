$(document).ready(function() {
    $('.js-example-basic-multiple').select2();


        var pgurl = window.location.href.substr(window.location.href
            .lastIndexOf("/")+1);
        $(".menu-sub a ").each(function(){
            if($(this).attr("href") == '/'+pgurl || $(this).attr("href") == '' )
                $(this).parent().addClass("active");
        })




    $('body').on('click','.edite-block-title',function (){
        $(this).closest('.panel-title').find('.non-active').hide();
        $(this).closest('.panel-title').find('.only-active').show();
        $(this).closest('.panel-title').find('.edite-block-title-save').show();
        $(this).hide();
    });
    $('body').on('click','.edite-block-title-new',function (){
        $(this).closest('.panel-title').find('.non-active').hide();
        $(this).closest('.panel-title').find('.only-active').show();
        $(this).closest('.panel-title').find('.edite-block-title-save-new-field').show();
        $(this).hide();
    });
    $('body').on('click','.add_list_item',function (){
        var el__ = $(this).closest('.new-field').find('.list_el').clone().removeClass('list_el');
        $(this).closest('.new-field').find('.new_options').append(el__);

    });
    $('body').on('click','.create-block',function (){
        var block_ = $('.createable-panel').clone();
        block_.removeClass('createable-panel');
        block_.find('.only-active').attr('name','newblocks['+($('.default-panel').length+77777)+']');
        block_.attr('data-id',($('.default-panel').length+77777));
        $('.dinamic-form').append(block_);
    });
    $('body').on('click','.remove-field-new', function (){
        let confirm_ = confirm('Are you sure you want to delete this item?');
        if(confirm_){
            var this_ = $(this);
            var removeField = this_.closest('.new-field').data('field');
            var csrfToken = $('meta[name="csrf-token"]').attr("content");
            $.ajax({
                url:"/custom-fields/delete-field",
                method: 'post',
                dataType:'json',
                data:{
                    removeField:removeField,
                    _csrf : csrfToken
                },
                success:function(data){
                    if (data == true){
                        this_.closest('.new-field').remove();
                    }
                }
            })
        }
    })
    $('body').on('click', '.edite-block-trash', function () {
        let confirm_ = confirm('Are you sure you want to delete this item?');
        if(confirm_){
            var this_ = $(this);
            var blockId = this_.closest('.default-panel').data('id');
            var csrfToken = $('meta[name="csrf-token"]').attr("content");
            if (this_.closest('.default-panel').find('.new-field').length > 0){
                var total_ = true;
            }else {
                var total_ = false;
            }
            $.ajax({
                url:"/custom-fields/delete-block",
                method: 'post',
                dataType:'json',
                data:{
                    blockId:blockId,
                    total_:total_,
                    _csrf : csrfToken
                },
                success:function(data){
                    if (data == true){
                        this_.closest('.default-panel').remove();
                    }
                }
            })
        }

    })
    $('body').on('click','.dropdown-menu li',function (){
         var type_ = $(this).attr('data-type');
         var num_ = $(this).closest('.default-panel').attr('data-id');

         var input_ = '<div class="new-field">';
         switch (type_){
             case 'number':
                 input_ += '<input type="text" name="new_fild_name['+num_+'][0][]" placeholder="Field name"><input name="new_fild_value['+num_+'][0][]" type="number">';
                 break;
             case 'varchar':
                 input_ += '<input type="text" name="new_fild_name['+num_+'][1][]" placeholder="Field name"><input name="new_fild_value['+num_+'][1][]" type="text">';
                 break;
             case 'list':
                 var list_num = $('.list_el').length;
                 input_ += '<input type="text" name="new_fild_name['+num_+'][2]['+list_num+'][]" placeholder="Field name">' +
                     '<input name="new_fild_value['+num_+'][2]['+list_num+'][]" type="text" placeholder="option" class="list_el"><div class="new_options"></div><i class="bx bx-plus add_list_item" ></i>';
                 break;
             case 'file':
                 input_ += '<input type="text" name="new_fild_name['+num_+'][3][]" placeholder="Field name"><input type="hidden" name="new_fild_value['+num_+'][3][]"><input name="new_fild_value['+num_+'][3][]" type="file">';
                 break;
             case 'text':
                 input_ += '<input type="text" name="new_fild_name['+num_+'][4][]" placeholder="Field name"><textarea name="new_fild_value['+num_+'][4][]" ></textarea>';
                 break;
             case 'date':
                 input_ += '<input type="text" name="new_fild_name['+num_+'][5][]" placeholder="Field name"><input name="new_fild_value['+num_+'][5][]" type="date">';
                 break;
             case 'datetime':
                 input_ += '<input type="text" name="new_fild_name['+num_+'][6][]" placeholder="Field name"><input name="new_fild_value['+num_+'][6][]" type="datetime-local">';
                 break;

         }
        input_ += '<span class="remove-field" onclick="$(this).closest(\'.new-field\').remove()"><i class="bx bx-trash"></i></span></div>';
         $(this).closest('.default-panel').append(input_);
    });

    $('body').on('click','.edite-block-title-save',function (){
        var el_ = $(this).closest('.panel-title').find('.only-active');
        var this_ = $(this);
        var panel_ = $(this).closest('.default-panel');
        var val_ = el_.val();
        var id_ = panel_.attr('data-id');
        var csrfToken = $('meta[name="csrf-token"]').attr("content");
        if(val_ && id_) {
            $.ajax({
                method: "POST",
                url: "/custom-fields/update-title",
                data: {val_: val_, id_: id_, _csrf : csrfToken},
                success:function (){
                    this_.closest('.panel-title').find('.non-active').text(val_).show();
                    this_.closest('.panel-title').find('.only-active').hide();
                }
            });
        } else {
            el_.css('border','1px solid red');
        }
        $(this).closest('.panel-title').find('.non-active').removeClass('el-active');
        $(this).closest('.panel-title').find('.edite-block-title').show();
        $(this).hide();
    });
    $('body').on('click','.edite-block-title-save-new-field',function (){
        let new_val = $(this).closest('.panel-title').find('.only-active').val();
        $(this).closest('.panel-title').find('.non-active').text(new_val).show();
        $(this).closest('.panel-title').find('.only-active').hide();
        $(this).closest('.panel-title').find('.edite-block-title-new').show();
        $(this).hide();
    });

    $('.searchmain').keyup(function () {
        if ($(this).val().length >= 3) {
            let search = $(this).val();
            console.log(search)
            $.ajax({
                url:'/product/filter',
                method:'post',
                datatype: 'json',
                data:{
                    searchProduct:searchProduct,
                },
                success:function (data){
                    let parse = JSON.parse(data);
                    let html_ = '';
                    var tbody = $('<tbody></tbody>');
                    parse.forEach(function (item){
                        html_ = `<tr>
                                    <td>`+item.id+`</td>
                                    <td>`+item.category_id+`</td>   
                                    <td>`+item.name+`</td>   
                                    <td>`+item.description+`</td>  
                                    <td>`+item.price+`</td> 
                                    <td>`+item.cost+`</td>
                                    <td class="tabImg"><img src="/uploads/`+item.img+`"</td>      
                                    <td>`+item.keyword+`</td>
                                    <td>
                                        <a href="/product/view?id=`+item.id+`"><svg aria-hidden="true" style="display:inline-block;font-size:inherit;height:1em;overflow:visible;vertical-align:-.125em;width:1.125em" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M573 241C518 136 411 64 288 64S58 136 3 241a32 32 0 000 30c55 105 162 177 285 177s230-72 285-177a32 32 0 000-30zM288 400a144 144 0 11144-144 144 144 0 01-144 144zm0-240a95 95 0 00-25 4 48 48 0 01-67 67 96 96 0 1092-71z"></path></svg></a>
                                        <a href="/product/update?id=`+item.id+`"><svg aria-hidden="true" style="display:inline-block;font-size:inherit;height:1em;overflow:visible;vertical-align:-.125em;width:1em" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M498 142l-46 46c-5 5-13 5-17 0L324 77c-5-5-5-12 0-17l46-46c19-19 49-19 68 0l60 60c19 19 19 49 0 68zm-214-42L22 362 0 484c-3 16 12 30 28 28l122-22 262-262c5-5 5-13 0-17L301 100c-4-5-12-5-17 0zM124 340c-5-6-5-14 0-20l154-154c6-5 14-5 20 0s5 14 0 20L144 340c-6 5-14 5-20 0zm-36 84h48v36l-64 12-32-31 12-65h36v48z"></path></svg></a>
                                        <a href="/product/delete?id=`+item.id+`"><svg aria-hidden="true" style="display:inline-block;font-size:inherit;height:1em;overflow:visible;vertical-align:-.125em;width:.875em" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M32 464a48 48 0 0048 48h288a48 48 0 0048-48V128H32zm272-256a16 16 0 0132 0v224a16 16 0 01-32 0zm-96 0a16 16 0 0132 0v224a16 16 0 01-32 0zm-96 0a16 16 0 0132 0v224a16 16 0 01-32 0zM432 32H312l-9-19a24 24 0 00-22-13H167a24 24 0 00-22 13l-9 19H16A16 16 0 000 48v32a16 16 0 0016 16h416a16 16 0 0016-16V48a16 16 0 00-16-16z"></path></svg></a>
                                    </td>
                                 </tr>`;
                        tbody.append(html_);
                    })
                    $('.searchTab').children('.grid-view').children('.table').children('tbody').replaceWith(tbody);
                    $('.searchTab').children('.grid-view').children('.pagination').html('');

                }
            })
        }else if($(this).val().length === 0) {
            // alert(1)
            location.reload();
        }
    })
});


