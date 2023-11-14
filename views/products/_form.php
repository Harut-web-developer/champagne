<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Products $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="products-form">
    <div class="card card-primary">
        <?php $form = ActiveForm::begin(); ?>
            <div class="card-body formDesign">
                <div class="form-group col-md-5 col-lg-3 col-sm-6 productsWarehouse">
                    <?= $form->field($model, 'warehouse_id')->dropDownList($warehouse) ?>
                </div>
                <div class="form-group col-md-5 col-lg-3 col-sm-6 productsNomenclature">
                    <?= $form->field($model, 'nomenclature_id')->dropDownList($nomenclature) ?>
                </div>
                <div class="form-group col-md-5 col-lg-3 col-sm-6 productsCount">
                    <?= $form->field($model, 'count')->textInput() ?>
                </div>
                <div class="form-group col-md-5 col-lg-3 col-sm-6 productsPrice">
                    <?= $form->field($model, 'price')->textInput() ?>
                </div>
            </div>
            <div class="card-footer">
                <?= Html::submitButton('Save', ['class' => 'btn rounded-pill btn-secondary']) ?>
            </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
