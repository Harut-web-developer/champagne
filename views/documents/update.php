<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Documents $model */

$this->title = 'Փոփոխել փաստաթուղթը: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Documents', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
$this->params['sub_page'] = $sub_page;
$this->params['date_tab'] = $date_tab;
?>
<div class="documents-update">

    <h5><?= Html::encode($this->title) ?></h5>

    <?= $this->render('_form', [
        'model' => $model,
        'users' => $users,
        'warehouse' => $warehouse,
        'rates' => $rates,
        'document_items' => $document_items,
        'aah' => $aah,
        'to_warehouse' => $to_warehouse,
        'delivered_documents' => $delivered_documents,

    ]) ?>

</div>
