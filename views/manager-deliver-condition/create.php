<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\ManagerDeliverCondition $model */

$this->title = 'Ստեղծել մենեջեր-առաքիչ կապ';
$this->params['breadcrumbs'][] = ['label' => 'Manager Deliver Conditions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['sub_page'] = $sub_page;
$this->params['date_tab'] = $date_tab;
?>
<div class="manager-deliver-condition-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'sub_page' => $sub_page,
        'date_tab' => $date_tab,
        'manager_id' => $manager_id,
        'deliver_id' => $deliver_id,
        'route' => $route,
    ]) ?>

</div>
