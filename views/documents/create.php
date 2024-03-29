<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Documents $model */

$this->title = 'Ստեղծել փաստաթուղթ';
$this->params['breadcrumbs'][] = ['label' => 'Documents', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['sub_page'] = $sub_page;
$this->params['date_tab'] = $date_tab;

?>
<div class="documents-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'clietns' => $clietns,
        'warehouse' => $warehouse,
        'rates' => $rates,
        'nomenclatures' => $nomenclatures,
        'total' => $total,
        'to_warehouse' => $to_warehouse,
//        'delivered_documents' => $delivered_documents
    ]) ?>

</div>
