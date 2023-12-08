<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Orders $model */

$this->title = 'Ստեղծել վաճառքներ';
$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['sub_page'] = $sub_page;
?>
<div class="orders-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'users' => $users,
        'clients' => $clients,
        'nomenclatures' => $nomenclatures,
        'total' => $total,
//        'pagination' => $pagination,
//        'count' => $count,
    ]) ?>

</div>
