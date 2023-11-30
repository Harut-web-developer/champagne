<?php

use app\models\Users;
use app\models\Warehouse;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\WarehouseSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Պահեստներ';
$this->params['breadcrumbs'][] = $this->title;
$have_access_create = Users::checkPremission(1);
$have_access_update = Users::checkPremission(2);
$have_access_delete = Users::checkPremission(3);
$action_column = [];
if ($have_access_update && $have_access_delete){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => '{update} {delete}',
        'urlCreator' => function ($action, Warehouse $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        }
    ];
} else if($have_access_update){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => '{update}',
        'urlCreator' => function ($action, Warehouse $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        }
    ];
}else if($have_access_delete){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => '{delete}',
        'urlCreator' => function ($action, Warehouse $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        }
    ];
}

?>
<div class="warehouse-index">
    <h1><?= Html::encode($this->title) ?> <?= Html::a('', ['create-fields'], ['class' => 'bx bx-cog right-btn']) ?></h1>
    <p>
        <?php if($have_access_create){ ?>
          <?= Html::a('Ստեղծել պահեստ', ['create'], ['class' => 'btn rounded-pill btn-secondary']) ?>
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
                    'name',
                    'location',
                    'type',
                    ...$action_column,
            ],
        ]); ?>
    </div>
</div>
