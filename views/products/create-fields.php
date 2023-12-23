<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Products $model */

$this->title = 'Ապրանոի դաշտերի կարգավորում';
$this->params['breadcrumbs'][] = ['label' => 'Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['sub_page'] = $sub_page;
?>
<div class="products-create">

    <div class="titleAndPrevPage">
        <i class='bx bxs-log-out iconPrevPage' onclick="window.location = document.referrer"></i>
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?= $this->render('_form_fields', [
        'model' => $model,
        'action__'=>'configure_filds',
    ]) ?>

</div>
