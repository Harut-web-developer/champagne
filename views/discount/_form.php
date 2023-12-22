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
                    <span class="non-active">Զեղչ</span>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 discountType">
                    <?= $form->field($model, 'name')->input('text') ?>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 discountType">
                    <?= $form->field($model, 'discount_option')->dropDownList([ '1' => 'Մեկ անգամյա', '2' => 'Բազմակի', ], ['prompt' => '']) ?>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 discountType">
                        <?= $form->field($model, 'type')->dropDownList([ 'percent' => 'Տոկոսով', 'money' => 'Գումարով', ], ['prompt' => '']) ?>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 discount">
                    <?= $form->field($model, 'discount')->input('number') ?>
                </div>
                <?php if ($model->id){ ?>
                    <div class="form-group col-md-12 col-lg-12 col-sm-12 discount">
                        <label for="multipleClients">Հաճախորդ</label>
                        <select id="multipleClients" class="js-example-basic-multiple form-control" name="clients[]" multiple="multiple">
                            <?php foreach ($clients as $client){
                                $isSelected = in_array($client['id'], $discount_clients_id);
                                ?>
                                <option <?= $isSelected ? 'selected' : '' ?> value="<?= $client['id'] ?>"><?= $client['name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group col-md-12 col-lg-12 col-sm-12 discount">
                        <label for="multipleProducts">Ապրանք</label>
                        <select id="multipleProducts" class="js-example-basic-multiple form-control" name="products[]" multiple="multiple">
                            <?php foreach ($products as $product){
                                $selected = in_array($product['id'], $discount_products_id);
                                ?>
                                <option <?= $selected ? 'selected' : '' ?> value="<?= $product['id'] ?>"><?= $product['name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group col-md-12 col-lg-12 col-sm-12 discountEndDate">
                        <div class="price-range-block">
                            <div class="form-group col-md-12 col-lg-12 col-sm-12 discountType">
                                <?= $form->field($model, 'discount_filter_type')->dropDownList([ 'count' => 'Ըստ քանակի', 'price' => 'Ըստ գնի', ], ['prompt' => 'Ընտրել տեսակը'])?>
                            </div>
                            <div id="slider-range" class="price-filter-range" name="rangeInput"></div>
                            <div>
                                <input type="number" name="min" min=0 max="500000" value="<?=$min['min']?>" oninput="validity.valid||(value='0');" id="min_price" class="price-range-field min-value" />
                                <input type="number" name="max" min=0 max="1000000" value="<?=$max['max']?>" oninput="validity.valid||(value='1000000');" id="max_price" class="price-range-field max-value" />
                            </div>
                            <!--                        <div id="searchResults" class="search-results-block"></div>-->
                        </div>
                    </div>
                    <?php }else{ ?>
                        <div class="form-group selGroup">
                            <div class="clientSelect">
                                <label for="multipleClients">Հաճախորդ և խմբեր</label>
                                <select id="multipleClients" class="js-example-basic-multiple form-control" name="clients[]" multiple="multiple">
                                    <?php foreach ($clients as $client){ ?>
                                        <option value="<?=$client['id']?>"><?=$client['name']?></option>
                                    <?php } ?>
                                    <?php foreach ($discount_client_groups as $index => $client_groups ){ ?>
                                        <option value="<?= "groups['id'] = " . $client_groups['id'] ?>"><?= $client_groups['groups_name'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="productSelect">
                                <label for="multipleProducts">Ապրանք</label>
                                <select id="multipleProducts" class="js-example-basic-multiple form-control " name="products[]" multiple="multiple">
                                    <?php foreach ($products as $product){ ?>
                                        <option value="<?=$product['id']?>"><?=$product['name']?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-12 col-lg-12 col-sm-12 discountEndDate">
                            <div class="price-range-block">
                                <div class="form-group col-md-12 col-lg-12 col-sm-12 discountType">
                                    <?= $form->field($model, 'discount_filter_type')->dropDownList([ 'count' => 'Ըստ քանակի', 'price' => 'Ըստ գնի', ], ['prompt' => 'Ընտրել տեսակը'])?>
                                </div>
                                <div id="slider-range" class="price-filter-range" name="rangeInput"></div>
                                <div>
                                    <input type="number" name="min" min=0 max="500000" oninput="validity.valid||(value='0');" id="min_price" class="price-range-field min-value" />
                                    <input type="number" name="max" min=0 max="1000000" oninput="validity.valid||(value='1000000');" id="max_price" class="price-range-field max-value" />
                                </div>
                                <!--                        <div id="searchResults" class="search-results-block"></div>-->
                            </div>
                        </div>

                    <?php } ?>

                <div class="form-group col-md-12 col-lg-12 col-sm-12 discountStartDate">
                    <?= $form->field($model, 'start_date')->input('date',['value' => date('Y-m-d')]) ?>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 discountEndDate">
                    <?= $form->field($model, 'end_date')->input('date') ?>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 discountType">
                    <?= $form->field($model, 'comment')->textarea(['rows' => '4']) ?>
                </div>

                <div class="form-group col-md-12 col-lg-12 col-sm-12 discountCheck">
                    <?= $form->field($model, 'discount_check')->checkbox(['label' => 'Կիրառել մյուս զեղչերի հետ'],false) ?>
                </div>

            </div>
            <div class="card-footer">
                <?= Html::submitButton('Պահպանել', ['class' => 'btn rounded-pill btn-secondary']) ?>
            </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>



