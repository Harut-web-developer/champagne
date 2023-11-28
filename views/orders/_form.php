<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Orders $model */
/** @var yii\widgets\ActiveForm $form */

?>
<?php if ($model->id){ ?>
    <div class="orders-form">
        <div class="card card-primary">
            <?php $form = ActiveForm::begin(); ?>
            <div class="default-panel">
                <div class="panel-title premission">
                    <span class="non-active">Վաճառք</span>
                </div>
                <!--            <div class="card-body formDesign">-->
                <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName">
                    <?= $form->field($model, 'user_id')->dropDownList($users) ?>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName">
                    <?= $form->field($model, 'clients_id')->dropDownList($clients) ?>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersTotalPrice">
                    <?= $form->field($model, 'total_price')->textInput(['readonly'=> true]) ?>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersTotalCount">
                    <?= $form->field($model, 'total_count')->textInput(['readonly'=> true]) ?>
                </div>
            </div>
            <div class="default-panel">
                <div class="panel-title premission">
                    <span class="non-active">Վաճառքի ցուցակ</span>
                </div>
                <div class="card">
                    <div class="table-responsive text-nowrap">
                        <table class="table ordersAddingTable">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Անուն</th>
                                <th>Քանակ</th>
                                <th>Գին</th>
                                <th>Ինքնարժեք</th>
<!--                                <th>Discount</th>-->
<!--                                <th>Before discounting</th>-->
                                <th>Ընդհանուր գումար</th>
                                <th>Գործողություն</th>
                            </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                <?php
                                $itemsArray = [];
                                foreach($order_items as $keys => $item){
                                    $itemsArray[] = $item['product_id'];
                                    ?>
                                    <tr class="tableNomenclature">
                                        <td><?=$keys + 1?><input class="orderItemsId" type="hidden" name="order_items[]" value="<?=$item['id']?>"><input type="hidden" name="product_id[]" value="<?=$item['product_id']?>"></td>
                                        <td class="name"><?=$item['name']?></td>
                                        <td class="count"><input type="number" name="count_[]" value="<?=$item['count']?>" class="form-control countProductForUpdate"></td>
                                        <td class="price"><?=$item['price']?><input type="hidden" name="price[]" value="<?=$item['price']?>"></td>
                                        <td class="cost"><?=$item['cost']?><input type="hidden" name="cost[]" value="<?=$item['cost']?>"></td>
<!--                                        <td class="discount">--><?php //=$item['discount']?><!--<input type="hidden" name="discount[]" value="--><?php //=$item['discount']?><!--"></td>-->
<!--                                        <td class="priceBeforeDiscount">--><?php //=$item['price_before_discount']?><!--<input type="hidden" name="priceBeforeDiscount[]" value="--><?php //=$item['price_before_discount']?><!--"></td>-->
                                        <td class="total"><span><?=$item['count'] * $item['price']?></span><input type="hidden" name="total[]" value="<?=$item['count'] * $item['price']?>"></td>
                                        <td><button  type="button" class="btn rounded-pill btn-outline-danger deleteItemsFromDB">Ջնջել</button></td>
                                    </tr>
                               <?php }?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Button trigger modal -->
                <button type="button" class="btn rounded-pill btn-secondary addOrders" data-bs-toggle="modal" data-bs-target="#largeModal">Ավելացնել ապրանք</button>
                <!-- Modal -->
                <div class="modal fade" id="largeModal" tabindex="-1" style="display: none;" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel3">Ապրանքացուցակ</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="card">
                                    <div class="table-responsive text-nowrap">
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Ընտրել</th>
                                                <th>Անուն</th>
                                                <th>Քանակ</th>
                                            </tr>
                                            </thead>
                                            <tbody class="table-border-bottom-0">
                                            <?php
                                            foreach ($nomenclatures as $keys => $nomenclature){
                                                if(in_array($nomenclature['id'],$itemsArray)){
                                                    continue;
                                                }
                                                ?>
                                                <tr class="addOrdersTableTr">
                                                    <td><?=$keys + 1?></td>
                                                    <td>
                                                        <input data-id="<?=$nomenclature['id']?>" type="checkbox">
                                                        <input class="productIdInput" data-product="<?=$nomenclature['products_id']?>" type="hidden">
                                                    </td>
                                                    <td class="nomenclatureName"><?=$nomenclature['name']?></td>
                                                    <td class="ordersAddCount">
                                                        <input type="number" class="form-control ordersCountInput">
                                                        <input class="ordersPriceInput" type="hidden" value="<?=$nomenclature['price']?>">
                                                        <input class="ordersCostInput" type="hidden" value="<?=$nomenclature['cost']?>">
<!--                                                        <input class="ordersPriceBrforeDiscount" type="hidden" value="--><?php //=$nomenclature['price_before_discount']?><!--">-->
<!--                                                        <input class="ordersDiscountInput" type="hidden" value="--><?php //=$nomenclature['discount_id']?><!--">-->
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn rounded-pill btn-secondary update" data-bs-dismiss="modal">Ավելացնել ցուցակում</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="card-footer">
                <?= Html::submitButton('Պահպանել', ['class' => 'btn rounded-pill btn-secondary']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
<?php }else{ ?>
    <div class="orders-form">
        <div class="card card-primary">
            <?php $form = ActiveForm::begin(); ?>
            <div class="default-panel">
                <div class="panel-title premission">
                    <span class="non-active">Վաճառք</span>
                </div>
                <!--            <div class="card-body formDesign">-->
                <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName">
                    <?= $form->field($model, 'user_id')->dropDownList($users) ?>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName">
                    <?= $form->field($model, 'clients_id')->dropDownList($clients) ?>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersTotalPrice">
                    <?= $form->field($model, 'total_price')->textInput(['readonly'=> true]) ?>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersTotalCount">
                    <?= $form->field($model, 'total_count')->textInput(['readonly'=> true]) ?>
                </div>
            </div>
            <div class="default-panel">
                <div class="panel-title premission">
                    <span class="non-active">Վաճառքի ցուցակ</span>
                </div>
                <div class="card">
                    <div class="table-responsive text-nowrap">
                        <table class="table ordersAddingTable">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Անուն</th>
                                <th>Քանակ</th>
                                <th>Գին</th>
                                <th>Ինքնարժեք</th>
<!--                                <th>Discount</th>-->
<!--                                <th>Before discounting</th>-->
                                <th>Ընդհանուր գումար</th>
                                <th>Գործողություն</th>
                            </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">

                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Button trigger modal -->
                <button type="button" class="btn rounded-pill btn-secondary addOrders" data-bs-toggle="modal" data-bs-target="#largeModal">Ավելացնել ապրանք</button>
                <!-- Modal -->
                <div class="modal fade" id="largeModal" tabindex="-1" style="display: none;" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel3">Ապրանքացուցակ</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="card">
                                    <div class="table-responsive text-nowrap">
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Ընտրել</th>
                                                <th>Անուն</th>
                                                <th>Քանակ</th>
                                            </tr>
                                            </thead>
                                            <tbody class="table-border-bottom-0">
                                            <?php
                                            foreach ($nomenclatures as $keys => $nomenclature){
                                                ?>
                                                <tr class="addOrdersTableTr">
                                                    <td><?=$keys + 1?></td>
                                                    <td>
                                                        <input data-id="<?=$nomenclature['id']?>" type="checkbox">
                                                        <input class="productIdInput" data-product="<?=$nomenclature['products_id']?>" type="hidden">
                                                    </td>
                                                    <td class="nomenclatureName"><?=$nomenclature['name']?></td>
                                                    <td class="ordersAddCount">
                                                        <input type="number" class="form-control ordersCountInput">
                                                        <input class="ordersPriceInput" type="hidden" value="<?=$nomenclature['price']?>">
                                                        <input class="ordersCostInput" type="hidden" value="<?=$nomenclature['cost']?>">
<!--                                                        <input class="ordersPriceBrforeDiscount" type="hidden" value="--><?php //=$nomenclature['price_before_discount']?><!--">-->
<!--                                                        <input class="ordersDiscountInput" type="hidden" value="--><?php //=$nomenclature['discount_id']?><!--">-->
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn rounded-pill btn-secondary create" data-bs-dismiss="modal">Ավելացնել ցուցակում</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="card-footer">
                <?= Html::submitButton('Պահպանել', ['class' => 'btn rounded-pill btn-secondary']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
<?php } ?>

<?php
$this->registerJsFile(
    '@web/js/orders.js',
    ['depends' => [\yii\web\JqueryAsset::class]]
);
?>