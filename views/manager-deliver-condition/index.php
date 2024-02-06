<?php

use app\models\ManagerDeliverCondition;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use app\widgets\CustomGridView;

/** @var yii\web\View $this */
/** @var app\models\ManagerDeliverConditionSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Ստեցծել մենեջեր-առաքիչ';
$this->params['breadcrumbs'][] = $this->title;
$this->params['sub_page'] = $sub_page;
$this->params['date_tab'] = $date_tab;
?>
<div class="manager-deliver-condition-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Ստեցծել մենեջեր-առաքիչ', ['create'], ['class' => 'btn rounded-pill btn-secondary']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <div class="card pageStyle">
    <?= CustomGridView::widget([
        'summary' => 'Ցուցադրված է <b>{totalCount}</b>-ից <b>{begin}-{end}</b>-ը',
        'summaryOptions' => ['class' => 'summary'],
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'manager_id',
                'value' => function ($model) {
                    if ($model->managerName) {
                        return $model->managerName->name;
                    } else {
                        return 'Դատարկ';
                    }
                }
            ],
            [
                    'header' => 'Գործողություն',
                    'class' => ActionColumn::className(),
                    'template' => '{update} {delete}',
                    'urlCreator' => function ($action, ManagerDeliverCondition $model, $key, $index, $column) {
                        return Url::toRoute([$action, 'id' => $model->id]);
                    }
                ],
        ],
    ]); ?>

    </div>
</div>
