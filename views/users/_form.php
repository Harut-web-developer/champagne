<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Users $model */
/** @var yii\widgets\ActiveForm $form */
?>

    <div class="users-form">
        <div class="card card-primary">
            <?php $form = ActiveForm::begin(); ?>
            <div class="card-body formDesign">
                <div class="form-group col-md-5 col-lg-3 col-sm-6 usersName">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="form-group col-md-5 col-lg-3 col-sm-6 usersUsername">
                    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="form-group col-md-5 col-lg-3 col-sm-6 usersUsername">
                    <?= $form->field($model, 'role_id')->dropDownList($roles) ?>
                </div>
                <div class="form-group col-md-5 col-lg-3 col-sm-6 usersPassword">
                    <?= $form->field($model, 'password')->passwordInput() ?>
                </div>
            </div>
            <div class="card-footer">
                <?= Html::submitButton('Save', ['class' => 'btn rounded-pill btn-secondary']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>


