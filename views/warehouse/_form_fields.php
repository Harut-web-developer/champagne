<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\CustomfieldsBlocksTitle;
use app\models\CustomfieldsBlocksInputs;

/** @var yii\web\View $this */
/** @var app\models\Warehouse $model */
/** @var yii\widgets\ActiveForm $form */
$blocks = CustomfieldsBlocksTitle::find()->where(['page'=>'warehouse','block_type'=>1])->andWhere(['status'=>'1'])->orderBy(['order_number'=>SORT_ASC])->all();
$req = true;
if(isset($action__)){
    $req = false;
}
?>


<div class="warehouse-form">
    <div class="card card-primary">
        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
        <div class="dinamic-form">
            <input type="hidden" name="page" value="warehouse">
            <div class="default-panel" data-id="1" data-page="warehouse">
                <div class="panel-title">
                    <span class="non-active"><?=$model->DefaultTitle->title?></span>
                    <input type="text" name="newblocks[<?php echo $model->DefaultTitle->id;?>]" value="<?=$model->DefaultTitle->title?>"  class="only-active form-control">
                    <button type="button" class="btn btn-default btn-sm edite-block-title" ><i class='bx bx-edit-alt'></i></button>
                    <button type="button" class="btn btn-default btn-sm edite-block-title-save" ><i class='bx bx-save'></i></button>
                </div>
                <?php $fields = CustomfieldsBlocksInputs::find()->where(['iblock_id'=>1])->andWhere(['status'=>'1'])->all(); ?>
                <?php if(!empty($fields)){ ?>
                    <?php foreach ($fields as $fild => $fild_simple){ ?>
                        <?php echo CustomfieldsBlocksInputs::createElement($fild_simple,$model->id, false);?>
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
            <?php if(!empty($blocks)){ ?>
                <?php foreach ($blocks as $block => $block_val){
                    ?>
                    <div class="default-panel"  data-id="<?php echo $block_val->id;?>" data-page="warehouse">
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
                                <?php echo CustomfieldsBlocksInputs::createElement($fild_simple,$model->id, false);?>
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
        <button class="btn btn-default btn-sm create-block" type="button">Ստեղծել բլոկ</button>
        <div class="card-footer">
            <?= Html::submitButton('Պահպանել', ['class' => 'btn rounded-pill btn-secondary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
        <div class="default-panel createable-panel new-panel" data-page="warehouse">
            <div class="panel-title">
                <span class="non-active">Նոր բլոկ</span>
                <input type="text"  value="Նոր բլոկ" name="newblocks[]" class="only-active form-control">
                <button type="button" class="btn btn-default btn-sm edite-block-title-new" ><i class='bx bx-edit-alt'></i></button>
                <button type="button" class="btn btn-default btn-sm edite-block-title-save-new-field" ><i class='bx bx-save'></i></button>
                <button type="button" class="btn btn-default btn-sm edite-block-trash-new" onclick="$(this).closest('.new-panel').remove()"><i class="bx bx-trash"></i></button>
            </div>
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
    </div>
</div>
