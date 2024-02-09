<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\ManagerDeliverCondition $model */

$this->title = 'Update Manager Deliver Condition: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Manager Deliver Conditions', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
$this->params['sub_page'] = $sub_page;
$this->params['date_tab'] = $date_tab;
?>
<div class="manager-deliver-condition-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'sub_page' => $sub_page,
        'date_tab' => $date_tab,
        'manager_id' => $manager_id,
        'deliver_id' => $deliver_id,
        'update_value' => $update_value,
        'route' => $route,

    ]) ?>

</div>
