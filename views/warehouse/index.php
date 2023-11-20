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

$this->title = 'Warehouses';
$this->params['breadcrumbs'][] = $this->title;
$have_access_create = Users::checkPremission(1);
$have_access_update = Users::checkPremission(2);
if ($have_access_update){
    $actions_ = '{update} {delete}';
} else {
    $actions_ = '{delete}';
}

?>
<div class="warehouse-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?php if($have_access_create){ ?>
          <?= Html::a('Create Warehouse', ['create'], ['class' => 'btn rounded-pill btn-secondary']) ?>
        <?php } ?>
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
                    'template' => $actions_,
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
