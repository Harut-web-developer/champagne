<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\widgets\CustomLinkPager;
use app\models\Orders;
use app\models\Users;

/** @var yii\web\View $this */
/** @var app\models\Orders $model */
/** @var yii\widgets\ActiveForm $form */
$session = Yii::$app->session;
?>
<?php if ($model->id){ ?>
    <?php if (Users::checkPremission(56)){ ?>
        <img style="height: 20px; float: right;"  class="downloadUpdateXLSX" onclick="window.open('/orders/reports?type=1&id=<?= $model->id ?>//','newwindow','width:500, height:700')" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAAACXBIWXMAAAsTAAALEwEAmpwYAAAC+klEQVR4nO2ZzWsTURDAFwW/kYre9CKKiB4ERT2of4AUveXqF1htZtaQ7rw0NDN9ehMUsTfxIggepPSioOJRz3rQg3gRqYI91I/Wg1rUykvzkk2atNl2d/uCHRjIMpnZ+c2b93Y28bxlWZZlmVf8gt4GJMNAMolKpheoU6DkJarBjJd68oo/LyLx2UqsUwMAkuFYk68oKLmcFsBkXAkvCQTGVPFyMRT3z24nudoxAEsCgTEDpA6BCQCkCoEJAaQGgQkCpAKRNICRZkdsbA+7NABaQsQxdsQFYEaSyBDEL5wBAMWPokIA8S9nAJJqvXnlvwEAxbetj9Z6BSh5V2+Xi9YOhcGzzgGgkimfSjuane9AMpbP59dW4FY1wqEjAKbKd6xfT5/egsQ/yjaSYggs52QL4Yz+xj69u+pLfBcUT+RyustcZ7XeYFbDZYBpVHLP+mb7S0eR5GYISJzdxFjtd/7jB3qP3czZAu81n6FY3Iwk35wHwBm93xgHSK4vJJa3JADE333fX10HoPhNJwFcmRWH+NScrafkJygeygZyOAiC9YtOfOEA/MmcNmXfoLQd+3mf3Q9I8qqF30f7vdglevUHz4cHMyC5Ya/9QE40qzwmlXxUAFD8OpPJrDR+ZhWQZNz8rtRbLG6qxiN53uAzlFjyUQEwkOM1P86HbAN1z4awT2HgUN39Auk2LQXEH8Lxkgcgfmp9zLyDSkarVSYZO631GmsHxQ+sLVvZL1WbSbwWdzQ1AF/xuUuBHDDa7EUdiEvWjsRnWh2T6MQxGkG9DgZ4DIpPdiLAX6AStHs/zz0Avhblfp5TAOUZSW9MFQAUT8QHIA/DsXM53QXEz0xbtTOmI/GTHq3XRQMgGYkLAJTcqqs21Y7TtjWQ7mgAfXqXGQliASAZCcfOFvVO01YRVnDcvGt7UeVCfmCreUlZbDuZfzvNU7puFZQcawuC5Asovd9zUZDkyFx/JoLir72BHPRcFmwB0RHJexVphOio5K1Uxu33qPhtOz3/DyrGtgq43BHiAAAAAElFTkSuQmCC">
    <?php } ?>
    <div class="orders-form">
        <div class="card card-primary">
            <?php $form = ActiveForm::begin(); ?>
            <div class="default-panel">
                <div class="panel-title premission">
                    <span class="non-active">Վաճառք</span>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName">
                    <?php if ($session['role_id'] == 1){ ?>
                        <?= $form->field($model, 'user_id')->dropDownList($users) ?>
                    <?php }elseif($session['role_id'] == 2){ ?>
                        <?= $form->field($model, 'user_id')->hiddenInput(['value' => $session['user_id']])->label(false) ?>
                    <?php }elseif ($session['role_id'] == 3 || $session['role_id'] == 4){
                        $manager_id = Orders::findOne($model->id);
                        ?>
                        <?= $form->field($model, 'user_id')->hiddenInput(['value' => $manager_id->user_id])->label(false) ?>
                    <?php } ?>
                </div>
                <div class="clientSelectSingle">
                    <?php if ($session['role_id'] == 2 || $session['role_id'] == 3 || $session['role_id'] == 4){ ?>
                        <label class="label_clients" for="singleClients">Հաճախորդ</label>
                        <select id="singleClients" class="js-example-basic-single form-control" name="clients_id">
                            <option  value=""></option>
                            <?php foreach ($clients as $client){
                                $isSelected = in_array($client['id'], $orders_clients);
                                if ($isSelected){ ?>
                                    <option <?= $isSelected ? 'selected' : '' ?> value="<?= $client['id'] ?>"><?= $client['name'] ?></option>
                            <?php } }?>
                        </select>
                    <?php }elseif ($session['role_id'] == 1 ) {?>
                        <div class="clients_ajax_content">
                            <label class="label_clients" for="singleClients">Հաճախորդ</label>
                            <select id="singleClients" class="js-example-basic-single form-control" name="clients_id">
                                <option  value=""></option>
                                <?php foreach ($clients as $client){
                                    $isSelected = in_array($client['id'], $orders_clients);
                                    ?>
                                    <option <?= $isSelected ? 'selected' : '' ?> value="<?= $client['id'] ?>"><?= $client['name'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    <?php } ?>
                </div>
                <div class="warhouse_ajax_content">
                    <input <?= $warehouse_value_update["warehouse_id"] ? 'value="' . $warehouse_value_update['warehouse_id'] . '"' : 'value=""' ?> type="hidden" name="warehouse" class="warehouse_id">
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersTotalCount">
                    <?= $form->field($model, 'comment')->textarea() ?>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName">
                    <?php if ($session['role_id'] == 4){ ?>
                        <?= $form->field($model, 'orders_date')->input('datetime-local',['readonly' => true]) ?>
                    <?php } else {?>
                        <?= $form->field($model, 'orders_date')->input('datetime-local') ?>
                    <?php } ?>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName">
                    <?= $form->field($model, 'is_exist_company')->checkbox(['label' => 'Չտպել'],false) ?>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName companies">
                    <?= $form->field($model, 'company_id')->dropDownList(['' => 'Ընտրել Ընկերությունը'] + $companies) ?>
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
                                <th>Զեղչ</th>
                                <th>Գինը մինչև զեղչելը</th>
                                <th>Զեղչված գին</th>
                                <th>Ընդհանուր գումար</th>
                                <th>Ընդհանուր զեղչված գումար</th>
                                <?php if ($session['role_id'] != 4){ ?>
                                    <th>Գործողություն</th>
                                <?php } ?>

                            </tr>
                            </thead>
                            <tbody class="table-border-bottom-0 old_tbody">
                                <?php
                                $itemsArray = [];
                                foreach($order_items as $keys => $item){
                                    $itemsArray[] = $item['product_id'];
                                    ?>
                                    <tr class="tableNomenclature fromDB">
                                        <td>
                                            <span class="acordingNumber"><?=$keys + 1?></span>
                                            <input class="orderItemsId" type="hidden" name="order_items[]" value="<?=$item['id']?>">
                                            <input class="stringCount" type="hidden" name="string_count[]" value="<?=$item['string_count']?>">
                                            <input type="hidden" name="string_price[]" value="<?=$item['string_price']?>">
                                            <input type="hidden" name="string_before_price[]" value="<?=$item['string_before_price']?>">
                                            <input class="count_balance" type="hidden" name="count_balance[]" value="<?=$item['count_balance']?>">
                                            <input class="prodId" type="hidden" name="product_id[]" value="<?=$item['product_id']?>">
                                            <input class="nomId"  type="hidden" name="nom_id[]" value="<?=$item['nom_id']?>">
                                            <input class="cost" type="hidden" name="cost[]" value="<?=$item['cost']?>">
                                            <input type="hidden" name="aah[]" value="<?=$item['AAH']?>">
                                            <input class="countDiscountId" type="hidden" name="count_discount_id[]" value="<?=$item['count_discount_id']?>">
                                        </td>
                                        <td class="name"><?=$item['name']?></td>
                                        <td class="count">
                                            <input type="number" readonly name="count_[]" value="<?=$item['count_by']?>" class="form-control countProductForUpdate">
                                        </td>
                                        <td class="discount">
                                            <span><?= $item['string_discount']?></span>
                                            <input type="hidden" name="discount[]" value="<?=$item['string_discount']?>">
                                        </td>
                                        <?php
                                        $count = explode(',',$item['string_count']);
                                        $price = explode(',',$item['string_price']);
                                        $before_price = explode(',',$item['string_before_price']);
                                        $sum_price = 0;
                                        $sum_before_price = 0;
                                        for ($k = 0; $k < count($count); $k++){
                                            $sum_price += intval($count[$k]) * floatval($price[$k]);
                                            $sum_before_price += intval($count[$k]) * floatval($before_price[$k]);
                                        }
                                        ?>
                                        <td class="beforePrice"><span><?=number_format($before_price[count($before_price) - 1],2,'.','')?></span>
                                            <input type="hidden" name="beforePrice[]" value="<?=number_format($before_price[count($before_price) - 1],2,'.','')?>">
                                        </td>
                                        <td class="price"><span><?=number_format($price[count($price) - 1],2,'.','')?></span>
                                            <input type="hidden" name="price[]" value="<?=number_format($price[count($price) - 1],2,'.','')?>">
                                        </td>
                                        <td class="totalBeforePrice">
                                            <span><?=number_format($sum_before_price,2,'.','')?></span>
                                            <input type="hidden" name="total_before_price[]" value="<?=number_format($sum_before_price,2,'.','')?>">
                                        </td>
                                        <td class="totalPrice">
                                            <span><?=number_format($sum_price,2,'.','')?></span>
                                            <input type="hidden" name="total_price[]" value="<?=number_format($sum_price,2,'.','')?>">
                                        </td>
                                        <td class="ordersButtons">
                                        <?php if ($oldattributes['is_exit'] == 0){
                                            if ($session['role_id'] != 4){?>
                                                <button type="button" data-orders="<?=$item['id']?>" class="btn rounded-pill btn-outline-info changeCount" data-bs-toggle="modal" data-bs-target="#modalCenter">
                                                    Փոփոխել
                                                </button>
                                            <?php }} ?>
                                            <div class="modal fade" id="modalCenter" tabindex="-1" style="display: none;" aria-modal="true" role="dialog">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
<!--                                                            <h5 class="modal-title" id="modalCenterTitle">Modal title</h5>-->
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
<!--                                                        <form action="/orders/refuse" method="post">-->
                                                            <div class="modal-body changeModalBody">

                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                                                    Փակել
                                                                </button>
                                                                <button type="button" class="btn btn-primary addChange" data-bs-dismiss="modal">Պահպանել</button>
                                                            </div>
<!--                                                        </form>-->
                                                    </div>
                                                </div>
                                            </div>
                                            <?php if ($session['role_id'] != 4){ ?>
                                                <button  type="button" class="btn rounded-pill btn-outline-danger deleteItemsFromDB">Ջնջել</button>
                                            <?php } ?>

                                        </td>
                                    </tr>
                               <?php }?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Button trigger modal -->
                <?php
                if ($model->is_exit == 1){
                    if ($session['role_id'] != 4){?>
                        <button type="button" class="btn rounded-pill btn-secondary addOrders addOrders_get_warh_id_update" data-bs-toggle="modal" data-bs-target="#largeModal">Ավելացնել ապրանք</button>
                    <?php } }?>
                <!-- Modal -->
                <div class="modal fade" id="largeModal" tabindex="-1" style="display: none;" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel3">Ապրանքացուցակ</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <input class="form-control col-md-3 mb-3 searchForOrderUpdate" type="search" placeholder="Որոնել...">
                                <div id="ajax_content">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn rounded-pill btn-secondary update" data-bs-dismiss="modal">Ավելացնել ցուցակում</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="default-panel">
                <div class="panel-title premission">
                    <span class="non-active">Կիրառված զեղչեր</span>
                </div>
                <div class="card">
                    <div class="table-responsive text-nowrap">
<!--                        <div class="loader d-none">-->
<!--                            <img src="/upload/loader.gif" >-->
<!--                        </div>-->
                        <table class="table discountDesc">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Զեղչի անուն</th>
                                <th>Զեղչի չափ</th>
                            </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                            <?php
                            $n = 0;
                                foreach ($active_discount as $k => $item){
                                    if (!in_array($item['id'],$numericValuesOnly)){
                                        continue;
                                    }else{

                                        $n++;
                                        ?>
                                        <tr>
                                            <td><?=$n?></td>
                                            <td><?=$item['name']?></td>
                                            <td>
                                            <?php
                                                if ($item['type'] == 'percent'){
                                                        echo $item['discount'].' %';
                                                }else{
                                                    echo $item['discount'].' դր․';
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="default-panel">
                <div class="panel-title premission">
                    <span class="non-active">Վաճառք</span>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersTotalCount">
                    <?= $form->field($model, 'total_price_before_discount')->textInput(['readonly'=> true,'value' => number_format($model->total_price_before_discount, 2, '.', ''),]) ?>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersTotalPrice">
                    <?= $form->field($model, 'total_price')->textInput(['readonly'=> true,'value' => number_format($model->total_price, 2, '.', ''),]) ?>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersTotalCount">
                    <?= $form->field($model, 'total_discount')->textInput(['readonly'=> true,'value' => number_format($model->total_discount, 2, '.', ''),]) ?>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersTotalCount">
                    <?= $form->field($model, 'total_count')->textInput(['readonly'=> true]) ?>
                </div>
            </div>
            <div class="card-footer">
                <?php
                if ($model->is_exit == 1){
                    if ($session['role_id'] != 4){?>
                        <?= Html::submitButton('Պահպանել', ['class' => 'btn rounded-pill btn-secondary submit_save']) ?>
                <?php } }?>
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
                <div class="clientSelectSingle">
                    <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName">
                        <?php if ($session['role_id'] == 2){ ?>
                            <?= $form->field($model, 'user_id')->hiddenInput(['value' => $session['user_id']])->label(false) ?>
                        <?php }else{
                            $users = [null => ''] + $users; ?>
                            <?= $form->field($model, 'user_id')->dropDownList($users) ?>
                        <?php } ?>
                    </div>
                    <?php if ($session['role_id'] == 2){ ?>
                        <label class="label_clients" for="singleClients">Հաճախորդ</label>
                        <select id="singleClients" class="js-example-basic-single form-control" name="clients_id">
                            <option  value=""></option>
                            <?php if (isset($clients)){
                                foreach ($clients as $client){
                                    ?>
                                    <option  value="<?= $client['id'] ?>"><?= $client['name'] ?></option>
                                <?php } }?>
                        </select>
                    <?php } elseif ($session['role_id'] == 1) {?>
                        <div class="clients_ajax_content">
                            <label class="label_clients" for="singleClients">Հաճախորդ</label>
                            <select id="singleClients" class="js-example-basic-single form-control" name="clients_id">

                            </select>
                        </div>
                    <?php } ?>
                </div>
                <div class="warhouse_ajax_content">

                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersTotalCount">
                    <?= $form->field($model, 'comment')->textarea() ?>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName">
                    <?= $form->field($model, 'orders_date')->input('datetime-local') ?>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName">
                    <?= $form->field($model, 'is_exist_company')->checkbox(['label' => 'Չտպել'],false) ?>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName companies">
                    <?= $form->field($model, 'company_id')->dropDownList(['' => 'Ընտրել Ընկերությունը'] + $companies) ?>
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
                                <th>Զեղչ</th>
                                <th>Գինը մինչև զեղչելը</th>
                                <th>Զեղչված գին</th>
                                <th>Ընդհանուր գումար</th>
                                <th>Ընդհանուր զեղչված գումար</th>
                                <th>Գործողություն</th>
                            </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">

                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Button trigger modal -->
                <button type="button" class="btn rounded-pill btn-secondary addOrders addOrders_get_warh_id" disabled  data-bs-toggle="modal" data-bs-target="#largeModal">Ավելացնել ապրանք</button>
                <!-- Modal -->
                <div class="modal fade" id="largeModal" tabindex="-1" style="display: none;" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel3">Ապրանքացուցակ</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <input class="form-control col-md-3 mb-3 searchForOrder" type="search" placeholder="Որոնել...">
                                <div id="ajax_content">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn rounded-pill btn-secondary create" data-bs-dismiss="modal">Ավելացնել ցուցակում</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="default-panel">
                <div class="panel-title premission">
                    <span class="non-active">Կիրառված զեղչեր</span>
                </div>
                <div class="card">
                    <div class="table-responsive text-nowrap">
                        <table class="table discountDesc">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Զեղչի անուն</th>
                                <th>Զեղչի չափ</th>
                            </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="default-panel">
                <div class="panel-title premission">
                    <span class="non-active">Վաճառք</span>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersTotalCount">
                    <?= $form->field($model, 'total_price_before_discount')->textInput(['readonly'=> true]) ?>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersTotalPrice">
                    <?= $form->field($model, 'total_price')->textInput(['readonly'=> true]) ?>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersTotalCount">
                    <?= $form->field($model, 'total_discount')->textInput(['readonly'=> true]) ?>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersTotalCount">
                    <?= $form->field($model, 'total_count')->textInput(['readonly'=> true]) ?>
                </div>
            </div>
            <div class="card-footer">
                <?= Html::submitButton('Պահպանել', ['class' => 'btn rounded-pill btn-secondary submit_save', 'disabled' => true]) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
<?php } ?>

<?php
$this->registerJsFile(
    '@web/js/orders.js?v=15992',
    ['depends' => [\yii\web\JqueryAsset::class]]
);
?>

<style>
    /* Vendor prefixes for cross-browser support */
    input[type="search"]::-webkit-search-cancel-button,
    input[type="search"]::-webkit-search-clear-button {
        -webkit-appearance: none;
        appearance: none;
        display: none;
    }
</style>
