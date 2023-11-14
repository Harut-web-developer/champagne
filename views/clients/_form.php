<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Clients $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="clients-form">
    <div class="card card-primary">
        <?php $form = ActiveForm::begin(); ?>
            <div class="card-body formDesign">
                <div class="form-group col-md-5 col-lg-3 col-sm-6 clientName">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="form-group col-md-5 col-lg-3 col-sm-6 clientLocation">
                    <?= $form->field($model, 'location')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="form-group col-md-5 col-lg-3 col-sm-6 clientPhone">
                    <?= $form->field($model, 'phone')->input('number') ?>
                </div>
            </div>
            <div class="card-footer">
                <?= Html::submitButton('Save', ['class' => 'btn rounded-pill btn-secondary']) ?>
            </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
