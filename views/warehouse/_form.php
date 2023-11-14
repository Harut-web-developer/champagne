<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Warehouse $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="warehouse-form">
    <div class="card card-primary">
    <?php $form = ActiveForm::begin(); ?>
        <div class="card-body formDesign">
            <div class="form-group col-md-5 col-lg-3 col-sm-6 warehouseName">
                <?= $form->field($model, 'name')->textInput() ?>
            </div>
            <div class="form-group col-md-5 col-lg-3 col-sm-6 warehouseType">
                <?= $form->field($model, 'type')->dropDownList([ 'usual' => 'Usual', 'virtual' => 'Virtual', ], ['prompt' => 'choose type']) ?>
            </div>
        </div>
        <div class="card-footer">
            <?= Html::submitButton('Save', ['class' => 'btn rounded-pill btn-secondary']) ?>
        </div>

    <?php ActiveForm::end(); ?>
    </div>
</div>
