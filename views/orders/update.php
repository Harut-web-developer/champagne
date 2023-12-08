<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Orders $model */

$this->title = 'Փոփոխել վաճառքը: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="orders-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'users' => $users,
        'clients' => $clients,
        'nomenclatures' => $nomenclatures,
        'order_items' => $order_items,
        'total' => $total,
    ]) ?>

</div>
