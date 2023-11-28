<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Discount $model */

$this->title = 'Ստեղծել զեղչ';
$this->params['breadcrumbs'][] = ['label' => 'Discounts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="discount-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'clients' => $clients,
        'products' => $products
    ]) ?>

</div>
