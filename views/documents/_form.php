<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Documents $model */
/** @var yii\widgets\ActiveForm $form */
?>
<?php if ($model->id){
    if ($model->document_type === '1'){
        $value = 'Մուտք';
    }elseif ($model->document_type === '2'){
        $value = 'Ելք';
    }elseif ($model->document_type === '3'){
        $value = 'Տեղափոխություն';
    }else{
        $value = 'Խոտան';
    }
    ?>
    <div class="documents-form">
        <div class="card card-primary">
            <?php $form = ActiveForm::begin(); ?>
            <div class="default-panel">
                <div class="panel-title premission">
                    <span class="non-active">Փաստաթուղթ</span>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName">
                    <?= $form->field($model, 'document_type')->textInput(['value' => $value, 'readonly' => true]) ?>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName">
                    <?= $form->field($model, 'user_id')->dropDownList($users) ?>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName">
                    <?= $form->field($model, 'warehouse_id')->dropDownList($warehouse) ?>
                </div>
                <label class="rateLabel" for="rate">Փոխարժեք</label>
                <div id="rate" class="form-group col-md-12 col-lg-12 col-sm-12 rateDocument">
                    <div class="rateType">
                        <?= $form->field($model, 'rate_id')->dropDownList($rates)->label(false) ?>
                    </div>
                    <div class="rateValue">
                        <?= $form->field($model, 'rate_value')->input('number')->label(false) ?>
                    </div>
                </div>


                <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName">
                    <label for="aah">ԱԱՀ</label>
                    <select class="form-control" name="aah" id="aah">
                        <option value="true" <?php echo ($aah['AAH'] === 'true') ? 'selected' : ''; ?>>Կա</option>
                        <option value="false" <?php echo ($aah['AAH'] === 'false') ? 'selected' : ''; ?>>Չկա</option>
                    </select>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName">
                    <?= $form->field($model, 'comment')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName">
                    <?= $form->field($model, 'date')->input('datetime-local') ?>
                </div>
            </div>
            <div class="default-panel">
                <div class="panel-title premission">
                    <span class="non-active">Ապրանքացուցակ</span>
                </div>
                <div class="card">
                    <div class="table-responsive text-nowrap">
                        <table class="table documentsAddingTable">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Անուն</th>
                                <th>Քանակ</th>
                                <th>Գին</th>
                                <th>Գործողություն</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $itemsArray = [];
                            foreach ($document_items as $keys => $document_item){
                                $itemsArray[] = $document_item['nom_id'];
                                ?>
                                <tr class="oldTr">
                                    <td><?=$keys + 1?><input type="hidden" name="document_items[]" value="<?=$document_item['id']?>">
                                        <input class="itemsId" type="hidden" name="items[]" value="<?=$document_item['nom_id']?>">
<!--                                        <input class="itemsId" type="hidden" name="nom_id[]" value="--><?php //=$document_item['nom_id']?><!--">-->
                                    </td>
                                    <td class="name"><?=$document_item['name']?></td>
                                    <td class="count"><input type="number" name="count_[]" value="<?=$document_item['count']?>" class="form-control countDocuments"></td>
                                    <td class="price"><input type="number" name="price[]" value="<?=$document_item['price']?>" class="form-control PriceDocuments"></td>
                                    <td><button  type="button" class="btn rounded-pill btn-outline-danger deleteDocumentItems">Ջնջել</button></td>
                                </tr>
                                <?php
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Button trigger modal -->
                <button type="button" class="btn rounded-pill btn-secondary addDocuments" data-bs-toggle="modal" data-bs-target="#documentsModal">Ավելացնել ապրանք</button>
                <!-- Modal -->
                <div class="modal fade" id="documentsModal" tabindex="-1" style="display: none;" aria-hidden="true">
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
                                                <tr class="documentsTableTr">
                                                    <td><?=$keys + 1?></td>
                                                    <td><input data-id="<?=$nomenclature['id']?>" type="checkbox"></td>
                                                    <td class="documentsName"><?=$nomenclature['name']?></td>
                                                    <td class="documentsCount">
                                                        <input type="number" class="form-control documentsCountInput">
                                                        <input class="documentsPriceInput" type="hidden" value="<?=$nomenclature['price']?>">
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
                                <button type="button" class="btn rounded-pill btn-secondary updateDocuments" data-bs-dismiss="modal">Ավելացնել ցուցակում</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <?= Html::submitButton('Պահպանել', ['class' => 'btn rounded-pill  btn-secondary']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
<?php
}else{
    ?>
    <div class="documents-form">
        <div class="card card-primary">
            <?php $form = ActiveForm::begin(); ?>
            <div class="default-panel">
                <div class="panel-title premission">
                    <span class="non-active">Փաստաթուղթ</span>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName">
                    <?= $form->field($model, 'document_type')->dropDownList([ '1' => 'Մուտք', '2' => 'Ելք','3' => 'Տեղափոխություն','4' => 'Խոտան', ]) ?>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName">
                    <?= $form->field($model, 'user_id')->dropDownList($users) ?>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName">
                    <?= $form->field($model, 'warehouse_id')->dropDownList($warehouse) ?>
                </div>
                <label class="rateLabel" for="rate">Փոխարժեք</label>
                <div id="rate" class="form-group col-md-12 col-lg-12 col-sm-12 rateDocument">
                    <div class="rateType">
                        <?= $form->field($model, 'rate_id')->dropDownList($rates)->label(false) ?>
                    </div>
                    <div class="rateValue">
                        <?= $form->field($model, 'rate_value')->input('number', ['placeholder' => 'Փոխարժեքի գինը','required' => true])->label(false) ?>
                    </div>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName">
                    <label for="aah">ԱԱՀ</label>
                    <select class="form-control" name="aah" id="aah">
                        <option value="true">Կա</option>
                        <option value="false">Չկա</option>
                    </select>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName">
                    <?= $form->field($model, 'comment')->textArea(['maxlength' => true]) ?>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName">
                    <?= $form->field($model, 'date')->input('datetime-local') ?>
                </div>
            </div>
            <div class="default-panel">
                <div class="panel-title premission">
                    <span class="non-active">Ապրանքացուցակ</span>
                </div>
                <div class="card">
                    <div class="table-responsive text-nowrap">
                        <table class="table documentsAddingTable">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Անուն</th>
                                <th>Քանակ</th>
                                <th>Գին</th>
                                <th>Գործողություն</th>
                            </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Button trigger modal -->
                <button type="button" class="btn rounded-pill btn-secondary addDocuments" data-bs-toggle="modal" data-bs-target="#documentsModal">Ավելացնել ապրանք</button>
                <!-- Modal -->
                <div class="modal fade" id="documentsModal" tabindex="-1" style="display: none;" aria-hidden="true">
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
                                                <tr class="documentsTableTr">
                                                    <td><?=$keys + 1?></td>
                                                    <td>
                                                        <input data-id="<?=$nomenclature['id']?>" type="checkbox">
                                                    </td>
                                                    <td class="documentsName"><?=$nomenclature['name']?></td>
                                                    <td class="documentsCount">
                                                        <input type="number" class="form-control documentsCountInput">
                                                        <input class="documentsPriceInput" type="hidden" value="<?=$nomenclature['price']?>">
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
                                <button type="button" class="btn rounded-pill btn-secondary createDocuments" data-bs-dismiss="modal">Ավելացնել ցուցակում</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <?= Html::submitButton('Պահպանել', ['class' => 'btn rounded-pill  btn-secondary']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
<?php
}
?>


<?php
$this->registerJsFile(
    '@web/js/documents.js',
    ['depends' => [\yii\web\JqueryAsset::class]]
);
?>
