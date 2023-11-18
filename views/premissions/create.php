<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Premissions $model */

$this->title = 'Create Premissions';
$this->params['breadcrumbs'][] = ['label' => 'Premissions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="premissions-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'roles' => $roles,

    ]) ?>

</div>
