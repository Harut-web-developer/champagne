<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */

/** @var app\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\Url;
//$this->params['sub_page'] = $sub_page;

if(isset($_COOKIE['username'])){
    $username = $_COOKIE['username'];
}else{
    $username = '';
}
?>
    <?php $form = ActiveForm::begin(['id' => 'formAuthentication']); ?>
        <div class="mb-3">
            <label for="email" class="form-label">օգտանուն</label>
            <input type="text" class="form-control" value="<?=$username?>" id="email" name="username" placeholder="Մուտքագրեք ձեր էլփոստը կամ օգտվողի անունը" autofocus />
        </div>
        <div class="mb-3 form-password-toggle">
            <div class="d-flex justify-content-between">
                <label class="form-label" for="password">Գաղտնաբառ</label>
            </div>
            <div class="input-group input-group-merge">
                <input type="password" id="password" class="form-control" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" />
                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
            </div>
<!--            <a href="--><?php //= Url::to(['site/forgot-password']) ?><!--">-->
<!--                <small>Մոռացել եք գաղտնաբառը?</small>-->
<!--            </a>-->
        </div>
        <div class="mb-3">
            <div class="form-check">
                <input class="form-check-input" name="remember" type="checkbox" id="remember-me" />
                <label class="form-check-label" for="remember-me"> Հիշիր ինձ </label>
            </div>
        </div>
        <div class="class="mb-3"">
            <?= Html::submitButton('Մուտք գործել', ['class' => 'btn btn-primary d-grid w-100', 'name' => 'login-button']) ?>
        </div>
    <?php ActiveForm::end(); ?>
