<?php

use app\models\CustomfieldsBlocksInputValues;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\CustomfieldsBlocksTitle;
use app\models\CustomfieldsBlocksInputs;

/** @var yii\web\View $this */
/** @var app\models\Clients $model */
/** @var yii\widgets\ActiveForm $form */
$blocks = CustomfieldsBlocksTitle::find()->where(['page'=>'clients','block_type'=>1])->orderBy(['order_number'=>SORT_ASC])->all();
?>
<script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU&amp;apikey=e243c296-f6a7-46b7-950a-bd42eb4b2684" type="text/javascript"></script>
<script src="/js/event_reverse_geocode.js" type="text/javascript"></script>

<div class="clients-form">
    <div class="card card-primary">
        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
        <div class="dinamic-form">
            <input type="hidden" name="page" value="clients">
            <div class="default-panel" data-id="17" data-page="clients">
                <div class="panel-title">
                    <span class="non-active"><?=$model->DefaultTitle->title?></span>
                    <input type="text" name="newblocks[<?php echo $model->DefaultTitle->id;?>]" value="<?=$model->DefaultTitle->title?>"  class="only-active form-control">
                    <button type="button" class="btn btn-default btn-sm edite-block-title" ><i class='bx bx-edit-alt'></i></button>
                    <button type="button" class="btn btn-default btn-sm edite-block-title-save" ><i class='bx bx-save'></i></button>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 clientName">
                    <?= $form->field($model, 'branch_groups_id')->dropDownList(['' => 'Ընտրել'] + $branch_groups) ?>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 clientName">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 clientLocation">
                    <?= $form->field($model, 'location')->textInput(['maxlength' => true]) ?>
                    <div class="clientAbsolute"> </div>
                    <div id="map">
                    </div>
                </div>
                <?php if (empty($model->id)){ ?>
                    <div class="form-group col-md-12 col-lg-12 col-sm-12 route">
                        <label for="multipleClients">Երթուղիներ</label>
                        <select class="form-select form-control routeRequire" aria-label="Default select example" name="Clients[route]" required>
                            <option value="">Ընտրել Երթուղի</option>
                            <?php foreach ($route as $index => $rout) { ?>
                                <option value="<?= $rout['id'] ?>"><?= $rout['route'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group col-md-12 col-lg-12 col-sm-12 warehouse">
                        <label for="warehouseSelect">Պահեստներ</label>
                        <select id="warehouse Select" class="form-select form-control warehouseRequire" aria-label="Default select example" name="Clients[warehouse_id]" required>
                            <option value="">Ընտրել պահեստ</option>
                            <?php foreach ($warehouse as $index => $warehous ){ ?>
                                <option value="<?= $warehous['id'] ?>"><?= $warehous['name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                <?php } else { ?>
                    <div class="form-group col-md-12 col-lg-12 col-sm-12 route">
                        <label for="multipleClients">Երթուղիներ</label>
                        <select class="form-select form-control routeRequire" aria-label="Default select example" name="Clients[route]" required>
                            <option value="">Ընտրել Երթուղի</option>
                            <?php foreach ($route as $index => $rout) { ?>
                                <option value="<?= $rout['id'] ?>" <?= ($rout['id'] == $route_value_update['route_id']) ? 'selected' : '' ?> ><?= $rout['route']?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group col-md-12 col-lg-12 col-sm-12 warehouse">
                        <label for="warehouseSelect">Պահեստներ</label>
                        <select id="warehouse Select" class="form-select form-control warehouseRequire" aria-label="Default select example" name="Clients[warehouse_id]" required>
                            <option value="">Ընտրել պահեստ</option>
                            <?php foreach ($warehouse as $index => $warehous ){ ?>
                                <option value="<?= $warehous['id'] ?>" <?= ($warehous['id'] == $warehouse_value_update['client_warehouse_id']) ? 'selected' : '' ?> ><?= $warehous['name']?></option>
                            <?php } ?>
                        </select>
                    </div>
                <?php } ?>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 clientPhone">
                    <?= $form->field($model, 'phone')->input('text') ?>
                </div>
                <?php if ($model->id){ ?>
                    <?php $fields = CustomfieldsBlocksInputs::find()->where(['iblock_id'=>17])->all(); ?>
                    <?php if(!empty($fields)){ ?>
                        <?php foreach ($fields as $fild => $fild_simple){ ?>
                            <?php echo CustomfieldsBlocksInputs::createElement($fild_simple,$model->id);?>
                        <?php } ?>
                    <?php } ?>
                <?php }else{?>
                    <?php $fields = CustomfieldsBlocksInputs::find()->where(['iblock_id'=>17])->andWhere(['status'=>'1'])->all(); ?>
                    <?php if(!empty($fields)){ ?>
                        <?php foreach ($fields as $fild => $fild_simple){ ?>
                            <?php echo CustomfieldsBlocksInputs::createElement($fild_simple,$model->id);?>
                        <?php } ?>
                    <?php } ?>
                <?php }?>
            </div>
            <?php if ($model->id){ ?>
                <?php if(!empty($blocks)){
                $fieldValues = '';
                ?>
                    <?php foreach ($blocks as $block => $block_val){ ?>
                        <?php $fields = CustomfieldsBlocksInputs::find()->where(['iblock_id'=>$block_val->id])->all();
                        foreach ($fields as $field) {
                            $value = CustomfieldsBlocksInputValues::find()
                                ->where(['input_id' => $field->id])
                                ->andWhere(['item_id' => $model->id])
                                ->one();
                            if ($value) {
                                $fieldValues = true;
                                break;
                            } else {
                                $fieldValues = false;
                            }
                        }
                        if ($fieldValues){ ?>
                            <div class="default-panel"  data-id="<?php echo $block_val->id;?>" data-page="documents">
                                <div class="panel-title">
                                    <span class="non-active"><?=$block_val->title?></span>
                                    <input type="text" name="newblocks[<?php echo $block_val->id;?>]" value="<?=$block_val->title?>"  class="only-active form-control">
                                    <!--                                    <button type="button" class="btn btn-default btn-sm edite-block-title" ><i class='bx bx-edit-alt'></i></button>-->
                                    <!--                                    <button type="button" class="btn btn-default btn-sm edite-block-title-save" ><i class='bx bx-save'></i></button>-->
                                    <!--                                    <button type="button" class="btn btn-default btn-sm edite-block-trash"><i class="bx bx-trash"></i></button>-->
                                </div>
                                <?php if(!empty($fields)){ ?>
                                    <?php foreach ($fields as $fild => $fild_simple){ ?>
                                        <?php echo CustomfieldsBlocksInputs::createElement($fild_simple,$model->id);?>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    <?php } ?>
                <?php } ?>
            <?php }else{?>
                <?php if(!empty($blocks)){ ?>
                    <?php foreach ($blocks as $block => $block_val){ ?>
                        <div class="default-panel"  data-id="<?php echo $block_val->id;?>" data-page="clients">
                            <div class="panel-title">
                                <span class="non-active"><?=$block_val->title?></span>
                                <input type="text" name="newblocks[<?php echo $block_val->id;?>]" value="<?=$block_val->title?>"  class="only-active form-control">
                                <button type="button" class="btn btn-default btn-sm edite-block-title" ><i class='bx bx-edit-alt'></i></button>
                                <button type="button" class="btn btn-default btn-sm edite-block-title-save" ><i class='bx bx-save'></i></button>
                                <button type="button" class="btn btn-default btn-sm edite-block-trash"><i class="bx bx-trash"></i></button>
                            </div>
                            <?php $fields = CustomfieldsBlocksInputs::find()->where(['iblock_id'=>$block_val->id])->andWhere(['status'=>'1'])->all(); ?>
                            <?php if(!empty($fields)){ ?>
                                <?php foreach ($fields as $fild => $fild_simple){ ?>
                                    <?php echo CustomfieldsBlocksInputs::createElement($fild_simple,$model->id);?>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    <?php } ?>
                <?php } ?>
            <?php }?>
        </div>
        <div class="card-footer">
            <?= Html::submitButton('Պահպանել', ['class' => 'btn rounded-pill btn-secondary addClients submit_save']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

<style>
    #map {
        width: 100%;
        height: 400px;
    }
</style>
