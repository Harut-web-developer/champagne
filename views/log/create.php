<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Log $model */

$this->title = 'Ստեղծել տեղեկամատյան';
$this->params['breadcrumbs'][] = ['label' => 'Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['sub_page'] = $sub_page;
$this->params['date_tab'] = $date_tab;

require_once 'models/Log.php';
?>
<div class="log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'log' => $log,
        'sub_page' => $sub_page
    ]) ?>

</div>
