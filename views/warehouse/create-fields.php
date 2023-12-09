<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Warehouse $model */

$this->title = 'Պահեստի դաշտերի կարգավորում';
$this->params['breadcrumbs'][] = ['label' => 'Warehouse', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['sub_page'] = $sub_page;
?>
<div class="warehouse-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form_fields', [
        'model' => $model,
        'action__'=>'configure_filds',
    ]) ?>

</div>
