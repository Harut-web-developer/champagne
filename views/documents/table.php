<?php

use app\widgets\CustomLinkPager;

/** @var yii\web\View $this */
/** @var app\models\Orders $model */
/** @var yii\widgets\ActiveForm $form */

$this->registerCssFile('@web/css/bootstrap.min.css');
$id = $_GET['id'];
$id = rtrim($id, '//');
$res = Yii::$app->db->createCommand('
    SELECT 
        customfields_blocks_inputs.label as `attribute`,
        customfields_blocks_inputs.id, 
        customfields_blocks_input_values.value_, 
        customfields_blocks_title.title 
    FROM customfields_blocks_title 
    LEFT JOIN customfields_blocks_inputs 
        ON customfields_blocks_inputs.iblock_id = customfields_blocks_title.id      
    LEFT JOIN customfields_blocks_input_values 
        ON customfields_blocks_input_values.input_id = customfields_blocks_inputs.id      
    WHERE customfields_blocks_title.page = :page
    AND customfields_blocks_input_values.item_id = :id
')->bindValues([
    ':page' => 'documents',
    ':id' => $id,
])->queryAll();
$res_title = Yii::$app->db->createCommand('
    SELECT customfields_blocks_title.title 
    FROM customfields_blocks_title 
    WHERE customfields_blocks_title.page = "documents" 
    AND customfields_blocks_title.block_type = "0"
')->queryOne();
?>
<div id="print">
    <div class="card">
        <div id="documents_table" class="table-responsive text-nowrap">
            <h1><?=$res_title['title']?></h1>
            <table class="table">
                <thead>
                    <tr>
                    <th>№</th>
                    <th>Պահեստ</th>
                    <th>Փաստաթղթի տեսակ</th>
                    <?php if (isset($model->to_warehouse)) { ?>
                        <th>Տեղափոխվող պահեստ</th>
                    <?php } ?>
                    <?php if (isset($model->document_type) && $model->document_type == 10) { ?>
                        <th>Հաճախորդներ</th>
                        <th>Հաստատված փաստաթղթեր</th>
                        <th>Առաքիչ</th>
                    <?php } ?>
                    <th>Օգտատեր</th>
                    <th>Փոխարժեք</th>
                    <th>Մեկնաբանություն</th>
                    <th>Ստեղծման ժամանակ</th>
                    <?php if(!empty($res)){
                        for ($i = 0; $i < count($res); $i++){ ?>
                            <th><?= $res[$i]['attribute'] ?></th>
                    <?php } } ?>
                    </tr>
                </thead>
                <tbody>
                <tr>
                    <td>1</td>
                    <td><?=$warehouse[$model->warehouse_id]?></td>
                    <td>
                        <?php
                        if ($model->document_type == 1) {
                            echo 'Մուտքի';
                        } elseif($model->document_type == 2) {
                            echo 'Ելքի';
                        } elseif($model->document_type == 3) {
                            echo 'Տեղափոխություն';
                        } elseif($model->document_type == 4) {
                            echo 'Խոտան';
                        } elseif($model->document_type == 6) {
                            echo 'Վերադարձրած';
                        } elseif($model->document_type == 7) {
                            echo 'Մերժված';
                        } elseif($model->document_type == 8){
                            echo 'Մուտք(վերադարցրած)';
                        } elseif ($model->document_type == 9){
                            echo 'Պատվերից ելքագրված';
                        } elseif ($model->document_type == 10){
                            echo 'Ետ վերադարցրած';
                        }
                        ?>
                    </td>
                    <?php if (isset($model->to_warehouse)) { ?>
                        <td><?=$warehouse[$model->to_warehouse] ?></td>
                    <?php } ?>
                    <?php if (isset($model->document_type) && $model->document_type == 10) { ?>
                        <td><?=$delivered_documents['name']?></td>
                        <td>Հաստատված պատվեր <?=$delivered_documents['orders_date']?></td>
                        <td><?=$delivered_documents['deliver_name']?></td>
                    <?php } ?>
                    <td>
                        <?php
                        if ($model->user_id == null){
                            echo 'Դատարկ';
                        }else{
                           echo $users[$model->user_id];
                        }
                         ?>
                    </td>
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
                    <?php if (!empty($res)) {
                        for ($i = 0; $i < count($res); $i++) {
                            if (isset($res[$i]['value_']) && is_string($res[$i]['value_']) && strpos($res[$i]['value_'], 'uploads/cf/') !== false) { ?>
                                <td><img src="/<?= htmlspecialchars($res[$i]['value_']) ?>" style="width:100px;height:70px" alt=""></td>
                            <?php } else { ?>
                                <td><?= isset($res[$i]['value_']) ? htmlspecialchars($res[$i]['value_']) : '' ?></td>
                            <?php }
                        }
                    } ?>
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
                '<body>{table}</body></html>',
            base64 = function(s) {
                return window.btoa(unescape(encodeURIComponent(s)));
            },
            format = function(s, c) {
                return s.replace(/{(\w+)}/g, function(m, p) {
                    return c[p];
                });
            };

        if (!table.nodeType) table = document.getElementById(table);
        var tableHTML = table.innerHTML;
        var images = table.getElementsByTagName('img');
        for (var i = 0; i < images.length; i++) {
            var imgSrc = images[i].src;
            var imgWidth = images[i].width; // Get the image width
            var imgHeight = images[i].height; // Get the image height
            var imgTag = '<img src="' + imgSrc + '" width="' + imgWidth + '" height="' + imgHeight + '">';
            tableHTML = tableHTML.replace(images[i].outerHTML, imgTag);
        }
        var ctx = {
            worksheet: name || 'Worksheet',
            table: tableHTML
        };
        var a = document.createElement('a');
        a.href = uri + base64(format(template, ctx));
        a.download = name + '.xls';
        a.click();
        window.addEventListener("load", function() {
            $('table').colResizable();
        });
        $('.other_tr').css('display', 'none');
        $('#totals_table').css('display', '');
    }
</script>
