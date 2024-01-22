<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\GroupsName $model */
/** @var yii\widgets\ActiveForm $form */
?>
<div class="clients-groups-form">
    <div class="card card-primary">
        <?php $form = ActiveForm::begin(); ?>
        <div class="default-panel">
            <div class="panel-title premission">
                <span class="non-active">Հաճախորդների խմբեր</span>
            </div>
            <div class="form-group col-md-12 col-lg-12 col-sm-12 clientName">
                <?= $form->field($model, 'groups_name')->textInput(['maxlength' => true]) ?>
            </div>
            <?php if ($model->id){ ?>
            <div class="form-group col-md-12 col-lg-12 col-sm-12 discount groupName">
                <label for="multipleClients">Հաճախորդ</label>
                <select id="multipleClients" class="js-example-basic-multiple form-control" name="clients[]" multiple="multiple">
                    <?php foreach ($clients as $client){
                        $isSelected = in_array($client['id'], $clients_groups);
                        ?>
                        <option <?= $isSelected ? 'selected' : '' ?> value="<?= $client['id'] ?>"><?= $client['name'] ?></option>
                    <?php } ?>
                </select>
            </div>
            <?php }else{ ?>
            <div class="clientSelect groupName">
                <label for="multipleClients">Հաճախորդ</label>
                <select id="multipleClients" class="js-example-basic-multiple form-control" name="clients[]" multiple="multiple">
                    <?php foreach ($clients as $client){ ?>
                        <option value="<?=$client['id']?>"><?=$client['name']?></option>
                    <?php } ?>
                </select>
            </div>
            <?php } ?>
        </div>
        <div class="card-footer">
            <?= Html::submitButton('Պահպանել', ['class' => 'btn rounded-pill btn-secondary']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
