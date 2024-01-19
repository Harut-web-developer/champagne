<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\widgets\CustomLinkPager;

/** @var yii\web\View $this */
/** @var app\models\Orders $model */
/** @var yii\widgets\ActiveForm $form */
$session = Yii::$app->session;

?>
<?php if ($model->id){ ?>
    <img style="height: 20px; float: right;"  class="downloadUpdateXLSX" onclick="window.open('/orders/reports?type=1&id=<?= $model->id ?>//','newwindow','width:500, height:700')" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAAACXBIWXMAAAsTAAALEwEAmpwYAAAC+klEQVR4nO2ZzWsTURDAFwW/kYre9CKKiB4ERT2of4AUveXqF1htZtaQ7rw0NDN9ehMUsTfxIggepPSioOJRz3rQg3gRqYI91I/Wg1rUykvzkk2atNl2d/uCHRjIMpnZ+c2b93Y28bxlWZZlmVf8gt4GJMNAMolKpheoU6DkJarBjJd68oo/LyLx2UqsUwMAkuFYk68oKLmcFsBkXAkvCQTGVPFyMRT3z24nudoxAEsCgTEDpA6BCQCkCoEJAaQGgQkCpAKRNICRZkdsbA+7NABaQsQxdsQFYEaSyBDEL5wBAMWPokIA8S9nAJJqvXnlvwEAxbetj9Z6BSh5V2+Xi9YOhcGzzgGgkimfSjuane9AMpbP59dW4FY1wqEjAKbKd6xfT5/egsQ/yjaSYggs52QL4Yz+xj69u+pLfBcUT+RyustcZ7XeYFbDZYBpVHLP+mb7S0eR5GYISJzdxFjtd/7jB3qP3czZAu81n6FY3Iwk35wHwBm93xgHSK4vJJa3JADE333fX10HoPhNJwFcmRWH+NScrafkJygeygZyOAiC9YtOfOEA/MmcNmXfoLQd+3mf3Q9I8qqF30f7vdglevUHz4cHMyC5Ya/9QE40qzwmlXxUAFD8OpPJrDR+ZhWQZNz8rtRbLG6qxiN53uAzlFjyUQEwkOM1P86HbAN1z4awT2HgUN39Auk2LQXEH8Lxkgcgfmp9zLyDSkarVSYZO631GmsHxQ+sLVvZL1WbSbwWdzQ1AF/xuUuBHDDa7EUdiEvWjsRnWh2T6MQxGkG9DgZ4DIpPdiLAX6AStHs/zz0Avhblfp5TAOUZSW9MFQAUT8QHIA/DsXM53QXEz0xbtTOmI/GTHq3XRQMgGYkLAJTcqqs21Y7TtjWQ7mgAfXqXGQliASAZCcfOFvVO01YRVnDcvGt7UeVCfmCreUlZbDuZfzvNU7puFZQcawuC5Asovd9zUZDkyFx/JoLir72BHPRcFmwB0RHJexVphOio5K1Uxu33qPhtOz3/DyrGtgq43BHiAAAAAElFTkSuQmCC">
    <div class="orders-form">
        <div class="card card-primary">
            <?php $form = ActiveForm::begin(); ?>
            <div class="default-panel">
                <div class="panel-title premission">
                    <span class="non-active">Վաճառք</span>
                </div>
                <!--            <div class="card-body formDesign">-->
                <div class="clientSelectSingle">
                    <label for="singleClients">Հաճախորդ</label>
                    <select id="singleClients" class="js-example-basic-single form-control" name="clients_id">
                        <option  value=""></option>
                        <?php foreach ($clients as $client){
                            $isSelected = in_array($client['id'], $orders_clients);
                            ?>
                            <option <?= $isSelected ? 'selected' : '' ?> value="<?= $client['id'] ?>"><?= $client['name'] ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName">
                    <?php
                    if ($session['role_id'] == 1){
                    ?>
                        <?= $form->field($model, 'user_id')->dropDownList($users) ?>
                    <?php
                    }elseif($session['role_id'] == 2){
                    ?>
                        <?= $form->field($model, 'user_id')->hiddenInput(['value' => $session['user_id']])->label(false) ?>
                    <?php
                    }
                    ?>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersTotalCount">
                    <?= $form->field($model, 'comment')->textarea() ?>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName">
                    <?= $form->field($model, 'orders_date')->input('datetime-local') ?>
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
                                <?php
                                $itemsArray = [];
                                foreach($order_items as $keys => $item){
                                    $itemsArray[] = $item['product_id'];
                                    ?>
                                    <tr class="tableNomenclature fromDB">
                                        <td>
                                            <span class="acordingNumber"><?=$keys + 1?></span>
                                            <input class="orderItemsId" type="hidden" name="order_items[]" value="<?=$item['id']?>">
                                            <input class="prodId" type="hidden" name="product_id[]" value="<?=$item['product_id']?>">
                                            <input class="nomId"  type="hidden" name="nom_id[]" value="<?=$item['nom_id']?>">
                                            <input class="cost" type="hidden" name="cost[]" value="<?=$item['cost']?>">
                                            <input class="countDiscountId" type="hidden" name="count_discount_id[]" value="<?=$item['count_discount_id']?>">
                                        </td>
                                        <td class="name"><?=$item['name']?></td>
                                        <td class="count">
                                            <input type="number" name="count_[]" value="<?=$item['count']?>" class="form-control countProductForUpdate">
                                        </td>
                                        <td class="discount">
                                            <?php
                                            if ($item['discount'] == 0){
                                            ?>
                                                <span>0</span>
                                                <input type="hidden" name="discount[]" value="0">
                                            <?php
                                            }else{
                                            ?>
                                                <span><?=$item['discount'] / $item['count']?></span>
                                                <input type="hidden" name="discount[]" value="<?=$item['discount'] / $item['count']?>">
                                            <?php
                                            }
                                            ?>
                                        </td>
                                        <td class="beforePrice"><span><?=$item['beforePrice']?></span>
                                            <input type="hidden" name="beforePrice[]" value="<?=$item['beforePrice']?>">
                                        </td>
                                        <td class="price"><span><?=$item['price']?></span>
                                            <input type="hidden" name="price[]" value="<?=$item['price']?>">
                                        </td>
                                        <td class="totalBeforePrice">
                                            <span><?=$item['totalBeforePrice']?></span>
                                            <input type="hidden" name="total_before_price[]" value="<?=$item['totalBeforePrice']?>">
                                        </td>
                                        <td class="totalPrice">
                                            <span><?=$item['total_price']?></span>
                                            <input type="hidden" name="total_price[]" value="<?=$item['total_price']?>">
                                        </td>
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
                            <div class="modal-body" id="ajax_content">
                                <input class="form-control col-md-3 mb-3 searchForOrder" type="search" placeholder="Որոնել...">
                                <div class="card">
                                    <div class="table-responsive text-nowrap">
                                        <table class="table resultSearch">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Նկար</th>
                                                <th>Անուն</th>
                                                <th>Քանակ</th>
                                            </tr>
                                            </thead>
                                            <tbody class="table-border-bottom-0 tbody_">
                                            <?php
//                                            var_dump($nomenclatures);

                                            foreach ($nomenclatures as $keys => $nomenclature){
                                                if(in_array($nomenclature['id'],$itemsArray)){
                                                    continue;
                                                }
                                                ?>
                                                <tr class="addOrdersTableTr">
                                                    <td>
                                                        <span><?=$keys + 1?></span>
                                                        <input class="prodId" data-id="<?=$nomenclature['id']?>" type="hidden">
                                                        <input class="nomId" data-product="<?=$nomenclature['nom_id']?>" type="hidden">
                                                    </td>
                                                    <td class="imageNom"><img src="/upload/<?=$nomenclature['image']?>"></td>
                                                    <td class="nomenclatureName"><?=$nomenclature['name']?></td>
                                                    <td class="ordersAddCount">
                                                        <input type="number" class="form-control ordersCountInput">
                                                        <input class="ordersPriceInput" type="hidden" value="<?=$nomenclature['price']?>">
                                                        <input class="ordersCostInput" type="hidden" value="<?=$nomenclature['cost']?>">
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <?php $page = $_GET['paging'] ?? 1;?>
                                <?php  $count = intval(ceil($total/10)) ; ?>
                                <nav aria-label="Page navigation example" class="pagination">
                                    <ul class="pagination pagination-sm">
                                        <li class="page-item prev <?= ($page <= 1) ? 'disabled' : '' ?>">
                                            <a class="page-link by_ajax" href="#" data-href="/orders/get-nomiclature?paging=<?= $page-1 ?>"><i class="tf-icon bx bx-chevrons-left"></i></a>
                                        </li>
                                        <?php for ($i = 1;$i <= $count; $i++){ ?>
                                            <?php if($i > 0 && $i <= $count+1){ ?>
                                                <li class="page-item <?= ($page==$i) ? 'active' : '' ?>">
                                                    <a class="page-link by_ajax" href="#" data-href="/orders/get-nomiclature?paging=<?= $i ?>"><?= $i ?>
                                                    </a>
                                                </li>
                                            <?php } ?>
                                        <?php } ?>
<!--                                        /orders/update?id=--><?php //=$model->id?>
                                        <?php if(intval($page) < $count){ ?>
                                            <li class="page-item next">
                                                <a class="page-link by_ajax" href="#" data-href="/orders/get-nomiclature?paging=<?= $page+1 ?>"><i class="tf-icon bx bx-chevrons-right"></i></a>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </nav>
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
                        <div class="loader d-none">
                            <img src="/upload/loader.gif" >
                        </div>
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
//                            foreach ($numericValuesOnly as $key => $value){
                                foreach ($active_discount as $k => $item){
                                    if (!in_array($item['id'],$numericValuesOnly)){
                                        continue;
                                    }else{

                                        $n++;
                                        ?>
                                        <tr>
                                            <td><?=$n?></td>
                                            <td><?=$item['name']?></td>
                                            <td><?=$item['discount']?></td>
                                        </tr>
                                        <?php
                                    }

//                                    if ($item['id'] == $value){

//                                    }
                                }
//                            }
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

                <div class="clientSelectSingle">
                    <label for="singleClients">Հաճախորդ</label>
                    <select id="singleClients" class="js-example-basic-single form-control" name="clients_id">
                        <option  value=""></option>
                        <?php foreach ($clients as $client){
                            ?>
                            <option  value="<?= $client['id'] ?>"><?= $client['name'] ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName">
                    <?php
                    if ($session['role_id'] == 1){
                        ?>
                        <?= $form->field($model, 'user_id')->dropDownList($users) ?>
                        <?php
                    }elseif($session['role_id'] == 2){
                        ?>
                        <?= $form->field($model, 'user_id')->hiddenInput(['value' => $session['user_id']])->label(false) ?>
                        <?php
                    }
                    ?>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersTotalCount">
                    <?= $form->field($model, 'comment')->textarea() ?>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName">
                    <?= $form->field($model, 'orders_date')->input('datetime-local') ?>
                </div>
            </div>
            <div class="default-panel">
                <div class="panel-title premission">
                    <span class="non-active">Վաճառքի ցուցակ</span>
                </div>
                <div class="card">
                    <div class="table-responsive text-nowrap">
                        <div class="loader d-none">
                            <img src="/upload/loader.gif" >
                        </div>
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
                                <input class="form-control col-md-3 mb-3 searchForOrder" type="search" placeholder="Որոնել...">
                                <div id="ajax_content">
                                    <div class="card">
                                        <div class="table-responsive text-nowrap">
                                            <table class="table">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Նկար</th>
                                                    <th>Անուն</th>
                                                    <th>Քանակ</th>
                                                </tr>
                                                </thead>
                                                <tbody class="table-border-bottom-0 tbody_">
                                                <?php
                                                foreach ($nomenclatures as $keys => $nomenclature){
                                                    ?>
                                                    <tr class="addOrdersTableTr">
                                                        <td>
                                                            <span><?=$keys + 1?></span>
                                                            <input class="prodId" data-id="<?=$nomenclature['id']?>" type="hidden">
                                                            <input class="nomId" data-product="<?=$nomenclature['nomenclature_id']?>" type="hidden">
                                                        </td>
                                                        <td class="imageNom"><img src="/upload/<?=$nomenclature['image']?>"></td>
                                                        <td class="nomenclatureName"><?=$nomenclature['name']?></td>
                                                        <td class="ordersAddCount">
                                                            <input type="number" class="form-control ordersCountInput">
                                                            <input class="ordersPriceInput" type="hidden" value="<?=$nomenclature['price']?>">
                                                            <input class="ordersCostInput" type="hidden" value="<?=$nomenclature['cost']?>">
                                                        </td>
                                                    </tr>
                                                    <?php
                                                }
                                                ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <?php $page = @$_GET['paging'] ?? 1; ?>
                                    <?php  $count = intval(ceil($total/10)) ; ?>
                                     <nav aria-label="Page navigation example" class="pagination">
                                        <ul class="pagination pagination-sm">
                                            <li class="page-item prev <?= ($page <= 1) ? 'disabled' : '' ?>">
                                                <a class="page-link by_ajax" href="#" data-href="/orders/get-nomiclature?paging=<?= $page-1 ?>"><i class="tf-icon bx bx-chevrons-left"></i></a>
                                            </li>
                                            <?php for ($i = 1;$i <= $count; $i++){ ?>
                                                <?php if($i > 0 && $i <= $count+1){ ?>
                                                <li class="page-item <?= ($page==$i) ? 'active' : '' ?>">
                                                    <a class="page-link by_ajax" href="#" data-href="/orders/get-nomiclature?paging=<?= $i ?>"><?= $i ?>
                                                    </a>
                                                </li>
                                                <?php } ?>
                                            <?php } ?>

                                            <?php if(intval($page) < $count){ ?>
                                            <li class="page-item next">
                                                <a class="page-link by_ajax" href="#" data-href="/orders/get-nomiclature?paging=<?= $page+1 ?>"><i class="tf-icon bx bx-chevrons-right"></i></a>
                                            </li>
                                            <?php } ?>
                                        </ul>
                                    </nav>
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
                        <div class="loader d-none">
                            <img src="/upload/loader.gif" >
                        </div>
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

<style>
    /* Vendor prefixes for cross-browser support */
    input[type="search"]::-webkit-search-cancel-button,
    input[type="search"]::-webkit-search-clear-button {
        -webkit-appearance: none;
        appearance: none;
        display: none;
    }
</style>
