<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */

/** @var app\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\Url;
?>



<?php $form = ActiveForm::begin(['id' => 'formAuthentication']); ?>
<div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="text" class="form-control" id="email" name="email" placeholder="Enter your email" autofocus="">
    </div>
    <div class="class="mb-3"">
    <?= Html::submitButton('Send Reset Link', ['class' => 'btn btn-primary d-grid w-100', 'name' => 'reset-button']) ?>
    </div>
<?php ActiveForm::end(); ?>
<div class="text-center">
    <a href="<?= Url::to(['site/login']) ?>" class="d-flex align-items-center justify-content-center">
        <i class="bx bx-chevron-left scaleX-n1-rtl bx-sm"></i>
        Back to login
    </a>
</div>
