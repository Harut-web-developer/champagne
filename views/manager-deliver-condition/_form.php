<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\ManagerDeliverCondition $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="manager-deliver-condition-form">
    <div class="card card-primary">
        <?php $form = ActiveForm::begin(); ?>
            <div class="default-panel">
                <div class="panel-title premission">
                    <span class="non-active">Երթուղի</span>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName">
                    <?= $form->field($model, 'manager_id')->dropDownList($manager_id) ?>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName">
                    <?php if($model->id){?>
                        <div class="deliverSelectMultiple">
                            <label for="multipleDeliver">Առաքիչ</label>
                            <select id="multipleDeliver" class="js-example-basic-multiple form-control" name="deliver_id[]"  multiple="multiple">
                                <option  value=""></option>
                                <?php foreach ($deliver_id as $value){
                                    $isSelected = in_array($value['id'], $deliver_groups);
                                    ?>
                                    <option <?= $isSelected ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    <?php } else{?>
                        <div class="deliverSelectMultiple">
                            <label for="multipleDeliver">Առաքիչ</label>
                            <select id="multipleDeliver" class="js-example-basic-multiple form-control" name="deliver_id[]" multiple="multiple">
                                <option  value=""></option>
                                <?php foreach ($deliver_id as $value){ ?>
                                    <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    <?php }?>

                </div>
            </div>
            <div class="card-footer">
                <?= Html::submitButton('Պահպանել', ['class' => 'btn rounded-pill btn-secondary']) ?>
            </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
