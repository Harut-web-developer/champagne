<?php

use app\models\Orders;
use app\models\Users;
use kartik\export\ExportMenu;
use yii\bootstrap\Modal;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\OrdersSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Վաճառքներ';
$this->params['breadcrumbs'][] = $this->title;
$this->params['sub_page'] = $sub_page;

$have_access_create = Users::checkPremission(21);
$have_access_update = Users::checkPremission(22);
$have_access_delete = Users::checkPremission(23);
$have_access_delivered = Users::checkPremission(55);
$action_column = [];
if ($approved == 2){
    $btn = '{update} {delete}';
}elseif ($approved == 0){
    $btn = '{delivered} {update}';
}else{
    $btn = '{delivered} {update} {delete}';
}
if ($have_access_update && $have_access_delete && $have_access_delivered){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => '{delivered} {update} {delete}',
        'buttons' => [
            'delivered' => function ($url, $model, $key) {
                // The content of the new template with your SVG icon
                return Html::a('<i class="bx bxs-check-circle" style="color:#0f5132" ></i>', $url, [
                    'title' => Yii::t('yii', 'delivered'), // Add a title if needed
                ]);
            },
        ],
        'urlCreator' => function ($action, Orders $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        }
    ];
} else if($have_access_update && $have_access_delivered){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => '{delivered} {update}',
        'urlCreator' => function ($action, Orders $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        }
    ];
}else if($have_access_delete && $have_access_delivered){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => '{delivered} {delete}',
        'urlCreator' => function ($action, Orders $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        }
    ];
}
else if($have_access_update && $have_access_delete){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => '{update} {delete}',
        'urlCreator' => function ($action, Orders $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        }
    ];
}else if($have_access_delete){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => '{delete}',
        'urlCreator' => function ($action, Orders $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        }
    ];
}else if($have_access_update){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => '{update}',
        'urlCreator' => function ($action, Orders $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        }
    ];
}else if($have_access_delivered){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => '{delivered}',
        'urlCreator' => function ($action, Orders $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        }
    ];
}
?>
<!--<div  data-status="--><?php //= $_GET['numberVal'] ?><!--">-->
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => 'Ցուցադրված է <b>{totalCount}</b>-ից <b>{begin}-{end}</b>-ը',
        'summaryOptions' => ['class' => 'summary'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'Օգտատեր',
                'value' => function ($model) {
                    if ($model->usersName) {
                        return $model->usersName->name;
                    } else {
                        return 'Դատարկ';
                    }
                }
            ],
            [
                'attribute' => 'Հաճախորդ',
                'value' => function ($model) {
                    if ($model->clientsName) {
                        return $model->clientsName->name;
                    } else {
                        return 'Դատարկ';
                    }
                }
            ],
            'comment',
            'total_price',
            'total_count',
            ...$action_column,
        ],
    ]); ?>
<!--<div>-->

