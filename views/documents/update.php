<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Documents $model */

$this->title = 'Փոփոխել փաստաթուղթը: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Documents', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="documents-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'users' => $users,
        'warehouse' => $warehouse,
        'rates' => $rates,
        'nomenclatures' => $nomenclatures,
        'document_items' => $document_items,
        'total' => $total,
        'aah' => $aah
    ]) ?>

</div>
