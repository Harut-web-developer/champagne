<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Payments $model */

$this->title = 'Կատարել վճար';
$this->params['breadcrumbs'][] = ['label' => 'Payments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['sub_page'] = $sub_page;
$this->params['date_tab'] = $date_tab;

?>
<div class="payments-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'client' => $client,
        'rates' => $rates,
    ]) ?>

</div>
