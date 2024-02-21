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
            <h1>Փաստաթուղթ</h1>
            <table class="table">
                <thead>
                <tr>
                    <th>Հ/Հ</th>
                    <th>Փաստաթղթի տեսակ</th>
                    <th>Օգտատեր</th>
                    <th>Պահեստ</th>
                    <th>Փոխարժեք</th>
                    <th>Մեկնաբանություն</th>
                    <th>Ստեղծման ժամանակ</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <th>1</th>
                    <td>
                        <?php
                            if ($model->document_type == 1){
                                echo 'Մուտքի';
                            }elseif($model->document_type == 2){
                                echo 'Ելքի';
                            }elseif($model->document_type == 3){
                                echo 'Ելքի';
                            }elseif ($model->document_type == 4){
                                echo 'Խոտան';
                            }elseif ($model->document_type == 6){
                                echo 'Վերադարձրած';
                            }elseif ($model->document_type == 7){
                                echo 'Մերժված';
                            }elseif ($model->document_type == 8){
                                echo 'Մուտք(վերադարցրած)';
                            }elseif ($model->document_type == 9){
                                echo 'Ելքագրված';
                            }
                        ?>
                    </td>
                    <td>
                        <?php
                        if ($model->user_id == null){
                            echo 'Դատարկ';
                        }else{
                           echo $users[$model->user_id];
                        }
                         ?>
                    </td>
                    <td><?=$warehouse[$model->warehouse_id]?></td>
                    <td><?=$rate[$model->rate_id]?></td>
                    <td><?php
                        if (!empty($model->comment)){
                            echo $model->comment;
                        }else{
                            echo 'Դատարկ';
                        }
                        ?>
                    </td>
                    <td><?=$model->date?></td>
                </tr>
                </tbody>
            </table>
            <?php if (!empty($document_items)) {?>
            <h1>Ապրանքի ցուցակ</h1>
            <table class="table">
                <thead>
                <tr>
                    <th>#</th>
                    <th>ԱՆՈՒՆ</th>
                    <th>ՔԱՆԱԿ</th>
                    <th>ԳԻՆԸ ԱՌԱՆՑ ԱԱՀ-Ի</th>
                    <th>ԳԻՆԸ  ԱԱՀ-ով</th>
                    <th>ԱԱՀ</th>
                </tr>
                </thead>
                <tbody>
                <?php
                for ($i=0; $i<count($document_items); $i++){
//                    var_dump($document_items[$i]);
//                    exit();
                    ?>
                    <tr>
                        <td><?=$i + 1; ?></td>
                        <td><?=$document_items[$i]['name']; ?></td>
                        <td><?=$document_items[$i]['count'] . " հատ"; ?></td>
                        <td><?=number_format($document_items[$i]['price'],2) . " դր" ?></td>
                        <td><?=number_format($document_items[$i]['price_with_aah'],2) . " դր" ?></td>
                        <td>
                            <?php if ($document_items[$i]['AAH'] == 'true'){
                                echo 'Կիրառված է';
                            }else{
                                echo 'Կիրառված չէ';
                            }
                            ?>
                        </td>
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
