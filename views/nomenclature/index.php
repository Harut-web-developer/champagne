<?php

use app\models\Nomenclature;
use app\models\Users;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\NomenclatureSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Անվանակարգ';
$this->params['breadcrumbs'][] = $this->title;
$this->params['sub_page'] = $sub_page;
$have_access_create = Users::checkPremission(9);
$have_access_update = Users::checkPremission(10);
$have_access_delete = Users::checkPremission(11);
$action_column = [];
if ($have_access_update && $have_access_delete){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => '{update} {delete}',
        'urlCreator' => function ($action, Nomenclature $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        }
    ];
} else if($have_access_update){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => '{update}',
        'urlCreator' => function ($action, Nomenclature $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        }
    ];
}else if($have_access_delete){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => '{delete}',
        'urlCreator' => function ($action, Nomenclature $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        }
    ];
}
?>
<div class="nomenclature-index">
    <h1><?= Html::encode($this->title) ?> <?= Html::a('', ['create-fields'], ['class' => 'bx bx-cog right-btn']) ?></h1>
    <p>
        <?php if($have_access_create){ ?>
            <?= Html::a('Ստեղծել անվանակարգ', ['create'], ['class' => 'btn rounded-pill btn-secondary']) ?>
        <?php } ?>
    </p>
    <div class="card">
        <?= GridView::widget([
            'summary' => 'Ցուցադրված է <b>{totalCount}</b>-ից <b>{begin}-{end}</b>-ը',
            'summaryOptions' => ['class' => 'summary'],
            'dataProvider' => new ActiveDataProvider([
                'query' => $dataProvider->query->andWhere(['status' => '1']),
                //                'pagination' => [
                //                    'pageSize' => 20,
                //                ],
            ]),
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'attribute' => 'image',
                    'format' => 'raw',
                    'value' => function($model) {
                        return '<img src="/upload/' . $model->image . '"width="50">';
                    }
                ],
                'name',
                'price',
                'cost',
                ...$action_column,
            ],
        ]); ?>
    </div>
</div>
