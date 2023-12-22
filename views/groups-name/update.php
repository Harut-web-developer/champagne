<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\GroupsName $model */

$this->title = 'Update Groups Name: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Groups Names', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
$this->params['sub_page'] = $sub_page;

?>
<div class="clients-groups-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'clients' => $clients,
        'clients_groups' => $clients_groups,
        'sub_page' => $sub_page,
    ]) ?>

</div>
