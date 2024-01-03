<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\GroupsName $model */

$this->title = 'Ստեղծեք հաճախորդների խմբեր';
$this->params['breadcrumbs'][] = ['label' => 'Groups Names', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['sub_page'] = $sub_page;
$this->params['date_tab'] = $date_tab;
?>
<div class="groups-name-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'clients' => $clients,
    ]) ?>

</div>
