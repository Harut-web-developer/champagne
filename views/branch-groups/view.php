<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\BranchGroups $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Branch Groups', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
$this->params['sub_page'] = $sub_page;
$this->params['date_tab'] = $date_tab;
?>
<div class="branch-groups-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
