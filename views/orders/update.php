<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Orders $model */
$this->title = 'Փոփոխել վաճառքը: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
$this->params['sub_page'] = $sub_page;
$this->params['date_tab'] = $date_tab;

?>
<div class="orders-update">

    <h5><?= Html::encode($this->title) ?></h5>

    <?= $this->render('_form', [
        'model' => $model,
        'users' => $users,
        'numericValuesOnly' => $numericValuesOnly,
        'active_discount' => $active_discount,
        'clients' => $clients,
        'orders_clients' => $orders_clients,
        'order_items' => $order_items,
        'oldattributes' => $oldattributes,
        'warehouse' => $warehouse,
        'warehouse_value_update' => $warehouse_value_update,

    ]) ?>

</div>
