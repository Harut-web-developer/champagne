<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Payments $model */

$this->title = 'Փոփոխել վճարը: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Payments', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
$this->params['sub_page'] = $sub_page;
?>
<div class="payments-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'client' => $client,
        'rates' => $rates,
    ]) ?>

</div>
