$(document).ready(function() {
    var x = "/" + window.location.href.split("/")[3];
    var y = "/" + window.location.href.split("/")[4];
    var t = 0;
    $(".menu-sub a").each(function () {
        var currentHref = $(this).attr("href");

        if ((currentHref === x && y === '/undefined') || currentHref === x + y) {
            $(this).parent().addClass("active");
        } else if (currentHref.indexOf(x) !== -1 && t === 0) {
            if (y !== '/undefined') {
                $(this).parent().addClass("active");
                t++;
            }
        }
    });

    // var pgurl = window.location.href.substr(window.location.href
    //     .lastIndexOf("/")+1);
    // $(".menu-sub a ").each(function(){
    //     if($(this).attr("href") == '/'+pgurl || $(this).attr("href") == '' )
    //         $(this).parent().addClass("active");
    // })

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
                input_ += '<input type="text" name="new_fild_name['+num_+'][0][]" placeholder="Դաշտի անվանումը"><input name="new_fild_value['+num_+'][0][]" type="number">';
                break;
            case 'varchar':
                input_ += '<input type="text" name="new_fild_name['+num_+'][1][]" placeholder="Դաշտի անվանումը"><input name="new_fild_value['+num_+'][1][]" type="text">';
                break;
            case 'list':
                var list_num = $('.list_el').length;
                input_ += '<input type="text" name="new_fild_name['+num_+'][2]['+list_num+'][]" placeholder="Դաշտի անվանումը">' +
                    '<input name="new_fild_value['+num_+'][2]['+list_num+'][]" type="text" placeholder="Տարբերակ" class="list_el"><div class="new_options"></div><i class="bx bx-plus add_list_item" ></i>';
                break;
            case 'file':
                input_ += '<input type="text" name="new_fild_name['+num_+'][3][]" placeholder="Դաշտի անվանումը"><input type="hidden" name="new_fild_value['+num_+'][3][]"><input name="new_fild_value['+num_+'][3][]" type="file">';
                break;
            case 'text':
                input_ += '<input type="text" name="new_fild_name['+num_+'][4][]" placeholder="Դաշտի անվանումը"><textarea name="new_fild_value['+num_+'][4][]" ></textarea>';
                break;
            case 'date':
                input_ += '<input type="text" name="new_fild_name['+num_+'][5][]" placeholder="Դաշտի անվանումը"><input name="new_fild_value['+num_+'][5][]" type="date">';
                break;
            case 'datetime':
                input_ += '<input type="text" name="new_fild_name['+num_+'][6][]" placeholder="Դաշտի անվանումը"><input name="new_fild_value['+num_+'][6][]" type="datetime-local">';
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

    //notifications
    function fetchNotifications() {
        var csrfToken = $('meta[name="csrf-token"]').attr("content");
        $.ajax({
            type: "GET",
            url: "/site/get-notifications",
            dataType: "json",
            data: { _csrf: csrfToken },
            success: function (data) {
                if (data['notifications_today'] !== 0) {
                    displayNotifications(data, data['notifications_today']);
                    $('body').on('click','#viweall',function () {
                        // displayNotifications(data['notifications_all']);
                        var notifications = data['notifications_all'];
                        var notificationsDropdown = $("#notifications-dropdown");
                        notificationsDropdown.empty();
                        notificationsDropdown.append('<div class="notification-ui_dd-header">\n' +
                            '<h3 class="text-center">Ծանուցումներ</h3>\n' +
                            '</div>' +
                            '<hr>'
                        );
                        notifications.forEach(function (notification) {
                            notificationsDropdown.append('<div class="notification-item">' +
                                '<p class="notification-title">' +
                                '<span class="title-text">' + notification.title + '</span>' +
                                '</br>' +
                                notification.message +
                                '<br>' +
                                '<small style="font-size: 60%">' +
                                notification.datetime +
                                '</small>' +
                                '</p>' +
                                '</div>');
                        });
                    })
                }
            }
        });
    }
    function fetchNotificationstoast() {
        var csrfToken = $('meta[name="csrf-token"]').attr("content");
        $.ajax({
            url: '/site/check-notifications',
            type: 'GET',
            dataType: 'json',
            success: function (data) {
                if (data.success !== undefined) {
                    displayNotificationtoast(data.notifications);
                }
            },
        });
        $.ajax({
            url:"/site/index-notifications",
            method: 'get',
            dataType:'json',
            data:{
                _csrf : csrfToken
            },
            success:function(data){
                if (data['notification_badge']>0){
                    $('.index_not').text(data['notification_badge'])
                }
            }
        })
    }
    function displayNotifications(data, notifications) {
        $('.index_not').text('')
        var notificationsDropdown = $("#notifications-dropdown");
        notificationsDropdown.empty();
        notificationsDropdown.append('<div class="notification-ui_dd-header">\n' +
            '<h3 class="text-center">Ծանուցումներ</h3>\n' +
            '</div>' +
            '<hr>'
        );
        notifications.forEach(function (notification) {
            notificationsDropdown.append('<div class="notification-item">' +
                '<p class="notification-title">' +
                '<span class="title-text">' + notification.title + '</span>' +
                '</br>' +
                notification.message +
                '<br>' +
                '<small style="font-size: 60%">' +
                notification.datetime +
                '</small>' +
                '</p>' +
                '</div>');
        });
        notificationsDropdown.append('<div id="viweall" class="notification-ui_dd-footer">\n' +
            '<a href="#!" class="btn bg-secondary text-white" style="display: block">Տեսնել բոլորը</a>\n' +
            '</div>'
        );
    }
    function displayNotificationtoast(notification) {
        if (notification!=null) {
            $('.bs-toast .toast-header .me-auto').text(notification.title);
            $('.bs-toast .toast-body').html('');
            $('.bs-toast .toast-body').append(notification.message);
            $('.bs-toast').toast('show');
        }
    }
    $(".bell-icon").click(function () {
        $("#notifications-dropdown").toggle();
        fetchNotifications();
        $('.index_not').text('');
        var csrfToken = $('meta[name="csrf-token"]').attr("content");
        $.ajax({
            url:"/site/index-notifications-click",
            method: 'get',
            dataType:'json',
            data:{
                _csrf : csrfToken
            },
        })
    });
    $('#notificationBell').click(function () {
        fetchNotifications();
    });
    fetchNotifications();
    fetchNotificationstoast();
    setInterval(fetchNotificationstoast, 10000);

    $(document).mouseup(function(e)
    {
        var container = $("#notifications-dropdown");
        if (!container.is(e.target) && container.has(e.target).length === 0)
        {
            container.hide();
        }
    });

    // downloadXLSX orders
    $('.downloadXLSX').click(function () {
        var excel = new ExcelJS.Workbook();
        var tables = '';
        var sheetNumber = 1;
        var PromiseArray = [];
        let clientsVal = $('.content-wrapper').find('.changeClients').val();
        let managerId = $('.content-wrapper').find('.changeManager').val();
        let numberVal = $('.content-wrapper').find('.orderStatus').val();
        let clickXLSX = 'clickXLSX';
        let ordersDate = $('.ordersDate').val();
        // let type = $(this).val()
        var csrfToken = $('meta[name="csrf-token"]').attr("content");
        $.ajax({
            url:'/orders/filter-status',
            method: 'get',
            data: {
                _csrf: csrfToken,
                action: 'xls-alldata',
                numberVal:numberVal,
                clientsVal:clientsVal,
                managerId:managerId,
                ordersDate:ordersDate,
                // type:type,
                clickXLSX:clickXLSX,
            },
            dataType: "html",
            success: function(data) {
                $('body').append(data);
                tables = document.getElementsByClassName("chatgbti_");
                $(".chatgbti_").hide();
                $(".deletesummary").hide();
                for (var i = 0; i < tables.length; i++) {
                    var table = tables[i];
                    var sheet = excel.addWorksheet("Sheet " + sheetNumber);
                    var headRow = table.querySelector("thead tr");
                    if (headRow) {
                        var headerData = [];
                        var headerCells = headRow.querySelectorAll("th:not(:nth-child(2))");
                        headerCells.forEach(function (headerCell) {
                            headerData.push(headerCell.textContent);
                        });
                        sheet.addRow(headerData);
                    }
                    var rows = table.querySelectorAll("tbody tr");
                    rows.forEach(function (row) {
                        var rowData = [];
                        var cells = row.querySelectorAll("td:not(:nth-child(2))");
                        cells.forEach(function (cell) {
                            rowData.push(cell.textContent);
                        });
                        if (rowData.length > 0) {
                            sheet.addRow(rowData);
                        }
                    });
                    sheetNumber++;
                }
                Promise.all(PromiseArray)
                    .then(function () {
                        return excel.xlsx.writeBuffer();
                    })
                    .then(function (buffer) {
                        var blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                        var url = window.URL.createObjectURL(blob);
                        var a = document.createElement('a');
                        a.href = url;
                        const date = new Date();
                        let day = date.getDate();
                        let month = date.getMonth() + 1;
                        let year = date.getFullYear();
                        let currentDate = `${day}-${month}-${year}`;
                        a.download = 'orders-' + currentDate + ".xlsx";
                        a.style.display = 'none';
                        document.body.appendChild(a);
                        a.click();
                        window.URL.revokeObjectURL(url);
                    })
                    .catch(function (error) {
                        console.error('Error:', error);
                    });
                $(".chatgbti_").removeClass();
            },
        });
    });

    // downloadXLSX documents
    $('.documents_downloadXLSX').click(function () {
        var excel = new ExcelJS.Workbook();
        var tables = '';
        var sheetNumber = 1;
        var PromiseArray = [];
        let numberVal = $('.documents-index').find('.documentStatus').val();
        let documentsDate = $('.documentsDate').val();
        let warehouse_id = $('.documentWarehouseStatus').val();
        let clickXLSX = 'clickXLSX';
        var csrfToken = $('meta[name="csrf-token"]').attr("content");
        $.ajax({
            url:'/documents/filter-status',
            method: 'get',
            data: {
                _csrf: csrfToken,
                action: 'xls-alldata',
                numberVal:numberVal,
                documentsDate:documentsDate,
                warehouse_id:warehouse_id,
                clickXLSX:clickXLSX,
            },
            dataType: "html",
            success: function(data) {
                $('body').append(data);
                tables = document.getElementsByClassName("chatgbti_");
                $(".chatgbti_").hide();
                $(".deletesummary").hide();
                for (var i = 0; i < tables.length; i++) {
                    var table = tables[i];
                    var sheet = excel.addWorksheet("Sheet " + sheetNumber);
                    var headRow = table.querySelector("thead tr");
                    if (headRow) {
                        var headerData = [];
                        // var headerCells = headRow.querySelectorAll("th:not(:last-child)");
                        var headerCells = headRow.querySelectorAll("th");
                        headerCells.forEach(function (headerCell) {
                            headerData.push(headerCell.textContent);
                        });
                        sheet.addRow(headerData);
                    }
                    var rows = table.querySelectorAll("tbody tr");
                    rows.forEach(function (row) {
                        var rowData = [];
                        // var cells = row.querySelectorAll("td:not(:last-child)");
                        var cells = row.querySelectorAll("td");
                        cells.forEach(function (cell) {
                            rowData.push(cell.textContent);
                        });
                        if (rowData.length > 0) {
                            sheet.addRow(rowData);
                        }
                    });

                    sheetNumber++;
                }
                Promise.all(PromiseArray)
                    .then(function () {
                        return excel.xlsx.writeBuffer();
                    })
                    .then(function (buffer) {
                        var blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                        var url = window.URL.createObjectURL(blob);
                        var a = document.createElement('a');
                        a.href = url;
                        const date = new Date();
                        let day = date.getDate();
                        let month = date.getMonth() + 1;
                        let year = date.getFullYear();
                        let currentDate = `${day}-${month}-${year}`;
                        a.download = 'documents-' + currentDate + ".xlsx";
                        a.style.display = 'none';
                        document.body.appendChild(a);
                        a.click();
                        window.URL.revokeObjectURL(url);
                    })
                    .catch(function (error) {
                        console.error('Error:', error);
                    });
                $(".chatgbti_").removeClass();
            },
        });
    });

    $('body').on('change','.byPrint',function () {
        let ordersDate = $('.ordersDate').val();
        let managerId = $('.changeManager').val();
        let numberVal = $('.orderStatus').val();
        let clientsVal = $('.changeClients').val();
        let printType = $(this).val();
        let type = $('.byType').val()
        let csrfToken = $('meta[name="csrf-token"]').attr("content");
        $.ajax({
            url:'/orders/filter-status',
            method:'get',
            datatype:'json',
            data:{
                type:type,
                ordersDate:ordersDate,
                numberVal:numberVal,
                managerId:managerId,
                clientsVal:clientsVal,
                printType:printType,
                _csrf: csrfToken,
            },
            success:function (data){
                $('body').find('.card').html(data);
                clearWidget();
            }
        })
    })

    $('body').on('change','.byType',function () {
        let ordersDate = $('.ordersDate').val();
        let managerId = $('.changeManager').val();
        let numberVal = $('.orderStatus').val();
        let clientsVal = $('.changeClients').val();
        let printType = $('.byPrint').val();
        let type = $(this).val()
        let csrfToken = $('meta[name="csrf-token"]').attr("content");
        $.ajax({
            url:'/orders/filter-status',
            method:'get',
            datatype:'json',
            data:{
                type:type,
                ordersDate:ordersDate,
                numberVal:numberVal,
                managerId:managerId,
                clientsVal:clientsVal,
                printType:printType,
                _csrf: csrfToken,
            },
            success:function (data){
                $('body').find('.card').html(data);
                clearWidget();
            }
        })
    })
    $('body').on('change', '.orderStatus', function () {
        let ordersDate = $('.ordersDate').val();
        let managerId = $('.changeManager').val();
        let numberVal = $(this).val();
        let type = $('.byType').val();
        let clientsVal = $('.changeClients').val();
        let printType = $('.byPrint').val();
        let csrfToken = $('meta[name="csrf-token"]').attr("content");
        $.ajax({
            url:'/orders/filter-status',
            method:'get',
            datatype:'json',
            data:{
                type:type,
                ordersDate:ordersDate,
                numberVal:numberVal,
                managerId:managerId,
                clientsVal:clientsVal,
                printType:printType,
                _csrf: csrfToken,
            },
            success:function (data){
                $('body').find('.card').html(data);
                clearWidget()
            }
        })
    })
    $('body').on('change', '.changeManager', function () {
        let ordersDate = $('.ordersDate').val();
        let managerId = $(this).val();
        let type = $('.byType').val();
        let numberVal = $('.orderStatus').val();
        let clientsVal = $('.changeClients').val();
        let printType = $('.byPrint').val();
        let csrfToken = $('meta[name="csrf-token"]').attr("content");
        $.ajax({
            url:'/orders/filter-status',
            method:'get',
            datatype:'json',
            data:{
                type:type,
                ordersDate:ordersDate,
                numberVal:numberVal,
                managerId:managerId,
                clientsVal:clientsVal,
                printType:printType,
                _csrf: csrfToken,
            },
            success:function (data){
                $('body').find('.card').html(data);
                clearWidget()
            }
        })
    })

    $('body').on('change', '.changeClients', function () {
        let ordersDate = $('.ordersDate').val();
        let managerId = $('.changeManager').val();
        let numberVal = $('.orderStatus').val();
        let clientsVal = $(this).val();
        let type = $('.byType').val();
        let printType = $('.byPrint').val();
        let csrfToken = $('meta[name="csrf-token"]').attr("content");
        $.ajax({
            url:'/orders/filter-status',
            method:'get',
            datatype:'json',
            data:{
                type:type,
                ordersDate:ordersDate,
                numberVal:numberVal,
                managerId:managerId,
                clientsVal:clientsVal,
                printType:printType,
                _csrf: csrfToken,
            },
            success:function (data){
                $('body').find('.card').html(data);
                clearWidget()
            }
        })
    })
    $('body').on('change', '.ordersDate', function () {
        let ordersDate = $(this).val();
        let type = $('.byType').val();
        let managerId = $('.changeManager').val();
        let numberVal = $('.orderStatus').val();
        let clientsVal = $('.changeClients').val();
        let printType = $('.byPrint').val();
        let csrfToken = $('meta[name="csrf-token"]').attr("content");
        $.ajax({
            url:'/orders/filter-status',
            method:'get',
            datatype:'json',
            data:{
                type:type,
                ordersDate:ordersDate,
                numberVal:numberVal,
                managerId:managerId,
                clientsVal:clientsVal,
                printType:printType,
                _csrf: csrfToken,
            },
            success:function (data){
                $('body').find('.card').html(data);
                clearWidget()
            }
        })
    })

    $('body').on('click','.deleteBtn',function (event) {
        event.preventDefault();
        if(confirm('Are you sure you want to delete this item?')){
            window.location.href = $(this).attr('href');
        }
    })
    $('.js-example-basic-multiple').select2();

    $("#slider-range").slider({
        range:true,
        orientation:"horizontal",
        min: 0,
        max: 10000,
        values: [0, 1000000],
        step: 100,
        slide:function (event, ui) {
            if (ui.values[0] == ui.values[1]) {
                return false;
            }
            $("#min_price").val(ui.values[0]);
            $("#max_price").val(ui.values[1]);
        }
    });

    $('body').on('change', '#discount-start_date, #discount-end_date', function (){
        let start = $('#discount-start_date').val();
        let end = $('#discount-end_date').val();
        let csrfToken = $('meta[name="csrf-token"]').attr("content");
        $.ajax({
            url:'/discount/check-date',
            method:'post',
            datatype:'json',
            data:{
                start:start,
                end:end,
                _csrf: csrfToken,
            },
            success:function (data){
                let pars = JSON.parse(data);
                const today = new Date();
                const year = today.getFullYear();
                const month = String(today.getMonth() + 1).padStart(2, '0');
                const day = String(today.getDate()).padStart(2, '0');
                if(pars == 'later'){
                    alert('Զեղչի սկիզբը չի կարող ավելի շուտ լինել քան այսօրը:')
                    $('#discount-start_date').val(`${year}-${month}-${day}`);
                }else if (pars == 'more'){
                    alert('Ընտրեք ճիշտ ամսաթվեր:');
                    $('#discount-start_date').val(`${year}-${month}-${day}`);
                    $('#discount-end_date').val('');
                }
            }
        })
    })
    $('body').on('change', '.min-value, .max-value', function (){
        let min = $('.min-value').val();
        let max = $('.max-value').val();
        let csrfToken = $('meta[name="csrf-token"]').attr("content");
        $.ajax({
            url:'/discount/check-filter-value',
            method:'post',
            datatype:'json',
            data:{
                min:min,
                max:max,
                _csrf: csrfToken,
            },
            success:function (data){
                let param = JSON.parse(data);
                // console.log(param)
                if(param == 'maxMoreThanMin'){
                    alert('Թվերը գրել ճիշտ հերթականությամբ:');
                    $('.min-value').val('');
                    $('.max-value').val('');
                }
            }
        })
    })
    $(window).on('load', function (){
        $('.debtPaymentBody tr').each(function (index, element) {
            $(element).find('.balance').each(function (x,el) {
                if ($(el).text() == 0){
                    let id = $(el).closest('tr').find('.orderIdDebt').text();
                    let csrfToken = $('meta[name="csrf-token"]').attr("content");
                    $.ajax({
                        url:'/clients/get-order-id',
                        method:'post',
                        data:{
                            id:id,
                            _csrf:csrfToken
                        }
                    })
                }

            })
        })
    })
    $('body').on('click', '.customIcon',function (e) {
        if ($('.start_date').val() > $('.end_date').val()){
            alert('Ամսաթվերը գրել ճիշտ հերթականությամբ:');
            e.preventDefault()
            $('.start_date').val('');
            $('.end_date').val('')
        }
    })

    $('body').on('change','#payments-rate_id',function () {
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
                    $('body').find('#payments-rate_value').attr('readonly', false);
                    $('body').find('#payments-rate_value').val('');
                }else if(param == 'amd'){
                    $('body').find('#payments-rate_value').attr('readonly', true);
                    $('body').find('#payments-rate_value').val(1);
                }
            }
        })
    })
    $('body').on('change', '.filterClientsChart', function () {
        let clientsId = $(this).val();
        let csrfToken = $('meta[name="csrf-token"]').attr("content");
        let getHref = window.location.href
        $.ajax({
            url: '/dashboard/change-clients',
            method: 'get',
            datatype: 'html',
            data: {
                clientsId: clientsId,
                getHref:getHref,
                _csrf: csrfToken
            },
            success: function (data) {
                $('body').find('.paymentsPart').html(data)
            }
        })
    })
    //print orders table
    $('body').on('click','.print_orders_table',function (){
        var url = '/orders/print-doc';
        var t_length = $('body').find('#w0 table tbody tr').length;
        var table = '<table id="ele4">';
        $('body').find('table').each(function (){
            if($(this).css('display') === 'none'){
                $(this).remove();
            }
        })
        $('body').find('table').find('tbody tr').each(function () {
            var el = $(this).clone();
            var thead = `
                                    <tr>
                                        <th>Օգտատեր</th>
                                        <th>Հաճախորդ</th>
                                        <th>Մեկնաբանություն</th>
                                        <th>Կարգավիճակ</th>
                                        <th>Փաստաթուղթ</th>
                                        <th>Ընդհ. զեղչված գումար</th>
                                        <th>Ընդհ. գումար</th>
                                        <th>Ընդհ. զեղչի չափ</th>                                        
                                        <th>Ընդհ. քանակ</th>
                                        <th>Պատվերի ամսաթիվ</th>
                                    </tr>
                                `;
            el.find('td:nth-child(1), td:nth-child(2)').remove();
            let id = $(this).data('key');
            if (id) {
                $.ajax({
                    url: url,
                    method: 'get',
                    dataType: 'html',
                    data: { id: id },
                    success: function (data) {
                        table += thead;
                        table += '<tr>' + el.html() + '</tr>';
                        table += data;
                        for (let i = 0; i < 20; i++){
                            table += '<tr><td colspan="7"></td></tr>';
                        }
                        if (--t_length == 0) {
                            table += '</table>';
                            var $table = $(table);
                            $table.print({
                                globalStyles: false,
                                mediaPrint: false,
                                stylesheet: "http://fonts.googleapis.com/css?family=Inconsolata",
                                iframe: false,
                                noPrintSelector: ".avoid-this",
                                deferred: $.Deferred().done(function () {
                                    console.log('Printing done', arguments);
                                })
                            });
                        }
                    }

                })
            }
        })
    });

    //print document table
    $('body').on('click','.print_document_table',function (){
        var url = '/documents/print-doc';
        var t_length = $('body').find('#w0 table tbody tr').length;
        var table = '<table id="ele4">';
        $('body').find('table').each(function (){
            if($(this).css('display') === 'none'){
                $(this).remove();
            }
        })
        var thead = `
                            <th>Փաստաթղթի տեսակ</th>
                            <th>Պահեստապետ</th>
                            <th>Պահեստ</th>
                            <th>Փոխարժեք</th>
                            <th>Առաքիչ</th>
                            <th>Մեկնաբանություն</th>
                            <th>Ստեղծման Ժամանակ</th>
                        `;
        let csrfToken = $('meta[name="csrf-token"]').attr("content");
        $.ajax({
            url: '/documents/print-doc-fild',
            method: 'get',
            datatype: 'json',
            data: {
                _csrf: csrfToken
            },
            success: function(data1) {
                let param = JSON.parse(data1)
                for (let i = 0; i < param['attribute'].length; i++) {
                    thead += `<th>${param['attribute'][i]}</th>`;
                }
                thead = `<tr>`+ thead +`</tr>`;
                $('body').find('table tbody tr').each(function () {
                    var el = $(this).clone();
                    el.find('td:nth-child(1), td:nth-child(2)').remove();
                    let id = $(this).data('key');
                    if (id) {
                        $.ajax({
                            url: url,
                            method: 'get',
                            dataType: 'html',
                            data: { id: id },
                            success: function (data) {
                                table += thead;
                                table += '<tr>' + el.html() + '</tr>';
                                table += data;
                                for (let i = 0; i < 20; i++){
                                    table += '<tr><td colspan="7"></td></tr>';
                                }
                                if (--t_length == 0) {
                                    table += '</table>';
                                    var $table = $(table);
                                    $table.print({
                                        globalStyles: false,
                                        mediaPrint: false,
                                        stylesheet: "http://fonts.googleapis.com/css?family=Inconsolata",
                                        iframe: false,
                                        noPrintSelector: ".avoid-this",
                                        deferred: $.Deferred().done(function () {
                                            console.log('Printing done', arguments);
                                        })
                                    });
                                }
                            }

                        })
                    }
                })
            }
        })
    });

    $('body').on('change', '.documentsDate, .documentStatus, .documentWarehouseStatus', function () {
        let numberVal = $('.documentStatus').val();
        let documentsDate = $('.documentsDate').val();
        let warehouse_id = $('.documentWarehouseStatus').val();
        let csrfToken = $('meta[name="csrf-token"]').attr("content");
        $.ajax({
            url:'/documents/document-filter-status',
            method:'get',
            datatype:'json',
            data:{
                numberVal:numberVal,
                documentsDate:documentsDate,
                warehouse_id:warehouse_id,
                _csrf: csrfToken,
            },
            success:function (data){
                $('body').find('.card').html(data);
            }
        })
    })

    $('.js-example-basic-single').select2();
    $('body').on('change','#orders-orders_date, #singleClients',function(){
        if($('#orders-orders_date').val() != '' && $('#singleClients').val() != ''){
            $('body').find('.addOrders').attr('disabled',false);
        }
    })

    $('body').on('change', '.productStatus', function () {
        let numberVal = $(this).val();
        let csrfToken = $('meta[name="csrf-token"]').attr("content");
        $.ajax({
            url:'/products/products-filter-status',
            method:'get',
            datatype:'json',
            data:{
                numberVal:numberVal,
                _csrf: csrfToken,
            },
            success:function (data){
                $('body').find('.card').html(data);
            }
        })
    })
    $('body').on('change','#users-role_id', function () {
        if ($(this).val() == 4){
            $('body').find('.warehouseCheck').addClass('activeForInput');
            // $("#documents-to_warehouse").attr('required',true);
        }else {
            $('body').find('.warehouseCheck').removeClass('activeForInput');
            // $("#documents-to_warehouse").removeAttr('required');
        }
    })
    let currentUrl = window.location.href;
    let hasUpdate = currentUrl.includes('users/update');
    if (hasUpdate){
        if ($('body').find('#users-role_id').val() == '4'){
            $('body').find('.warehouseCheck').addClass('activeForInput');
        }else {
            $('body').find('.warehouseCheck').removeClass('activeForInput');
        }
    }
    $('body').find('.card thead th').each(function () {
        if ($(this).has('a')){
            $(this).html( $(this).find('a').html())
        }
    })
    function clearWidget(){
        $('body').find('#w0 table thead th').each(function () {
            if ($(this).has('a')){
                $(this).html( $(this).find('a').html())
            }
        })
    }
    $('body').on('click','.exitOrders',function(){
        let ordersId = $(this).data('id');
        let csrfToken = $('meta[name="csrf-token"]').attr("content");
        $.ajax({
            url:'/orders/exit-modal',
            method:'get',
            datatype:'html',
            data:{
                ordersId:ordersId,
                _csrf: csrfToken,
            },
            success:function (data){
                $('body').find('.modalsExit').html(data);
            }
        })
    })
    $('body').on('click','.refuseDocument',function(){
        refuseDocument($(this));
    })

    $('body').on('keyup','#nomenclature-cost', function () {
        let inputValue = $(this).val();
        let sanitizedValue = inputValue.replace(/[^0-9.]/g, '');
        let parts = sanitizedValue.split('.');
        if (parts.length > 1) {
            parts[1] = parts[1].replace(/\./g, '');
            sanitizedValue = parts[0] + '.' + parts[1];
        }
        $(this).val(sanitizedValue);
    })
    $('body').on('keyup','#nomenclature-price', function () {
        let inputValue = $(this).val();
        let sanitizedValue = inputValue.replace(/[^0-9.]/g, '');
        let parts = sanitizedValue.split('.');
        if (parts.length > 1) {
            parts[1] = parts[1].replace(/\./g, '');
            sanitizedValue = parts[0] + '.' + parts[1];
        }
        $(this).val(sanitizedValue);
    })
    function refuseDocument(el) {
        let docId = el.data('id');
        let csrfToken = $('meta[name="csrf-token"]').attr("content");
        $.ajax({
            url:'/documents/refuse-modal',
            method:'get',
            datatype:'html',
            data:{
                documentId:docId,
                _csrf: csrfToken,
            },
            success:function (data){
                $('body').find('.modals').html(data);
            }
        })
    }
    $('body').on('keyup', '#users-username', function (){
        let this_ = $(this);
        let userText = $(this).val();
        let csrfToken = $('meta[name="csrf-token"]').attr("content");
        $.ajax({
            url:'/users/check-users',
            method:'post',
            datatype:'json',
            data:{
                userText:userText,
                _csrf: csrfToken,
            },
            success:function (data){
                let param = JSON.parse(data);
                if (param == true){
                    alert('Նման օգտանունով գրանցում կա:');
                    this_.val('');
                }
            }
        })
    })
    $('body').on('keyup', '#users-email', function (){
        let this_ = $(this);
        let userText = $(this).val();
        let csrfToken = $('meta[name="csrf-token"]').attr("content");
        $.ajax({
            url:'/users/check-mail',
            method:'post',
            datatype:'json',
            data:{
                userText:userText,
                _csrf: csrfToken,
            },
            success:function (data){
                let param = JSON.parse(data);
                if (param == true){
                    alert('Նման էլ. հասցեով գրանցում կա:');
                    this_.val('');
                }
            }
        })
    })
    $('body').on('keyup', '#users-phone', function () {
        var inputValue = $(this).val();
        var sanitizedValue = inputValue.replace(/[^0-9]/g, '');
        $(this).val(sanitizedValue);
    })
});
window.addEventListener('load', function() {
    var csrfToken = $('meta[name="csrf-token"]').attr("content");
    $.ajax({
        url:"/map/window-load-data",
        method: 'get',
        dataType:'json',
        data:{
            _csrf:csrfToken,
        },
        success:function(data){
            var currentDateStr = data['today'];
            var currentDate = new Date(currentDateStr);
            var startTime = new Date();
            startTime.setHours(8, 0, 0);
            var endTime = new Date();
            endTime.setHours(22, 0, 0);
            if (currentDate >= startTime && currentDate <= endTime) {
                function init () {
                    var location_value = data['route'];
                    var date = data['today'];
                    var managerId, deliverId = '';
                    if (data['role_id'] == 2) {
                        managerId = data['manager_id'];
                    }
                    if (data['role_id'] == 3) {
                        deliverId = data['deliver_id'];
                    }
                    if (data['role_id'] == 2 || data['role_id'] == 3) {
                        setInterval(function () {
                            var myLatitude = 40;
                            var myLongitude = 44;
                            var geolocation = ymaps.geolocation, myMap = new ymaps.Map('map', {
                                center: [40.2100725, 44.4987508],
                                zoom: 8
                            }, {
                                searchControlProvider: 'yandex#search'
                            }, {
                                buttonMaxWidth: 300
                            });
                            var myMap;
                            geolocation.get({
                                provider: 'yandex',
                                mapStateAutoApply: true
                            }).then(function (result) {
                                result.geoObjects.options.set('preset', 'islands#redCircleIcon');
                                result.geoObjects.get(0).properties.set({
                                    balloonContentBody: 'Мое местоположение'
                                });
                                myMap.geoObjects.add(result.geoObjects);
                            });
                            geolocation.get({
                                provider: 'browser',
                                mapStateAutoApply: true
                            }).then(function (result) {
                                myLatitude = result.geoObjects.get(0).geometry.getCoordinates()[0];
                                myLongitude = result.geoObjects.get(0).geometry.getCoordinates()[1];
                                result.geoObjects.options.set('preset', 'islands#blueCircleIcon');
                                console.log(myLatitude)
                                console.log(myLongitude)
                                myMap.geoObjects.add(result.geoObjects);
                                var csrfToken = $('meta[name="csrf-token"]').attr("content");
                                $.ajax({
                                    url: "/map/coordinates-user",
                                    method: 'post',
                                    dataType: 'json',
                                    data: {
                                        myLatitude: myLatitude,
                                        myLongitude: myLongitude,
                                        route_id: location_value,
                                        date:date,
                                        manager:managerId,
                                        deliver:deliverId,
                                        _csrf: csrfToken,
                                    },
                                });
                            }).catch(function (error) {
                                console.log(error);
                            });
                        }, 1000);
                    }
                }
                ymaps.ready(init);
            }
        }
    })
    ymaps.ready(init);
})