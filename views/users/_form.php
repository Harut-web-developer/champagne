<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Users $model */
/** @var yii\widgets\ActiveForm $form */
?>
<?php
if (isset($model->id)){
    ?>
    <div class="users-form">
        <div class="card card-primary">
            <?php $form = ActiveForm::begin(); ?>
            <div class="card-body formDesign">
                <div class="form-group col-md-5 col-lg-3 col-sm-6 usersName">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="form-group col-md-5 col-lg-3 col-sm-6 usersUsername">
                    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
                </div>
                <div>
                    <label for="role">Role</label>
                    <select name="role_id" id="role"  class="form-control col-md-5 col-lg-3 col-sm-6 usersRole" required>
                        <option value="">choose role</option>
                        <option value="admin" <?php echo ($role['role_id'] === 'admin') ? 'selected' : ''?>>Admin</option>
                        <option value="manager" <?php echo ($role['role_id'] === 'manager') ? 'selected' : ''?>>Manager</option>
                        <option value="deliver" <?php echo ($role['role_id'] === 'deliver') ? 'selected' : ''?>>Deliver</option>
                        <option value="storekeeper" <?php echo ($role['role_id'] === 'storekeeper') ? 'selected' : ''?>>Storekeeper</option>
                    </select>
                </div>
                <div class="form-group col-md-5 col-lg-3 col-sm-6 usersPassword">
                    <?= $form->field($model, 'password')->passwordInput() ?>
                </div>
            </div>
            <div class="card-footer">
                <?= Html::submitButton('Save', ['class' => 'btn rounded-pill btn-secondary']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
<?php
}else{
    ?>
    <div class="users-form">
        <div class="card card-primary">
            <?php $form = ActiveForm::begin(); ?>
            <div class="card-body formDesign">
                <div class="form-group col-md-5 col-lg-3 col-sm-6 usersName">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="form-group col-md-5 col-lg-3 col-sm-6 usersUsername">
                    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
                </div>
                <div>
                    <label for="role">Role</label>
                    <select name="role_id" id="role"  class="form-control col-md-5 col-lg-3 col-sm-6 usersRole" required>
                        <option value="">choose role</option>
                        <option value="admin">Admin</option>
                        <option value="manager">Manager</option>
                        <option value="deliver">Deliver</option>
                        <option value="storekeeper">Storekeeper</option>
                    </select>
                </div>
                <div class="form-group col-md-5 col-lg-3 col-sm-6 usersPassword">
                    <?= $form->field($model, 'password')->passwordInput() ?>
                </div>
            </div>
            <div class="card-footer">
                <?= Html::submitButton('Save', ['class' => 'btn rounded-pill btn-secondary']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
<?php
}
?>

