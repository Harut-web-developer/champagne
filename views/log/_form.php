<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Log $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="log-form">
    <div class="card card-primary">
        <?php $form = ActiveForm::begin(); ?>
            <div class="card-body formDesign">
                <div class="form-group col-md-5 col-lg-3 col-sm-6 loguser">
                    <?= $form->field($model, 'user_id')->dropDownList($log) ?>
                </div>
                <div class="form-group col-md-5 col-lg-3 col-sm-6 logAction">
                    <?= $form->field($model, 'action')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="form-group col-md-5 col-lg-3 col-sm-6 logDate">
                    <?= $form->field($model, 'create_date')->textInput() ?>
                </div>
            </div>
            <div class="card-footer">
                <?= Html::submitButton('Save', ['class' => 'btn rounded-pill btn-secondary']) ?>
            </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
