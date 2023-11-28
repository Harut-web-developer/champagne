<?php

use app\models\Clients;
use app\models\Users;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\ClientsSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Clients';
$this->params['breadcrumbs'][] = $this->title;
$have_access_create = Users::checkPremission(5);
$have_access_update = Users::checkPremission(6);
$have_access_delete = Users::checkPremission(7);
$action_column = [];
if ($have_access_update && $have_access_delete){
    $action_column[] = [
        'header' => 'Actions',
        'class' => ActionColumn::className(),
        'template' => '{update} {delete}',
        'urlCreator' => function ($action, Clients $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        }
    ];
} else if($have_access_update){
    $action_column[] = [
        'header' => 'Actions',
        'class' => ActionColumn::className(),
        'template' => '{update}',
        'urlCreator' => function ($action, Clients $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        }
    ];
}else if($have_access_delete){
    $action_column[] = [
        'header' => 'Actions',
        'class' => ActionColumn::className(),
        'template' => '{delete}',
        'urlCreator' => function ($action, Clients $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        }
    ];
}
?>
<div class="clients-index">
    <h1><?= Html::encode($this->title) ?> <?= Html::a('', ['create-fields'], ['class' => 'bx bx-cog right-btn']) ?></h1>
    <p>
        <?php if($have_access_create){ ?>
            <?= Html::a('Create Clients', ['create'], ['class' => 'btn rounded-pill btn-secondary']) ?>
        <?php } ?>
    </p>
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
            [
                'attribute' => 'route_id',
                'value' => function ($model) {
                    if ($model->routeName) {
                        return $model->routeName->route;
                    } else {
                        return 'empty';
                    }
                }
            ],
//            'route_id',
            'name',
            'location',
            'phone',
            ...$action_column,
        ],
    ]); ?>
    </div>
</div>
