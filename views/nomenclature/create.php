<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Nomenclature $model */

$this->title = 'Ստեղծել անվանակարգը';
$this->params['breadcrumbs'][] = ['label' => 'Nomenclatures', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['sub_page'] = $sub_page;
$this->params['date_tab'] = $date_tab;

?>
<div class="nomenclature-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'discounts' => $discounts
    ]) ?>

</div>
