<?php

use app\models\Warehouse;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\WarehouseSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Warehouses';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="warehouse-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('Create Warehouse', ['create'], ['class' => 'btn rounded-pill btn-secondary']) ?>
    </p>
    <div class="card">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
        //        'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                    'name',
                    'type',
                [
                     'header' => 'Actions',
                    'class' => ActionColumn::className(),
                    'template' => '{update} {delete}',
                    'urlCreator' => function ($action, Warehouse $model, $key, $index, $column) {
                        return Url::toRoute([$action, 'id' => $model->id]);
                    }
                ],
            ],
            'dataProvider' => new ActiveDataProvider([
                'query' => $dataProvider->query->andWhere(['status' => '1']),
//                'pagination' => [
//                    'pageSize' => 20,
//                ],
            ]),
        ]); ?>
    </div>
</div>
