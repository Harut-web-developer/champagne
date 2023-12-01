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
                <div class="form-group col-md-12 col-lg-12 col-sm-12 usersPassword">
                    <?= $form->field($model, 'email')->input( 'email',['required' => true]) ?>
                </div>
                <div class="form-group col-md-12 col-lg-12 col-sm-12 usersPassword">
                    <?= $form->field($model, 'phone')->input('text',['required' => true]) ?>
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
                <span class="non-active">Թույլտվություններ</span>
            </div>
            <?php if($model->id){?>
                <div class="premission-content">
                    <div class="rows-checkbox">
                        <?php
                        $permissions = [
                            ['id' => 1, 'name' => 'Ստեղծել պահեստ'],
                            ['id' => 2, 'name' => 'Փոփոխել պահեստ'],
                            ['id' => 3, 'name' => 'Ջնջել պահեստ'],
                            ['id' => 4, 'name' => 'Տեսնել պահեստ'],
                            ['id' => 5, 'name' => 'Ստեղծել հաճախորդ'],
                            ['id' => 6, 'name' => 'Փոփոխել հաճախորդ'],
                            ['id' => 7, 'name' => 'Ջնջել հաճախորդ'],
                            ['id' => 8, 'name' => 'Տեսնել հաճախորդ'],
                            ['id' => 9, 'name' => 'Ստեղծել անվանակարգ'],
                            ['id' => 10, 'name' => 'Փոփոխել անվանակարգ'],
                            ['id' => 11, 'name' => 'Ջնջել անվանակարգ'],
                            ['id' => 12, 'name' => 'Տեսնել անվանակարգ'],
                            ['id' => 13, 'name' => 'Ստեղծել օգտատեր'],
                            ['id' => 14, 'name' => 'Փոփոխել օգտատեր'],
                            ['id' => 15, 'name' => 'Ջնջել օգտատեր'],
                            ['id' => 16, 'name' => 'Տեսնել օգտատեր'],
                            ['id' => 17, 'name' => 'Ստեղծել ապրանք'],
                            ['id' => 18, 'name' => 'Փոփոխել ապրանք'],
                            ['id' => 19, 'name' => 'Ջնջել ապրանք'],
                            ['id' => 20, 'name' => 'Տեսնել ապրանք'],
                            ['id' => 21, 'name' => 'Ստեղծել վաճառք'],
                            ['id' => 22, 'name' => 'Փոփոխել վաճառք'],
                            ['id' => 23, 'name' => 'Ջնջել վաճառք'],
                            ['id' => 24, 'name' => 'Տեսնել վաճառք'],
                            ['id' => 25, 'name' => 'Ստեղծել տեղեկամատյան'],
                            ['id' => 26, 'name' => 'Փոփոխել տեղեկամատյան'],
                            ['id' => 27, 'name' => 'Ջնջել տեղեկամատյան'],
                            ['id' => 28, 'name' => 'Տեսնել տեղեկամատյան'],
                            ['id' => 29, 'name' => 'Ստեղծել կարգավիճակ'],
                            ['id' => 30, 'name' => 'Փոփոխել կարգավիճակ'],
                            ['id' => 31, 'name' => 'Ջնջել կարգավիճակ'],
                            ['id' => 32, 'name' => 'Տեսնել կարգավիճակ'],
                            ['id' => 33, 'name' => 'Ստեղծել թույլտվություն'],
                            ['id' => 34, 'name' => 'Փոփոխել թույլտվություն'],
                            ['id' => 35, 'name' => 'Ջնջել թույլտվություն'],
                            ['id' => 36, 'name' => 'Տեսնել թույլտվություն'],
                            ['id' => 37, 'name' => 'Ստեղծել փաստաթուղթ'],
                            ['id' => 38, 'name' => 'Փոփոխել փաստաթուղթ'],
                            ['id' => 39, 'name' => 'Ջնջել փաստաթուղթ'],
                            ['id' => 40, 'name' => 'Տեսնել փաստաթուղթ'],
                            ['id' => 41, 'name' => 'Ստեղծել զեղչ'],
                            ['id' => 42, 'name' => 'Փոփոխել զեղչ'],
                            ['id' => 43, 'name' => 'Ջնջել զեղչ'],
                            ['id' => 44, 'name' => 'Տեսնել զեղչ'],
                            ['id' => 45, 'name' => 'Ստեղծել փոխարժեք'],
                            ['id' => 46, 'name' => 'Փոփոխել փոխարժեք'],
                            ['id' => 47, 'name' => 'Ջնջել փոխարժեք'],
                            ['id' => 48, 'name' => 'Տեսնել փոխարժեք'],
                            ['id' => 49, 'name' => 'Ստեղծել երթուղի'],
                            ['id' => 50, 'name' => 'Փոփոխել երթուղի'],
                            ['id' => 51, 'name' => 'Ջնջել երթուղի'],
                            ['id' => 52, 'name' => 'Տեսնել երթուղի'],
                            ['id' => 53, 'name' => 'Ստեղծել քարտեզ'],
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
                            <span class="items-title">Ստեղծել պահեստ</span>
                            <input type="checkbox" value="1" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">Փոփոխել պահեստ</span>
                            <input type="checkbox" value="2" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">Ջնջել պահեստ</span>
                            <input type="checkbox" value="3" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">Տեսնել պահեստ</span>
                            <input type="checkbox" value="4" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">Ստեղծել հաճախորդ</span>
                            <input type="checkbox" value="5" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">Փոփոխել հաճախորդ</span>
                            <input type="checkbox" value="6" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">Ջնջել հաճախորդ</span>
                            <input type="checkbox" value="7" name="premission[]">
                        </div>
                    </div>
                    <div class="rows-checkbox">
                        <div class="premission-content-items">
                            <span class="items-title">Տեսնել հաճախորդ</span>
                            <input type="checkbox" value="8" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">Ստեղծել անվանակարգ</span>
                            <input type="checkbox" value="9" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">Փոփոխել անվանակարգ</span>
                            <input type="checkbox" value="10" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">Ջնջել անվանակարգ</span>
                            <input type="checkbox" value="11" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">Տեսնել անվանակարգ</span>
                            <input type="checkbox" value="12" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">Ստեղծել օգտատեր</span>
                            <input type="checkbox" value="13" name="premission[]">
                        </div>
                    </div>
                    <div class="rows-checkbox">
                        <div class="premission-content-items">
                            <span class="items-title">Փոփոխել օգտատեր</span>
                            <input type="checkbox" value="14" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">Ջնջել օգտատեր</span>
                            <input type="checkbox"  value="15" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">Տեսնել օգտատեր</span>
                            <input type="checkbox" value="16" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">Ստեղծել ապրանք</span>
                            <input type="checkbox" value="17" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">Փոփոխել ապրանք</span>
                            <input type="checkbox" value="18" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">Ջնջել ապրանք</span>
                            <input type="checkbox" value="19" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">Տեսնել ապրանք</span>
                            <input type="checkbox" value="20" name="premission[]">
                        </div>
                    </div>
                    <div class="rows-checkbox">
                        <div class="premission-content-items">
                            <span class="items-title">Ստեղծել վաճառք</span>
                            <input type="checkbox" value="21" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">Փոփոխել վաճառք</span>
                            <input type="checkbox" value="22" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">Ջնջել վաճառք</span>
                            <input type="checkbox" value="23" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">Տեսնել վաճառք</span>
                            <input type="checkbox" value="24" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">Ստեղծել տեղեկամատյան</span>
                            <input type="checkbox" value="25" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">Փոփոխել տեղեկամատյան</span>
                            <input type="checkbox" value="26" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">Ջնջել տեղեկամատյան</span>
                            <input type="checkbox" value="27" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">Տեսնել տեղեկամատյան</span>
                            <input type="checkbox" value="28" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">Ստեղծել կարգավիճակ</span>
                            <input type="checkbox" value="29" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">Փոփոխել կարգավիճակ</span>
                            <input type="checkbox" value="30" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">Ջնջել կարգավիճակ</span>
                            <input type="checkbox" value="31" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">Տեսնել կարգավիճակ</span>
                            <input type="checkbox" value="32" name="premission[]">
                        </div>
                    </div>
                    <div class="rows-checkbox">
                        <div class="premission-content-items">
                            <span class="items-title">Ստեղծել թույլտվություն</span>
                            <input type="checkbox" value="33" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">Փոփոխել թույլտվություն</span>
                            <input type="checkbox" value="34" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">Ջնջել թույլտվություն</span>
                            <input type="checkbox" value="35" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">Տեսնել թույլտվություն</span>
                            <input type="checkbox" value="36" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">Ստեղծել փաստաթուղթ</span>
                            <input type="checkbox" value="37" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">Փոփոխել փաստաթուղթ</span>
                            <input type="checkbox" value="38" name="premission[]">
                        </div>
                    </div>
                    <div class="rows-checkbox">
                        <div class="premission-content-items">
                            <span class="items-title">Ջնջել փաստաթուղթ</span>
                            <input type="checkbox" value="39" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">Տեսնել փաստաթուղթ</span>
                            <input type="checkbox" value="40" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">Ստեղծել զեղչ</span>
                            <input type="checkbox" value="41" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">Փոփոխել զեղչ</span>
                            <input type="checkbox" value="42" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">Ջնջել զեղչ</span>
                            <input type="checkbox" value="43" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">Տեսնել զեղչ</span>
                            <input type="checkbox" value="44" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">Ստեղծել փոխարժեք</span>
                            <input type="checkbox" value="45" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">Փոփոխել փոխարժեք</span>
                            <input type="checkbox" value="46" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">Ջնջել փոխարժեք</span>
                            <input type="checkbox" value="47" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">Տեսնել փոխարժեք</span>
                            <input type="checkbox" value="48" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">Ստեղծել երթուղի</span>
                            <input type="checkbox" value="49" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">Փոփոխել երթուղի</span>
                            <input type="checkbox" value="50" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">Ջնջել երթուղի</span>
                            <input type="checkbox" value="51" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">Տեսնել երթուղի</span>
                            <input type="checkbox" value="52" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <span class="items-title">Տեսնել քարտեզ</span>
                            <input type="checkbox" value="53" name="premission[]">
                        </div>
                    </div>
                </div>
                <?php } ?>

        </div>
        <div class="card-footer">
            <?= Html::submitButton('Պահպանել', ['class' => 'btn rounded-pill btn-secondary']) ?>
        </div>
        <?php ActiveForm::end(); ?>


    </div>
</div>