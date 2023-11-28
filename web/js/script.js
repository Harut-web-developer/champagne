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




});
// $('#valuemap').change(function () {
//     var location_value = $(this).val();
//     var csrfToken = $('meta[name="csrf-token"]').attr("content");
//     console.log(location_value)
//     $.ajax({
//         url: "/map/location-value",
//         method: 'post',
//         dataType: 'json',
//         data: {
//             locationvalue: location_value,
//             _csrf: csrfToken
//         },
//         success:function (data){
//             console.log(data)
//         }
//     });
// });


