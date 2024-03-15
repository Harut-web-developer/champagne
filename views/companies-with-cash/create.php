<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\CompaniesWithCash $model */
$this->params['sub_page'] = $sub_page;
$this->params['date_tab'] = $date_tab;
$this->title = 'Ստեղծել ընկերություն';
$this->params['breadcrumbs'][] = ['label' => 'Companies With Cashes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="companies-with-cash-create">

    <h5><?= Html::encode($this->title) ?></h5>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
