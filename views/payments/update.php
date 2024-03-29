<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Payments $model */

$this->title = 'Փոփոխել վճարը: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Payments', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
$this->params['sub_page'] = $sub_page;
$this->params['date_tab'] = $date_tab;

?>
<div class="payments-update">

    <h5><?= Html::encode($this->title) ?></h5>

    <?= $this->render('_form', [
        'model' => $model,
        'rates' => $rates,
        'payment_clients' => $payment_clients,
        'clients' => $clients,
    ]) ?>

</div>
