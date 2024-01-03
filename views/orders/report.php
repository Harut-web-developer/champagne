<?php
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/colresizable/1.6.0/colResizable-1.6.min.js', [
    'depends' => 'yii\web\JqueryAsset',
    'position' => \yii\web\View::POS_END,
]);
$this->params['sub_page'] = $sub_page;
$this->params['date_tab'] = $date_tab;

?>

<div id="events">
    <button type="button" onclick="printJS('print', 'html')">Print</button>
    <button onclick="tableToExcel('w1','<?=$clients[$model['clients_id']].'&'.$model['orders_date']?>')">Export</button>
</div>

<?= $this->render('table', [
    'model' => $model,
    'users' => $users,
    'clients' => $clients,
    'nomenclatures' => $nomenclatures,
    'order_items' => $order_items,
    'total' => $total,
    'is_export' => true,
]) ?>
<script src="https://cdn.jsdelivr.net/npm/print-js"></script>

<style>
    #events {
        text-align: end;
        margin-top: 10px;
    }

    button {
        margin: 0 5px;
        background-color: #697a8d;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
    }

    button:hover {
        background-color: #a3abb4;
    }
</style>