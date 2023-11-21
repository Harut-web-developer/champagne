<?php

use app\models\Nomenclature;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\NomenclatureSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Nomenclatures';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="nomenclature-index">
    <h1><?= Html::encode($this->title) ?> <?= Html::a('', ['create-fields'], ['class' => 'bx bx-cog right-btn']) ?></h1>
    <p>
        <?= Html::a('Create Nomenclature', ['create'], ['class' => 'btn rounded-pill btn-secondary']) ?>
    </p>
    <div class="card">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'name',
                'price',
                [
                    'header' => 'Actions',
                    'class' => ActionColumn::className(),
                    'template' => '{update} {delete}',
                    'urlCreator' => function ($action, Nomenclature $model, $key, $index, $column) {
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
