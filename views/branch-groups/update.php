<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\BranchGroups $model */

$this->title = 'Փոփոխել մասնաճյուղի խումբ: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Branch Groups', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
$this->params['sub_page'] = $sub_page;
$this->params['date_tab'] = $date_tab;
?>
<div class="branch-groups-update">

    <h5><?= Html::encode($this->title) ?></h5>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
