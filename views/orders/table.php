<?php

use app\widgets\CustomLinkPager;

/** @var yii\web\View $this */
/** @var app\models\Orders $model */
/** @var yii\widgets\ActiveForm $form */
$this->registerCssFile('@web/css/bootstrap.min.css');
?>


<!-- Basic Bootstrap Table -->
<div id="print">
    <div class="card">
        <div id="w1" class="table-responsive text-nowrap">
            <h1>Վաճառքներ</h1>
            <table class="table">
                <thead>
                <tr>
                    <th>Օգտատեր</th>
                    <th>Հաճախորդ</th>
                    <th>Ընդհանուր գումար</th>
                    <th>Ընդհանուր քանակ</th>
                    <th>Մեկնաբանություն</th>
                    <th>Պատվերի ամսաթիվ</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><?=$users[$model->user_id]; ?></td>
                    <td><?=$clients[$model['clients_id']]; ?></td>
                    <td><?=$model['total_price']?></td>
                    <td><?=$model['total_count']?></td>
                    <td><?=$model['comment']?></td>
                    <td><?=$model['orders_date']?></td>
                </tr>
                </tbody>
            </table>
    <?php if (!empty($order_items)) {?>
    <h1>Վաճառքի ցուցակ</h1>
        <table class="table">
            <thead>
            <tr>
                <th>#</th>
                <th>ԱՆՈՒՆ</th>
                <th>ՔԱՆԱԿ</th>
                <th>ԳԻՆ</th>
                <th>ԻՆՔՆԱՐԺԵՔ</th>
                <th>ԸՆԴՀԱՆՈՒՐ ԳՈՒՄԱՐ</th>
            </tr>
            </thead>
            <tbody>
            <?php
            for ($i=0; $i<count($order_items); $i++){ ?>
            <tr>
                <td><?=$i; ?></td>
                <td><?=$order_items[$i]['name']; ?></td>
                <td><?=$order_items[$i]['count']; ?></td>
                <td><?=$order_items[$i]['price']; ?></td>
                <td><?=$order_items[$i]['cost']; ?></td>
                <td><?=$order_items[$i]['count']*$order_items[$i]['price']; ?></td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
        </div>
    </div>
    <?php }?>
</div>
<!--/ Basic Bootstrap Table -->

<script>
    function print_() {
        document.getElementById('events').style.display = "none";
        window.print();
        document.getElementById('events').style.display = "block";
    }

    function tableToExcel(table, name) {
        $('.other_tr').css('display', '');
        $('#totals_table').css('display', 'none');
        var uri = 'data:application/vnd.ms-excel;base64,',
            template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">' +
                '<meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8">' +
                '<head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head>' +
                '<body>' +
                '<table>{table}</table>' +
                '</body></html>',
            base64 = function(s) {
                return window.btoa(unescape(encodeURIComponent(s)))
            },
            format = function(s, c) {
                return s.replace(/{(\w+)}/g, function(m, p) {
                    return c[p];
                })
            }

        if (!table.nodeType) table = document.getElementById(table)
        var ctx = {
            worksheet: name || 'Worksheet',
            table: table.innerHTML
        }
        var a = document.createElement('a');
        a.href = uri + base64(format(template, ctx))
        a.download = name + '.xls';
        a.click();

        window.addEventListener("load", function() {
            $('table').colResizable();
        });
        $('.other_tr').css('display', 'none');
        $('#totals_table').css('display', '');
    }
</script>
