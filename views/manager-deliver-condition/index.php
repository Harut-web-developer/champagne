<?php

use app\models\ManagerDeliverCondition;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\ManagerDeliverConditionSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Մենեջերին կցված առաքիչներ';
$this->params['breadcrumbs'][] = $this->title;
$this->params['sub_page'] = $sub_page;
$this->params['date_tab'] = $date_tab;?>
<div class="manager-deliver-condition-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Ստեղծել մենեջեր-առաքիչ կապ', ['create'], ['class' => 'btn rounded-pill btn-secondary']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <div class="card">

        <?= GridView::widget([
            'summary' => 'Ցուցադրված է <b>{totalCount}</b>-ից <b>{begin}-{end}</b>-ը',
            'summaryOptions' => ['class' => 'summary'],
            'dataProvider' => new ActiveDataProvider([
                'query' => $dataProvider->query->andWhere(['status' => '1']),
            ]),
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
                    'class' => ActionColumn::className(),
                    'urlCreator' => function ($action, ManagerDeliverCondition $model, $key, $index, $column) {
                        return Url::toRoute([$action, 'id' => $model->id]);
                     }
                ],
            ],
        ]); ?>

    </div>
</div>
