<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Route $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="route-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'route')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->dropDownList([ 1 => '1', 0 => '0', ], ['prompt' => '']) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn rounded-pill btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
