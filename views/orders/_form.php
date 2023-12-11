<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\widgets\CustomLinkPager;

/** @var yii\web\View $this */
/** @var app\models\Orders $model */
/** @var yii\widgets\ActiveForm $form */

?>
<?php if ($model->id){ ?>
    <div style="display: inline">
        <img style="height: 20px; margin-left: auto" class="downloadUpdateXLSX" onclick="window.open('/orders/reports?type=1&id=<?= $model->id ?>','newwindow','width:500, height:700')" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAAACXBIWXMAAAsTAAALEwEAmpwYAAAC+klEQVR4nO2ZzWsTURDAFwW/kYre9CKKiB4ERT2of4AUveXqF1htZtaQ7rw0NDN9ehMUsTfxIggepPSioOJRz3rQg3gRqYI91I/Wg1rUykvzkk2atNl2d/uCHRjIMpnZ+c2b93Y28bxlWZZlmVf8gt4GJMNAMolKpheoU6DkJarBjJd68oo/LyLx2UqsUwMAkuFYk68oKLmcFsBkXAkvCQTGVPFyMRT3z24nudoxAEsCgTEDpA6BCQCkCoEJAaQGgQkCpAKRNICRZkdsbA+7NABaQsQxdsQFYEaSyBDEL5wBAMWPokIA8S9nAJJqvXnlvwEAxbetj9Z6BSh5V2+Xi9YOhcGzzgGgkimfSjuane9AMpbP59dW4FY1wqEjAKbKd6xfT5/egsQ/yjaSYggs52QL4Yz+xj69u+pLfBcUT+RyustcZ7XeYFbDZYBpVHLP+mb7S0eR5GYISJzdxFjtd/7jB3qP3czZAu81n6FY3Iwk35wHwBm93xgHSK4vJJa3JADE333fX10HoPhNJwFcmRWH+NScrafkJygeygZyOAiC9YtOfOEA/MmcNmXfoLQd+3mf3Q9I8qqF30f7vdglevUHz4cHMyC5Ya/9QE40qzwmlXxUAFD8OpPJrDR+ZhWQZNz8rtRbLG6qxiN53uAzlFjyUQEwkOM1P86HbAN1z4awT2HgUN39Auk2LQXEH8Lxkgcgfmp9zLyDSkarVSYZO631GmsHxQ+sLVvZL1WbSbwWdzQ1AF/xuUuBHDDa7EUdiEvWjsRnWh2T6MQxGkG9DgZ4DIpPdiLAX6AStHs/zz0Avhblfp5TAOUZSW9MFQAUT8QHIA/DsXM53QXEz0xbtTOmI/GTHq3XRQMgGYkLAJTcqqs21Y7TtjWQ7mgAfXqXGQliASAZCcfOFvVO01YRVnDcvGt7UeVCfmCreUlZbDuZfzvNU7puFZQcawuC5Asovd9zUZDkyFx/JoLir72BHPRcFmwB0RHJexVphOio5K1Uxu33qPhtOz3/DyrGtgq43BHiAAAAAElFTkSuQmCC">
    </div>
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
                    <?= $form->field($model, 'total_price')->textInput(['readonly'=> true, 'class' => 'form-control totalPrice']) ?>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersTotalCount">
                    <?= $form->field($model, 'total_count')->textInput(['readonly'=> true,'class' => 'form-control totalCount']) ?>
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
                                        <td><?=$keys + 1?><input class="orderItemsId" type="hidden" name="order_items[]" value="<?=$item['id']?>">
                                            <input type="hidden" name="product_id[]" value="<?=$item['product_id']?>">
                                            <input class="nomId"  type="hidden" name="nom_id[]" value="<?=$item['nom_id']?>">
                                        </td>
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
                            <div class="modal-body" id="ajax_content">
                                <input class="form-control col-md-3 mb-3 searchForOrder" type="search" placeholder="Որոնել...">
                                <div class="card">
                                    <div class="table-responsive text-nowrap">
                                        <table class="table resultSearch">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Ընտրել</th>
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
                                                    <td><?=$keys + 1?></td>
                                                    <td>
                                                        <input data-id="<?=$nomenclature['id']?>" type="checkbox">
                                                        <input class="productIdInput" data-product="<?=$nomenclature['products_id']?>" type="hidden">
                                                    </td>
                                                    <td class="imageNom"><img src="/upload/<?=$nomenclature['image']?>"></td>
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
                            <div class="modal-body" id="ajax_content">
                                <input class="form-control col-md-3 mb-3 searchForOrder" type="search" placeholder="Որոնել...">
                                <div class="card">
                                    <div class="table-responsive text-nowrap">
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Ընտրել</th>
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
                                                    <td><?=$keys + 1?></td>
                                                    <td>
                                                        <input data-id="<?=$nomenclature['id']?>" type="checkbox">
                                                        <input class="productIdInput" data-product="<?=$nomenclature['products_id']?>" type="hidden">
                                                    </td>
                                                    <td class="imageNom"><img src="/upload/<?=$nomenclature['image']?>"></td>
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