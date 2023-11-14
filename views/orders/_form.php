<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Orders $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="orders-form">
    <div class="card card-primary">
        <?php $form = ActiveForm::begin(); ?>
            <div class="card-body formDesign">
                <div class="form-group col-md-5 col-lg-3 col-sm-6 ordersName">
                    <?= $form->field($model, 'user_id')->dropDownList($users) ?>
                </div>
                <div class="form-group col-md-5 col-lg-3 col-sm-6 ordersTotalPrice">
                    <?= $form->field($model, 'total_price')->textInput() ?>
                </div>
                <div class="form-group col-md-5 col-lg-3 col-sm-6 ordersTotalCount">
                    <?= $form->field($model, 'total_count')->textInput() ?>
                </div>
            </div>
            <div class="card-footer">
                <?= Html::submitButton('Save', ['class' => 'btn rounded-pill btn-secondary']) ?>
            </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
