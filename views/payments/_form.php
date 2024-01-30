<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Payments $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="payments-form">
    <div class="card card-primary">
        <?php $form = ActiveForm::begin(); ?>
            <div class="default-panel">
                <div class="panel-title premission">
                    <span class="non-active">Վճար</span>
                </div>
                <?php if ($model->id){ ?>
                    <div class="clientSelectSingle">
                        <label for="singleClients">Հաճախորդ</label>
                        <select id="singleClients" class="js-example-basic-single form-control" name="client_id">
                            <option  value=""></option>
                            <?php foreach ($clients as $client){
                                $isSelected = in_array($client['id'], $payment_clients);
                                ?>
                                <option <?= $isSelected ? 'selected' : '' ?> value="<?= $client['id'] ?>"><?= $client['name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                <?php } else {?>
                <div class="clientSelectSingle">
                    <label for="singleClients">Հաճախորդ</label>
                    <select id="singleClients" class="js-example-basic-single form-control" name="client_id">
                        <option  value=""></option>
                        <?php foreach ($client as $cl){ ?>
                            <option value="<?= $cl['id'] ?>"><?= $cl['name'] ?></option>
                        <?php } ?>
                    </select>
                </div>
                <?php } ?>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 discountType">
                    <?= $form->field($model, 'payment_sum')->input('number') ?>
                </div>
                <label class="rateLabel" for="rate">Փոխարժեք</label>
                <div id="rate" class="form-group col-md-12 col-lg-12 col-sm-12 rateDocument">
                    <div class="rateType">
                        <?= $form->field($model, 'rate_id')->dropDownList($rates,['options' => [1 => ['selected' => true]]])->label(false) ?>
                    </div>
                    <div class="rateValue">
                        <?= $form->field($model, 'rate_value')->input('number',['value' => 1,'required' => true,'readonly' => true])->label(false) ?>
                    </div>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName">
                    <?= $form->field($model, 'comment')->textArea(['maxlength' => true]) ?>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 discountType">
                    <?= $form->field($model, 'pay_date')->input('datetime-local') ?>
                </div>
            </div>
            <div class="card-footer">
                <?= Html::submitButton('Պահպանել', ['class' => 'btn rounded-pill btn-secondary']) ?>
            </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
