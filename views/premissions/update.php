<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Premissions $model */

$this->title = 'Փոփոխել թույլտվությունը: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Premissions', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
$this->params['sub_page'] = $sub_page;
?>
<div class="premissions-update">

    <h5><?= Html::encode($this->title) ?></h5>

    <?= $this->render('_form', [
        'model' => $model,
        'roles' => $roles,

    ]) ?>

</div>
