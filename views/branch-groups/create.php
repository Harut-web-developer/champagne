<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\BranchGroups $model */

$this->title = 'Ստեղծել մասնաճուղի խումբ';
$this->params['breadcrumbs'][] = ['label' => 'Branch Groups', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['sub_page'] = $sub_page;
$this->params['date_tab'] = $date_tab;
?>
<div class="branch-groups-create">

    <h5><?= Html::encode($this->title) ?></h5>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
