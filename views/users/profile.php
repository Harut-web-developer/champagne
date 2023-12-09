<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
$this->params['sub_page'] = $sub_page;
/** @var yii\web\View $this */
/** @var app\models\Roles $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="profile-form">
    <div class="card card-primary">
        <?php $form = ActiveForm::begin(); ?>
        <div class="default-panel">
            <div class="panel-title premission">
                <span class="non-active">Իմ պրոֆիլը</span>
            </div>
            <div class="form-group col-md-12 col-lg-12 col-sm-12 clientName">
                <?= $form->field($model, 'password')->input('password',['maxlength' => true]) ?>
            </div>
            <div class="form-group col-md-12 col-lg-12 col-sm-12 clientName">
                <?= $form->field($model, 'email')->input('email') ?>
            </div>
            <div class="form-group col-md-12 col-lg-12 col-sm-12 clientName">
                <?= $form->field($model, 'phone')->input('text') ?>
            </div>
        </div>
        <div class="card-footer">
            <?= Html::submitButton('Պահպանել', ['class' => 'btn rounded-pill btn-secondary']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>