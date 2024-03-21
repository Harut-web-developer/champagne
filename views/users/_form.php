<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\CustomfieldsBlocksTitle;
use app\models\CustomfieldsBlocksInputs;

/** @var yii\web\View $this */
/** @var app\models\Users $model */
/** @var yii\widgets\ActiveForm $form */
$blocks = CustomfieldsBlocksTitle::find()->where(['page'=>'users','block_type'=>1])->orderBy(['order_number'=>SORT_ASC])->all();

?>
<div class="users-form">
    <div class="card card-primary">
        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
        <div class="dinamic-form">
            <input type="hidden" name="page" value="users">
            <div class="default-panel" data-id="18" data-page="users">
                <div class="panel-title">
                    <span class="non-active"><?=$model->DefaultTitle->title?></span>
                    <input type="text" name="newblocks[<?php echo $model->DefaultTitle->id;?>]" value="<?=$model->DefaultTitle->title?>"  class="only-active form-control">
                    <button type="button" class="btn btn-default btn-sm edite-block-title" ><i class='bx bx-edit-alt'></i></button>
                    <button type="button" class="btn btn-default btn-sm edite-block-title-save" ><i class='bx bx-save'></i></button>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 usersName">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true,'required' => true]) ?>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 usersUsername">
                    <?= $form->field($model, 'username')->textInput(['maxlength' => true,'required' => true]) ?>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 usersUsername">
                    <?= $form->field($model, 'role_id')->dropDownList($roles,['required' => true]) ?>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 usersUsername warehouseCheck">
                    <?= $form->field($model, 'warehouse_id')->dropDownList($warehouse) ?>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 usersPassword">
                    <?= $form->field($model, 'email')->input( 'email',['required' => true]) ?>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 usersPassword">
                    <?= $form->field($model, 'phone')->input('text',['required' => true]) ?>
                </div>
                <?php if (isset($model->id)) { ?>
                    <div class="form-group col-md-12 col-lg-12 col-sm-12 usersPassword">
                        <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Ձեր գաղտնաբառն']) ?>
                    </div>
                <?php } else { ?>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 usersPassword">
                    <?= $form->field($model, 'password')->passwordInput(['required' => true, 'placeholder' => 'Մուտքագրեք ձեր գաղտնաբառն']) ?>
                </div>
                <?php } ?>
                <?php $fields = CustomfieldsBlocksInputs::find()->where(['iblock_id'=>18])->all(); ?>
                <?php if(!empty($fields)){ ?>
                    <?php foreach ($fields as $fild => $fild_simple){ ?>
                        <?php echo CustomfieldsBlocksInputs::createElement($fild_simple,$model->id);?>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
        <?php if(!empty($blocks)){ ?>
            <?php foreach ($blocks as $block => $block_val){ ?>
                <div class="default-panel"  data-id="<?php echo $block_val->id;?>" data-page="users">
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
        <div class="default-panel">
            <div class="panel-title premission">
                <span class="non-active">Թույլտվություններ</span>
            </div>
            <?php if($model->id){?>
                <div class="premission-content">
                    <?php foreach ($premissions_check as $premission){?>
                       <div class="premission-content-items">
                            <label for="premission<?=$premission['id']?>" class="items-title"><?=$premission['name']?></label>
                            <input id="premission<?=$premission['id']?>" type="checkbox" <?php echo (in_array($premission['id'], array_column($user_premission_select, 'premission_id'))) ? 'checked' : ''; ?> value="<?=$premission['id']?>" name="premission[]">
                       </div>
                  <?php } ?>
                </div>
            <?php } else{ ?>
                <div class="premission-content">
                    <?php foreach ($premissions_check as $premission){ ?>
                        <div class="premission-content-items">
                            <label for="premission<?=$premission['id']?>" class="items-title"><?=$premission['name']?></label>
                            <input id="premission<?=$premission['id']?>" type="checkbox" value="<?=$premission['id']?>" name="premission[]">
                        </div>
                       <?php }
                    ?>
                </div>
                <?php } ?>
        </div>
        <div class="card-footer">
            <?= Html::submitButton('Պահպանել', ['class' => 'btn rounded-pill btn-secondary submit_save']) ?>
        </div>
        <?php ActiveForm::end(); ?>


    </div>
</div>