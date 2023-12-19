<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Nomenclature $model */

$this->title = 'Փոփոխել անվանակարգը: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Nomenclatures', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
$this->params['sub_page'] = $sub_page;
?>
<div class="nomenclature-update">

    <h5><?= Html::encode($this->title) ?></h5>

    <?= $this->render('_form', [
        'model' => $model,
        'sub_page' => $sub_page,
    ]) ?>

</div>
