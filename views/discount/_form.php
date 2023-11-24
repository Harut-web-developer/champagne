<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Discount $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="discount-form">
    <div class="card card-primary">
        <?php $form = ActiveForm::begin(); ?>
            <div class="default-panel">
                <div class="panel-title premission">
                    <span class="non-active">Discount</span>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 discountType">
                    <?= $form->field($model, 'type')->dropDownList([ 'percent' => 'Percent', 'money' => 'Money', ], ['prompt' => '']) ?>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 discount">
                    <?= $form->field($model, 'discount')->input('number') ?>
                </div>
                <?php if ($model->id){ ?>
                    <div class="form-group col-md-12 col-lg-12 col-sm-12 discount">
                        <label for="multipleClients">Clients</label>
                        <select id="multipleClients" class="js-example-basic-multiple form-control" name="clients[]" multiple="multiple">
                            <?php foreach ($clients as $client){
                                $isSelected = in_array($client['id'], $discount_clients_id);
                                ?>
                                <option <?= $isSelected ? 'selected' : '' ?> value="<?= $client['id'] ?>"><?= $client['name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group col-md-12 col-lg-12 col-sm-12 discount">
                        <label for="multipleProducts">Products</label>
                        <select id="multipleProducts" class="js-example-basic-multiple form-control" name="products[]" multiple="multiple">
                            <?php foreach ($products as $product){
                                $selected = in_array($product['id'], $discount_products_id);
                                ?>
                                <option <?= $selected ? 'selected' : '' ?> value="<?= $product['id'] ?>"><?= $product['name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <?php }else{ ?>
                    <div class="form-group col-md-12 col-lg-12 col-sm-12 discount">
                        <label for="multipleClients">Clients</label>
                        <select id="multipleClients" class="js-example-basic-multiple form-control" name="clients[]" multiple="multiple">
                            <?php foreach ($clients as $client){ ?>
                                <option value="<?=$client['id']?>"><?=$client['name']?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group col-md-12 col-lg-12 col-sm-12 discount">
                        <label for="multipleProducts">Products</label>
                        <select id="multipleProducts" class="js-example-basic-multiple form-control" name="products[]" multiple="multiple">
                            <?php foreach ($products as $product){ ?>
                                <option value="<?=$product['id']?>"><?=$product['name']?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <?php } ?>

                <div class="form-group col-md-12 col-lg-12 col-sm-12 discountStartDate">
                    <?= $form->field($model, 'start_date')->input('date') ?>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 discountEndDate">
                    <?= $form->field($model, 'end_date')->input('date') ?>
                </div>
            </div>
            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn rounded-pill btn-secondary']) ?>
            </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

