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
            <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName">
                <?php if(isset($model->id)){?>
                    <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName">
                        <label for="managerSelect">Մենեջեր</label>
                        <select id="managerSelect" class="form-select form-control" aria-label="Default select example" name="manager_id" required>
                            <option value="">Ընտրել մենեջերին</option>
                            <?php foreach ($manager_id as $key => $manager ){
                                $isSelected = $manager['id'] == $update_value[0]['manager_id'] ? true : false;
                                ?>
                                <option <?= $isSelected ? 'selected' : '' ?> value="<?= $manager['id'] ?>"><?= $manager['name']?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="deliverSelectMultiple">
                        <label for="multipleDeliver">Առաքիչ</label>
                        <select id="multipleDeliver" class="js-example-basic-multiple form-control" name="deliver_id[]"  multiple="multiple" required>
                            <option  value=""></option>
                            <?php foreach ($deliver_id as $key => $value): ?>
                                <?php
                                $isSelected = false; // Initialize $isSelected
                                foreach ($update_value as $uskey => $upvalue) {
                                    $deliverIds = is_array($upvalue['deliver_id']) ? $upvalue['deliver_id'] : [$upvalue['deliver_id']];
                                    $isSelected = in_array($value['id'], $deliverIds);
                                    if ($isSelected) {
                                        break;
                                    }
                                }
                                ?>
                                <option <?= $isSelected ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="routeSelectMultiple">
                        <label for="multipleRoute">Երթուղի</label>
                        <select id="multipleRoute" class="form-select form-control" aria-label="Default select example" name="route_id" required>
                            <option  value="">Ընտրել երթուղին</option>
                            <?php foreach ($route as $route_val){
                                $isSelected = $route_val['id'] == $update_value[0]['route_id'] ? true : false;
                                ?>
                                <option <?= $isSelected ? 'selected' : '' ?> value="<?= $route_val['id'] ?>"><?= $route_val['route'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                <?php } else{?>
                    <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName">
                        <label for="managerSelect">Մենեջեր</label>
                        <select id="managerSelect" class="form-select form-control" aria-label="Default select example" name="manager_id" required>
                            <option value="">Ընտրել մենեջերին</option>
                            <?php foreach ($manager_id as $manager ){ ?>
                                <option value="<?= $manager['id'] ?>"><?= $manager['name']?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="deliverSelectMultiple">
                        <label for="multipleDeliver">Առաքիչ</label>
                        <select id="multipleDeliver" class="js-example-basic-multiple form-control" name="deliver_id[]" multiple="multiple" required>
                            <option  value=""></option>
                            <?php foreach ($deliver_id as $value){ ?>
                                <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="routeSelectMultiple">
                        <label for="multipleRoute">Երթուղի</label>
                        <select id="multipleRoute" class="form-select form-control" aria-label="Default select example" name="route_id" required>
                            <option  value="">Ընտրել երթուղին</option>
                            <?php foreach ($route as $route_val){ ?>
                                <option value="<?= $route_val['id'] ?>"><?= $route_val['route'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                <?php }?>

            </div>
        </div>
        <div class="card-footer">
            <?= Html::submitButton('Պահպանել', ['class' => 'btn rounded-pill btn-secondary submit_save']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

