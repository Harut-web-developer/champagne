<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Rates $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="rates-form">
    <div class="card card-primary">
        <?php $form = ActiveForm::begin(); ?>
        <div class="default-panel">
            <div class="panel-title premission">
                <span class="non-active">Փոխարժեք</span>
            </div>
            <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName">
                <?= $form->field($model, 'name')->textInput(['maxlength' => 255,'required' => true]) ?>
            </div>
        </div>
            <div class="card-footer">
                <?= Html::submitButton('Պահպանել', ['class' => 'btn rounded-pill  btn-secondary submit_save']) ?>
            </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
