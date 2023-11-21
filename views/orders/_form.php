<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Orders $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="orders-form">
    <div class="card card-primary">
        <?php $form = ActiveForm::begin(); ?>
            <div class="default-panel">
                <div class="panel-title premission">
                    <span class="non-active">Orders</span>
                </div>
<!--            <div class="card-body formDesign">-->
                <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName">
                    <?= $form->field($model, 'user_id')->dropDownList($users) ?>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName">
                    <?= $form->field($model, 'clients_id')->dropDownList($clients) ?>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersTotalPrice">
                    <?= $form->field($model, 'total_price')->textInput() ?>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersTotalCount">
                    <?= $form->field($model, 'total_count')->textInput() ?>
                </div>
            </div>
            <div class="default-panel">
                <div class="panel-title premission">
                    <span class="non-active">Add orders</span>
                </div>
                <div class="card">
                    <div class="table-responsive text-nowrap">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Check</th>
                                <th>Name</th>
                                <th>Count</th>
                                <!--                                                <th>Actions</th>-->
                            </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">

                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Button trigger modal -->
                <button type="button" class="btn rounded-pill btn-secondary addOrders" data-bs-toggle="modal" data-bs-target="#largeModal">add</button>
                <!-- Modal -->
                <div class="modal fade" id="largeModal" tabindex="-1" style="display: none;" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel3">Add orders</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="card">
                                    <div class="table-responsive text-nowrap">
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Check</th>
                                                <th>Name</th>
                                                <th>Count</th>
<!--                                                <th>Actions</th>-->
                                            </tr>
                                            </thead>
                                            <tbody class="table-border-bottom-0">
                                            <?php
                                            foreach ($nomenclatures as $keys => $nomenclature){
                                                ?>
                                                <tr class="addOrdersTableTr">
                                                    <td><?=$keys + 1?></td>
                                                    <td><input data-id="<?=$nomenclature['id']?>" type="checkbox"></td>
                                                    <td class="nomenclatureName"><?=$nomenclature['name']?></td>
                                                    <td class="ordersAddCount"><input type="number" class="form-control"></td>
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
                                <button type="button" class="btn rounded-pill btn-secondary create" data-bs-dismiss="modal">Add orders</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="card-footer">
                <?= Html::submitButton('Save', ['class' => 'btn rounded-pill btn-secondary']) ?>
            </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<?php
$this->registerJsFile(
    '@web/js/orders.js',
    ['depends' => [\yii\web\JqueryAsset::class]]
);
?>