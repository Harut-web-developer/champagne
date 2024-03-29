<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\CustomfieldsBlocksTitle;
use app\models\CustomfieldsBlocksInputs;

/** @var yii\web\View $this */
/** @var app\models\Nomenclature $model */
/** @var yii\widgets\ActiveForm $form */
$blocks = CustomfieldsBlocksTitle::find()->where(['page'=>'nomenclature','block_type'=>1])->orderBy(['order_number'=>SORT_ASC])->all();
$req = true;
if(isset($action__)){
    $req = false;
}
?>


<div class="nomenclature-form">
    <div class="card card-primary">
        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
        <div class="dinamic-form">
            <input type="hidden" name="page" value="nomenclature">
            <div class="default-panel" data-id="2" data-page="nomenclature">
                <div class="panel-title">
                    <span class="non-active"><?=$model->DefaultTitle->title?></span>
                    <input type="text" name="newblocks[<?php echo $model->DefaultTitle->id;?>]" value="<?=$model->DefaultTitle->title?>"  class="only-active form-control">
                    <button type="button" class="btn btn-default btn-sm edite-block-title" ><i class='bx bx-edit-alt'></i></button>
                    <button type="button" class="btn btn-default btn-sm edite-block-title-save" ><i class='bx bx-save'></i></button>
                </div>
                <div class="card-body formDesign">
                    <div class="form-group col-md-12 col-lg-12 col-sm-12 nomenclatureImage">
                        <?= $form->field($model, 'image')->fileInput() ?>
                        <?php if (isset($model->id)){ ?>
                            <img style="width:50px" src="/upload/<?= $model->image ?>" alt="">
                        <?php }?>
                    </div>
                    <div class="form-group col-md-12 col-lg-12 col-sm-12 nomenclatureName">
                        <?= $form->field($model, 'name')->textInput(['maxlength' => true,'required' => true]) ?>
                    </div>
                    <div class="form-group col-md-12 col-lg-12 col-sm-12 nomenclaturePrice">
                        <?= $form->field($model, 'cost')->input('text',['required' => true]) ?>
                    </div>
                    <div class="form-group col-md-12 col-lg-12 col-sm-12 nomenclaturePrice">
                        <?= $form->field($model, 'price')->input('text',['required' => true]) ?>
                    </div>
                </div>
                <?php $fields = CustomfieldsBlocksInputs::find()->where(['iblock_id'=>2])->all(); ?>
                <?php if(!empty($fields)){ ?>
                    <?php foreach ($fields as $fild => $fild_simple){ ?>
                        <?php echo CustomfieldsBlocksInputs::createElement($fild_simple,$model->id);?>
                    <?php } ?>
                <?php } ?>
            </div>
            <?php if(!empty($blocks)){ ?>
                <?php foreach ($blocks as $block => $block_val){ ?>
                    <div class="default-panel"  data-id="<?php echo $block_val->id;?>" data-page="nomenclature">
                        <div class="panel-title">
                            <span class="non-active"><?=$block_val->title?></span>
                            <input type="text" name="newblocks[<?php echo $block_val->id;?>]" value="<?=$block_val->title?>"  class="only-active form-control">
                            <button type="button" class="btn btn-default btn-sm edite-block-title" ><i class='bx bx-edit-alt'></i></button>
                            <button type="button" class="btn btn-default btn-sm edite-block-title-save" ><i class='bx bx-save'></i></button>
                            <button type="button" class="btn btn-default btn-sm edite-block-trash"><i class="bx bx-trash"></i></button>
                        </div>
                        <?php $fields = CustomfieldsBlocksInputs::find()->where(['iblock_id'=>$block_val->id])->all(); ?>
                        <?php if(!empty($fields)){ ?>
                            <?php foreach ($fields as $fild => $fild_simple){ ?>
                                <?php echo CustomfieldsBlocksInputs::createElement($fild_simple,$model->id);?>
                            <?php } ?>
                        <?php } ?>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
        <div class="card-footer">
            <?= Html::submitButton('Պահպանել', ['class' => 'btn rounded-pill btn-secondary submit_save']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>