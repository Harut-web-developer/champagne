<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Premissions $model */

$this->title = 'Փոփոխել թույլտվությունը: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Premissions', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="premissions-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'roles' => $roles,

    ]) ?>

</div>
