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
            <div class="default-panel" data-id="1" data-page="users">
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
                <div class="form-group col-md-12 col-lg-12 col-sm-12 usersPassword">
                    <?= $form->field($model, 'password')->passwordInput(['required' => true]) ?>
                </div>
                <?php $fields = CustomfieldsBlocksInputs::find()->where(['iblock_id'=>1])->all(); ?>
                <?php if(!empty($fields)){ ?>
                    <?php foreach ($fields as $fild => $fild_simple){ ?>
                        <?php echo CustomfieldsBlocksInputs::createElement($fild_simple,$model->id, false);?>
                    <?php } ?>
                <?php } ?>
            </div>
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
                        <?php $fields = CustomfieldsBlocksInputs::find()->where(['iblock_id'=>$block_val->id])->all(); ?>
                        <?php if(!empty($fields)){ ?>
                            <?php foreach ($fields as $fild => $fild_simple){ ?>
                                <?php echo CustomfieldsBlocksInputs::createElement($fild_simple,$model->id,false);?>
                            <?php } ?>
                        <?php } ?>
                        <div class="actions">
                            <div class="btn-group">
                                <button type="button" class="btn btn-default btn-sm create-block-item dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    Create Fild
                                </button>
                                <ul class="dropdown-menu" style="">
                                    <li data-type="number">NUMBER <br><span>Lorem ipsum dolor sit amet.</span>
                                    </li>
                                    <li data-type="varchar">TEXT (255 Simbols) <br><span>Lorem ipsum dolor sit amet.</span></li>
                                    <li data-type="list">LIST <br><span>Lorem ipsum dolor sit amet.</span></li>
                                    <li data-type="file">FILE <br><span>Lorem ipsum dolor sit amet.</span></li>
                                    <li data-type="text">TEXTAREA <br><span>Lorem ipsum dolor sit amet.</span></li>
                                    <li data-type="date">DATE <br><span>Lorem ipsum dolor sit amet.</span></li>
                                    <li data-type="datetime">DATETIME <br><span>Lorem ipsum dolor sit amet.</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
        <div class="default-panel">
            <div class="panel-title premission">
                <span class="non-active">Premissions</span>
            </div>
            <?php if($model->id){?>
                <div class="premission-content">
                    <div class="rows-checkbox">
                        <?php
                        $permissions = [
                            ['id' => 1, 'name' => 'warehouse create'],
                            ['id' => 2, 'name' => 'warehouse update'],
                            ['id' => 3, 'name' => 'warehouse delete'],
                            ['id' => 4, 'name' => 'warehouse view'],
                            ['id' => 5, 'name' => 'clients create'],
                            ['id' => 6, 'name' => 'clients update'],
                            ['id' => 7, 'name' => 'clients delete'],
                            ['id' => 8, 'name' => 'clients view'],
                            ['id' => 9, 'name' => 'nomenclature create'],
                            ['id' => 10, 'name' => 'nomenclature update'],
                            ['id' => 11, 'name' => 'nomenclature delete'],
                            ['id' => 12, 'name' => 'nomenclature view'],
                            ['id' => 13, 'name' => 'users create'],
                            ['id' => 14, 'name' => 'users update'],
                            ['id' => 15, 'name' => 'users delete'],
                            ['id' => 16, 'name' => 'users view'],
                            ['id' => 17, 'name' => 'products create'],
                            ['id' => 18, 'name' => 'products update'],
                            ['id' => 19, 'name' => 'products delete'],
                            ['id' => 20, 'name' => 'products view'],
                            ['id' => 21, 'name' => 'orders create'],
                            ['id' => 22, 'name' => 'orders update'],
                            ['id' => 23, 'name' => 'orders delete'],
                            ['id' => 24, 'name' => 'orders view'],
                            ['id' => 25, 'name' => 'log delete'],
                            ['id' => 26, 'name' => 'log delete'],
                            ['id' => 27, 'name' => 'log delete'],
                            ['id' => 28, 'name' => 'log delete'],
                            ['id' => 29, 'name' => 'roles create'],
                            ['id' => 30, 'name' => 'roles update'],
                            ['id' => 31, 'name' => 'roles delete'],
                            ['id' => 32, 'name' => 'roles view'],
                            ['id' => 33, 'name' => 'premissions create'],
                            ['id' => 34, 'name' => 'premissions update'],
                            ['id' => 35, 'name' => 'premissions delete'],
                            ['id' => 36, 'name' => 'premissions view'],
                        ];

                        foreach ($permissions as $permission) {
                            $permissionId = $permission['id'];
                            $permissionName = $permission['name'];
                            ?>
                            <div class="premission-content-items">
                                <span class="items-title"><?php echo $permissionName; ?></span>
                                <input type="checkbox" <?php echo (in_array($permissionId, array_column($user_premission_select, 'premission_id'))) ? 'checked' : ''; ?> value="<?php echo $permissionId; ?>" name="premission[]">
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } else{ ?>
                <div class="premission-content">
                    <div class="rows-checkbox">
                        <div class="premission-content-items">
                            <span class="items-title">warehouse create</span>
                            <input type="checkbox" value="1" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">warehouse update</span>
                            <input type="checkbox" value="2" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">warehouse delete</span>
                            <input type="checkbox" value="3" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">warehouse view</span>
                            <input type="checkbox" value="4" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">clients create</span>
                            <input type="checkbox" value="5" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">clients update</span>
                            <input type="checkbox" value="6" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">clients delete</span>
                            <input type="checkbox" value="7" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">clients view</span>
                            <input type="checkbox" value="8" name="premission[]">
                        </div>
                    </div>
                    <div class="rows-checkbox">
                        <div class="premission-content-items">
                            <span class="items-title">nomenclature create</span>
                            <input type="checkbox" value="9" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">nomenclature update</span>
                            <input type="checkbox" value="10" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">nomenclature delete</span>
                            <input type="checkbox" value="11" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">nomenclature view</span>
                            <input type="checkbox" value="12" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">users create</span>
                            <input type="checkbox" value="13" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">users update</span>
                            <input type="checkbox" value="14" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">users delete</span>
                            <input type="checkbox"  value="15" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">users view</span>
                            <input type="checkbox" value="16" name="premission[]">
                        </div>
                    </div>
                    <div class="rows-checkbox">
                        <div class="premission-content-items">
                            <span class="items-title">products create</span>
                            <input type="checkbox" value="17" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">products update</span>
                            <input type="checkbox" value="18" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">products delete</span>
                            <input type="checkbox" value="19" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">products view</span>
                            <input type="checkbox" value="20" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">orders create</span>
                            <input type="checkbox" value="21" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">orders update</span>
                            <input type="checkbox" value="22" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">orders delete</span>
                            <input type="checkbox" value="23" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">orders view</span>
                            <input type="checkbox" value="24" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">log create</span>
                            <input type="checkbox" value="25" name="premission[]">
                        </div>
                    </div>
                    <div class="rows-checkbox">
                        <div class="premission-content-items">
                            <span class="items-title">log update</span>
                            <input type="checkbox" value="26" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">log delete</span>
                            <input type="checkbox" value="27" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">log view</span>
                            <input type="checkbox" value="28" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">roles create</span>
                            <input type="checkbox" value="29" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">roles update</span>
                            <input type="checkbox" value="30" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">roles delete</span>
                            <input type="checkbox" value="31" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">roles view</span>
                            <input type="checkbox" value="32" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">premissions create</span>
                            <input type="checkbox" value="33" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">premissions update</span>
                            <input type="checkbox" value="34" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">premissions delete</span>
                            <input type="checkbox" value="35" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">premissions view</span>
                            <input type="checkbox" value="36" name="premission[]">
                        </div>
                    </div>
                </div>
                <?php } ?>

        </div>
        <div class="card-footer">
            <?= Html::submitButton('Save', ['class' => 'btn rounded-pill btn-secondary']) ?>
        </div>
        <?php ActiveForm::end(); ?>


    </div>
</div>