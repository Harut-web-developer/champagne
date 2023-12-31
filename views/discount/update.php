<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Discount $model */

$this->title = 'Փոփոխել զեղչը: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Discounts', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
$this->params['sub_page'] = $sub_page;
$this->params['date_tab'] = $date_tab;

?>
<div class="discount-update">

    <h5><?= Html::encode($this->title) ?></h5>

    <?= $this->render('_form', [
        'model' => $model,
        'clients' => $clients,
        'products' => $products,
        'discount_clients_id' => $discount_clients_id,
        'discount_client_groups_name' => $discount_client_groups_name,
        'discount_client_groups_id' => $discount_client_groups_id,
        'discount_products_id' => $discount_products_id,
        'discount_client_groups' => $discount_client_groups,
        'min' => $min,
        'max' => $max
    ]) ?>

</div>
