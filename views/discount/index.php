<?php

use app\models\Discount;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\DiscountSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Discounts';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="discount-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('Create Discount', ['create'], ['class' => 'btn rounded-pill btn-secondary']) ?>
    </p>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <div class="card">
    <?= GridView::widget([
        'dataProvider' => new ActiveDataProvider([
            'query' => $dataProvider->query->andWhere(['status' => '1']),
//                'pagination' => [
//                    'pageSize' => 20,
//                ],
        ]),
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'type',
            'discount',
            [
                'attribute' => 'Start date',
                'value' => function ($model) {
                    if ($model->start_date) {
                        return $model->start_date;
                    } else {
                        return 'empty';
                    }
                }
            ],
            [
                'attribute' => 'Start date',
                'value' => function ($model) {
                    if ($model->end_date) {
                        return $model->end_date;
                    } else {
                        return 'empty';
                    }
                }
            ],
            [
                'header' => 'Actions',
                'class' => ActionColumn::className(),
                'template' => '{update} {delete}',
                'urlCreator' => function ($action, Discount $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>
    </div>
</div>
