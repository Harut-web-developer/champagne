<?php

use app\models\Users;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\UsersSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;
$have_access_create = Users::checkPremission(13);
$have_access_update = Users::checkPremission(14);
$have_access_delete = Users::checkPremission(15);
$action_column = [];
if ($have_access_update && $have_access_delete){
    $action_column[] = [
        'header' => 'Actions',
        'class' => ActionColumn::className(),
        'template' => '{update} {delete}',
        'urlCreator' => function ($action, Users $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        }
    ];
} else if($have_access_update){
    $action_column[] = [
        'header' => 'Actions',
        'class' => ActionColumn::className(),
        'template' => '{update}',
        'urlCreator' => function ($action, Users $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        }
    ];
}else if($have_access_delete){
    $action_column[] = [
        'header' => 'Actions',
        'class' => ActionColumn::className(),
        'template' => '{delete}',
        'urlCreator' => function ($action, Users $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        }
    ];
}
?>
<div class="users-index">
    <h1><?= Html::encode($this->title) ?> <?= Html::a('', ['create-fields'], ['class' => 'bx bx-cog right-btn']) ?></h1>
    <p>
        <?php if($have_access_create){ ?>
            <?= Html::a('Create Users', ['create'], ['class' => 'btn rounded-pill btn-secondary']) ?>
        <?php } ?>
    </p>
    <div class="card">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
            'username',
            [
                'attribute' => 'Role',
                'value' => function ($model) {
                    if ($model->roleName) {
                        return $model->roleName->name;
                    } else {
                        return 'empty';
                    }
                }
            ],
            ...$action_column,
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
