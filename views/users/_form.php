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
                                    Ստեղծել դաշտ
                                </button>
                                <ul class="dropdown-menu custom_field_menu" style="">
                                    <li data-type="number">NUMBER <br><span>Թվային արժեք ավելացնելու դաշտ:</span></li>
                                    <li data-type="varchar">TEXT (255 Simbols) <br><span>Տեքստ ավելացնելու դաշտ:</span></li>
                                    <li data-type="list">LIST <br><span>Ցուցակներ լրացնելու դաշտ:</span></li>
                                    <li data-type="file">FILE <br><span>Նկար ավելացնելու դաշտ:</span></li>
                                    <li data-type="text">TEXTAREA <br><span>Մեշ ծավալով տեքստ ավելացնելու դաշտ:</span></li>
                                    <li data-type="date">DATE <br><span>Ամսաթիվ ավելացնելու դաշտ:</span></li>
                                    <li data-type="datetime">DATETIME <br><span>Ժամ և ամսաթիվ ավելացնելու դաշտ:</span></li>
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
                            ['id' => 1, 'name' => 'Ստեղծել պահեստ', 'label_id' => 'premission1'],
                            ['id' => 2, 'name' => 'Փոփոխել պահեստ', 'label_id' => 'premission2'],
                            ['id' => 3, 'name' => 'Ջնջել պահեստ', 'label_id' => 'premission3'],
                            ['id' => 4, 'name' => 'Տեսնել պահեստ', 'label_id' => 'premission4'],
                            ['id' => 5, 'name' => 'Ստեղծել հաճախորդ', 'label_id' => 'premission5'],
                            ['id' => 6, 'name' => 'Փոփոխել հաճախորդ', 'label_id' => 'premission6'],
                            ['id' => 7, 'name' => 'Ջնջել հաճախորդ', 'label_id' => 'premission7'],
                            ['id' => 8, 'name' => 'Տեսնել հաճախորդ', 'label_id' => 'premission8'],
                            ['id' => 9, 'name' => 'Ստեղծել անվանակարգ', 'label_id' => 'premission9'],
                            ['id' => 10, 'name' => 'Փոփոխել անվանակարգ', 'label_id' => 'premission10'],
                            ['id' => 11, 'name' => 'Ջնջել անվանակարգ', 'label_id' => 'premission11'],
                            ['id' => 12, 'name' => 'Տեսնել անվանակարգ', 'label_id' => 'premission12'],
                            ['id' => 13, 'name' => 'Ստեղծել օգտատեր', 'label_id' => 'premission13'],
                            ['id' => 14, 'name' => 'Փոփոխել օգտատեր', 'label_id' => 'premission14'],
                            ['id' => 15, 'name' => 'Ջնջել օգտատեր', 'label_id' => 'premission15'],
                            ['id' => 16, 'name' => 'Տեսնել օգտատեր', 'label_id' => 'premission16'],
                            ['id' => 17, 'name' => 'Ստեղծել ապրանք', 'label_id' => 'premission17'],
                            ['id' => 18, 'name' => 'Փոփոխել ապրանք', 'label_id' => 'premission18'],
                            ['id' => 19, 'name' => 'Ջնջել ապրանք', 'label_id' => 'premission19'],
                            ['id' => 20, 'name' => 'Տեսնել ապրանք', 'label_id' => 'premission20'],
                            ['id' => 21, 'name' => 'Ստեղծել վաճառք', 'label_id' => 'premission21'],
                            ['id' => 22, 'name' => 'Փոփոխել վաճառք', 'label_id' => 'premission22'],
                            ['id' => 23, 'name' => 'Ջնջել վաճառք', 'label_id' => 'premission23'],
                            ['id' => 24, 'name' => 'Տեսնել վաճառք', 'label_id' => 'premission24'],
                            ['id' => 25, 'name' => 'Ստեղծել տեղեկամատյան', 'label_id' => 'premission25'],
                            ['id' => 26, 'name' => 'Փոփոխել տեղեկամատյան', 'label_id' => 'premission26'],
                            ['id' => 27, 'name' => 'Ջնջել տեղեկամատյան', 'label_id' => 'premission27'],
                            ['id' => 28, 'name' => 'Տեսնել տեղեկամատյան', 'label_id' => 'premission28'],
                            ['id' => 29, 'name' => 'Ստեղծել կարգավիճակ', 'label_id' => 'premission29'],
                            ['id' => 30, 'name' => 'Փոփոխել կարգավիճակ', 'label_id' => 'premission30'],
                            ['id' => 31, 'name' => 'Ջնջել կարգավիճակ', 'label_id' => 'premission31'],
                            ['id' => 32, 'name' => 'Տեսնել կարգավիճակ', 'label_id' => 'premission32'],
                            ['id' => 33, 'name' => 'Ստեղծել թույլտվություն', 'label_id' => 'premission33'],
                            ['id' => 34, 'name' => 'Փոփոխել թույլտվություն', 'label_id' => 'premission34'],
                            ['id' => 35, 'name' => 'Ջնջել թույլտվություն', 'label_id' => 'premission35'],
                            ['id' => 36, 'name' => 'Տեսնել թույլտվություն', 'label_id' => 'premission36'],
                            ['id' => 37, 'name' => 'Ստեղծել փաստաթուղթ', 'label_id' => 'premission37'],
                            ['id' => 38, 'name' => 'Փոփոխել փաստաթուղթ', 'label_id' => 'premission38'],
                            ['id' => 39, 'name' => 'Ջնջել փաստաթուղթ', 'label_id' => 'premission39'],
                            ['id' => 40, 'name' => 'Տեսնել փաստաթուղթ', 'label_id' => 'premission40'],
                            ['id' => 41, 'name' => 'Ստեղծել զեղչ', 'label_id' => 'premission41'],
                            ['id' => 42, 'name' => 'Փոփոխել զեղչ', 'label_id' => 'premission42'],
                            ['id' => 43, 'name' => 'Ջնջել զեղչ', 'label_id' => 'premission43'],
                            ['id' => 44, 'name' => 'Տեսնել զեղչ', 'label_id' => 'premission44'],
                            ['id' => 45, 'name' => 'Ստեղծել փոխարժեք', 'label_id' => 'premission45'],
                            ['id' => 46, 'name' => 'Փոփոխել փոխարժեք', 'label_id' => 'premission46'],
                            ['id' => 47, 'name' => 'Ջնջել փոխարժեք', 'label_id' => 'premission47'],
                            ['id' => 48, 'name' => 'Տեսնել փոխարժեք', 'label_id' => 'premission48'],
                            ['id' => 49, 'name' => 'Ստեղծել երթուղի', 'label_id' => 'premission49'],
                            ['id' => 50, 'name' => 'Փոփոխել երթուղի', 'label_id' => 'premission50'],
                            ['id' => 51, 'name' => 'Ջնջել երթուղի', 'label_id' => 'premission51'],
                            ['id' => 52, 'name' => 'Տեսնել երթուղի', 'label_id' => 'premission52'],
                            ['id' => 53, 'name' => 'Ստեղծել քարտեզ', 'label_id' => 'premission53'],
                            ['id' => 54, 'name' => 'Տեսնել երթուղու դասավորություն', 'label_id' => 'premission54'],
                            ['id' => 55, 'name' => 'Հաստատել առաքումը', 'label_id' => 'premission55'],
                            ['id' => 56, 'name' => 'Հասանելիություն ֆայլերին', 'label_id' => 'premission56'],
                            ['id' => 57, 'name' => 'Տեսնել վահանակը', 'label_id' => 'premission57'],
                            ['id' => 58, 'name' => 'Ստեղծել խմբեր', 'label_id' => 'premission58'],
                            ['id' => 59, 'name' => 'Փոփոխել խմբեր', 'label_id' => 'premission59'],
                            ['id' => 60, 'name' => 'Ջնջել խմբեր', 'label_id' => 'premission60'],
                            ['id' => 61, 'name' => 'Տեսնել խմբեր', 'label_id' => 'premission61'],
                            ['id' => 62, 'name' => 'Ստեղծել վճարներ', 'label_id' => 'premission62'],
                            ['id' => 63, 'name' => 'Փոփոխել վճարներ', 'label_id' => 'premission63'],
                            ['id' => 64, 'name' => 'Ջնջել վճարներ', 'label_id' => 'premission64'],
                            ['id' => 65, 'name' => 'Տեսնել վճարներ', 'label_id' => 'premission65'],
                            ['id' => 66, 'name' => 'Տեսնել վիճակագրություն', 'label_id' => 'premission66'],
                            ['id' => 67, 'name' => 'Տեսնել փնտրվածը', 'label_id' => 'premission67'],
                            ['id' => 68, 'name' => 'Տեսնել պատվերների վճարները', 'label_id' => 'premission68'],
                            ['id' => 69, 'name' => 'Տեսնել անցած երթուղին', 'label_id' => 'premission69'],
                            ['id' => 70, 'name' => 'Ստեղծել հաճախորդի դաշտ', 'label_id' => 'premission70'],
                            ['id' => 71, 'name' => 'Ստեղծել փաստաթղթի դաշտ', 'label_id' => 'premission71'],
                            ['id' => 72, 'name' => 'Ստեղծել անվանակարգի դաշտ', 'label_id' => 'premission72'],
                            ['id' => 73, 'name' => 'Ստեղծել պահեստի դաշտ', 'label_id' => 'premission73'],
                            ['id' => 74, 'name' => 'Ստեղծել օգտատիրոջ դաշտ', 'label_id' => 'premission74'],

                        ];

                        foreach ($permissions as $permission) {
                            $permissionId = $permission['id'];
                            $permissionName = $permission['name'];
                            $label_id = $permission['label_id'];
                            ?>
                            <div class="premission-content-items">
                                <label for="<?=$label_id?>" class="items-title"><?php echo $permissionName; ?></label>
                                <input id="<?=$label_id?>" type="checkbox" <?php echo (in_array($permissionId, array_column($user_premission_select, 'premission_id'))) ? 'checked' : ''; ?> value="<?php echo $permissionId; ?>" name="premission[]">
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } else{ ?>
                <div class="premission-content">
                    <div class="rows-checkbox">
                        <div class="premission-content-items">
                            <label for="premission1" class="items-title">Ստեղծել պահեստ</label>
                            <input id="premission1" type="checkbox" value="1" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission2" class="items-title">Փոփոխել պահեստ</label>
                            <input id="premission2" type="checkbox" value="2" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission3" class="items-title">Ջնջել պահեստ</label>
                            <input id="premission3" type="checkbox" value="3" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission4" class="items-title">Տեսնել պահեստ</label>
                            <input id="premission4" type="checkbox" value="4" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission5" class="items-title">Ստեղծել հաճախորդ</label>
                            <input id="premission5" type="checkbox" value="5" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission6" class="items-title">Փոփոխել հաճախորդ</label>
                            <input id="premission6" type="checkbox" value="6" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission7" class="items-title">Ջնջել հաճախորդ</label>
                            <input id="premission7" type="checkbox" value="7" name="premission[]">
                        </div>
                    </div>
                    <div class="rows-checkbox">
                        <div class="premission-content-items">
                            <label for="premission8" class="items-title">Տեսնել հաճախորդ</label>
                            <input id="premission8" type="checkbox" value="8" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission9" class="items-title">Ստեղծել անվանակարգ</label>
                            <input id="premission9" type="checkbox" value="9" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission10" class="items-title">Փոփոխել անվանակարգ</label>
                            <input id="premission10" type="checkbox" value="10" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission11" class="items-title">Ջնջել անվանակարգ</label>
                            <input id="premission11" type="checkbox" value="11" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission12" class="items-title">Տեսնել անվանակարգ</label>
                            <input id="premission12" type="checkbox" value="12" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission13" class="items-title">Ստեղծել օգտատեր</label>
                            <input id="premission13" type="checkbox" value="13" name="premission[]">
                        </div>
                    </div>
                    <div class="rows-checkbox">
                        <div class="premission-content-items">
                            <label for="premission14" class="items-title">Փոփոխել օգտատեր</label>
                            <input id="premission14" type="checkbox" value="14" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission15" class="items-title">Ջնջել օգտատեր</label>
                            <input id="premission15" type="checkbox"  value="15" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission16" class="items-title">Տեսնել օգտատեր</label>
                            <input id="premission16" type="checkbox" value="16" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission17" class="items-title">Ստեղծել ապրանք</label>
                            <input id="premission17" type="checkbox" value="17" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission18" class="items-title">Փոփոխել ապրանք</label>
                            <input id="premission18" type="checkbox" value="18" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission19" class="items-title">Ջնջել ապրանք</label>
                            <input id="premission19" type="checkbox" value="19" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission20" class="items-title">Տեսնել ապրանք</label>
                            <input id="premission20" type="checkbox" value="20" name="premission[]">
                        </div>
                    </div>
                    <div class="rows-checkbox">
                        <div class="premission-content-items">
                            <label for="premission21" class="items-title">Ստեղծել վաճառք</label>
                            <input id="premission21" type="checkbox" value="21" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission22" class="items-title">Փոփոխել վաճառք</label>
                            <input id="premission22" type="checkbox" value="22" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission23" class="items-title">Ջնջել վաճառք</label>
                            <input id="premission23" type="checkbox" value="23" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission24" class="items-title">Տեսնել վաճառք</label>
                            <input id="premission24" type="checkbox" value="24" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission25" class="items-title">Ստեղծել տեղեկամատյան</label>
                            <input id="premission25" type="checkbox" value="25" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission26" class="items-title">Փոփոխել տեղեկամատյան</label>
                            <input id="premission26" type="checkbox" value="26" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission27" class="items-title">Ջնջել տեղեկամատյան</label>
                            <input id="premission27" type="checkbox" value="27" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission28" class="items-title">Տեսնել տեղեկամատյան</label>
                            <input id="premission28" type="checkbox" value="28" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission29" class="items-title">Ստեղծել կարգավիճակ</label>
                            <input id="premission29" type="checkbox" value="29" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission30" class="items-title">Փոփոխել կարգավիճակ</label>
                            <input id="premission30" type="checkbox" value="30" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission31" class="items-title">Ջնջել կարգավիճակ</label>
                            <input id="premission31" type="checkbox" value="31" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission32" class="items-title">Տեսնել կարգավիճակ</label>
                            <input id="premission32" type="checkbox" value="32" name="premission[]">
                        </div>
                    </div>
                    <div class="rows-checkbox">
                        <div class="premission-content-items">
                            <label for="premission33" class="items-title">Ստեղծել թույլտվություն</label>
                            <input id="premission33" type="checkbox" value="33" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission34" class="items-title">Փոփոխել թույլտվություն</label>
                            <input id="premission34" type="checkbox" value="34" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission35" class="items-title">Ջնջել թույլտվություն</label>
                            <input id="premission35" type="checkbox" value="35" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission36" class="items-title">Տեսնել թույլտվություն</label>
                            <input id="premission36" type="checkbox" value="36" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission37" class="items-title">Ստեղծել փաստաթուղթ</label>
                            <input id="premission37" type="checkbox" value="37" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission38" class="items-title">Փոփոխել փաստաթուղթ</label>
                            <input id="premission38" type="checkbox" value="38" name="premission[]">
                        </div>
                    </div>
                    <div class="rows-checkbox">
                        <div class="premission-content-items">
                            <label for="premission39" class="items-title">Ջնջել փաստաթուղթ</label>
                            <input id="premission39" type="checkbox" value="39" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission40" class="items-title">Տեսնել փաստաթուղթ</label>
                            <input id="premission40" type="checkbox" value="40" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission41" class="items-title">Ստեղծել զեղչ</label>
                            <input id="premission41" type="checkbox" value="41" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission42" class="items-title">Փոփոխել զեղչ</label>
                            <input id="premission42" type="checkbox" value="42" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission43" class="items-title">Ջնջել զեղչ</label>
                            <input id="premission43" type="checkbox" value="43" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission44" class="items-title">Տեսնել զեղչ</label>
                            <input id="premission44" type="checkbox" value="44" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission45" class="items-title">Ստեղծել փոխարժեք</label>
                            <input id="premission45" type="checkbox" value="45" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission46" class="items-title">Փոփոխել փոխարժեք</label>
                            <input id="premission46" type="checkbox" value="46" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission47" class="items-title">Ջնջել փոխարժեք</label>
                            <input id="premission47" type="checkbox" value="47" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission48" class="items-title">Տեսնել փոխարժեք</label>
                            <input id="premission48" type="checkbox" value="48" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission49" class="items-title">Ստեղծել երթուղի</label>
                            <input id="premission49" type="checkbox" value="49" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission50" class="items-title">Փոփոխել երթուղի</label>
                            <input id="premission50" type="checkbox" value="50" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission51" class="items-title">Ջնջել երթուղի</label>
                            <input id="premission51" type="checkbox" value="51" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission52" class="items-title">Տեսնել երթուղի</label>
                            <input id="premission52" type="checkbox" value="52" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission53" class="items-title">Տեսնել քարտեզ</label>
                            <input id="premission53" type="checkbox" value="53" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission54" class="items-title">Տեսնել երթուղու դասավորություն</label>
                            <input id="premission54" type="checkbox" value="54" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission55" class="items-title">Հաստատել առաքումը</label>
                            <input id="premission55" type="checkbox" value="55" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission56" class="items-title">Հասանելիություն ֆայլերին</label>
                            <input id="premission56" type="checkbox" value="56" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission57" class="items-title">Տեսնել վահանակը</label>
                            <input id="premission57" type="checkbox" value="57" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission58" class="items-title">Ստեղծել խմբեր</label>
                            <input id="premission58" type="checkbox" value="58" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission59" class="items-title">Փոփոխել խմբեր</label>
                            <input id="premission59" type="checkbox" value="59" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission60" class="items-title">Ջնջել խմբեր</label>
                            <input id="premission60" type="checkbox" value="60" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission61" class="items-title">Տեսնել խմբեր</label>
                            <input id="premission61" type="checkbox" value="61" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission62" class="items-title">Ստեղծել վճարներ</label>
                            <input id="premission62" type="checkbox" value="62" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission63" class="items-title">Փոփոխել վճարներ</label>
                            <input id="premission63" type="checkbox" value="63" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission64" class="items-title">Ջնջել վճարներ</label>
                            <input id="premission64" type="checkbox" value="64" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission65" class="items-title">Տեսնել վճարներ</label>
                            <input id="premission65" type="checkbox" value="65" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission66" class="items-title">Տեսնել վիճակագրություն</label>
                            <input id="premission66" type="checkbox" value="66" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission67" class="items-title">Տեսնել փնտրվածը</label>
                            <input id="premission67" type="checkbox" value="67" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission68" class="items-title">Տեսնել պատվերների վճարները</label>
                            <input id="premission68" type="checkbox" value="68" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission69" class="items-title">Տեսնել անցած երթուղին</label>
                            <input id="premission69" type="checkbox" value="69" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission70" class="items-title">Ստեղծել հաճախորդի դաշտ</label>
                            <input id="premission70" type="checkbox" value="70" name="premission[]">
                        </div
                        <div class="premission-content-items">
                            <label for="premission71" class="items-title">Ստեղծել փաստաթղթի դաշտ</label>
                            <input id="premission71" type="checkbox" value="71" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission72" class="items-title">Ստեղծել անվանակարգի դաշտ</label>
                            <input id="premission72" type="checkbox" value="72" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission73" class="items-title">Ստեղծել պահեստի դաշտ</label>
                            <input id="premission73" type="checkbox" value="73" name="premission[]">
                        </div>
                        <div class="premission-content-items">
                            <label for="premission74" class="items-title">Ստեղծել օգտատիրոջ դաշտ</label>
                            <input id="premission74" type="checkbox" value="74" name="premission[]">
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