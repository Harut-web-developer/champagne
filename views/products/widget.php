<?php

use app\models\Products;
use app\models\Users;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use app\widgets\CustomGridView;


/** @var yii\web\View $this */
/** @var app\models\ProductsSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Ապրանքներ';
$this->params['breadcrumbs'][] = $this->title;
$this->params['sub_page'] = $sub_page;
$this->params['date_tab'] = $date_tab;
$session = Yii::$app->session;

$have_access_create = Users::checkPremission(17);
$have_access_update = Users::checkPremission(18);
$have_access_delete = Users::checkPremission(19);
$action_column = [];
if ($have_access_update && $have_access_delete){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => '{update} {delete}',
        'urlCreator' => function ($action, Products $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        }
    ];
} else if($have_access_update){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => '{update}',
        'urlCreator' => function ($action, Products $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        }
    ];
}else if($have_access_delete){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => '{delete}',
        'urlCreator' => function ($action, Products $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        }
    ];
}
?>

<?= CustomGridView::widget([
    'summary' => 'Ցուցադրված է <b>{totalCount}</b>-ից <b>{begin}-{end}</b>-ը',
    'summaryOptions' => ['class' => 'summary'],
    'dataProvider' => new ActiveDataProvider([
        'query' => $dataProvider->query->andWhere(['status' => '1']),
    ]),
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'attribute' => 'Պահեստ',
            'value' => function ($model) {
                if ($model->warehouseName) {
                    return $model->warehouseName->name;
                } else {
                    return 'Դատարկ';
                }
            }
        ],
        [
            'attribute' => 'Անվանակարգ',
            'value' => function ($model) {
                if ($model->nomenclatureName) {
                    return $model->nomenclatureName->name;
                } else {
                    return 'Դատարկ';
                }
            }
        ],
        [
            'attribute' => 'Քանակ',
            'value' => function ($model) {
                return $model->count;
            }
        ],
        [
            'attribute' => 'Գին',
            'value' => function ($model) {
                return round($model->price);
            }
        ],
    ],
]); ?>