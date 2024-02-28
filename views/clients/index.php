<?php

use app\models\Clients;
use app\models\Users;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use app\widgets\CustomGridView;

/** @var yii\web\View $this */
/** @var app\models\ClientsSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Հաճախորդներ';
$this->params['breadcrumbs'][] = $this->title;
$this->params['sub_page'] = $sub_page;
$this->params['date_tab'] = $date_tab;

$have_access_create = Users::checkPremission(5);
$have_access_update = Users::checkPremission(6);
$have_access_delete = Users::checkPremission(7);
$have_access_debt_statistic = Users::checkPremission(68);
$have_access_custom_field = Users::checkPremission(70);
$action_column = [];
if ($have_access_update && $have_access_delete && $have_access_debt_statistic){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => '{view} {update} {delete}',
        'urlCreator' => function ($action, Clients $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        },
        'buttons' => [
            'view' => function ($url, $model, $key) {
                $url = Url::to(['clients/clients-debt', 'id' => $model->id]);
                return Html::a(
                    '<svg aria-hidden="true" style="display:inline-block;font-size:inherit;height:1em;overflow:visible;vertical-align:-.125em;width:1.125em" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M573 241C518 136 411 64 288 64S58 136 3 241a32 32 0 000 30c55 105 162 177 285 177s230-72 285-177a32 32 0 000-30zM288 400a144 144 0 11144-144 144 144 0 01-144 144zm0-240a95 95 0 00-25 4 48 48 0 01-67 67 96 96 0 1092-71z"></path></svg>',
                    $url,
                    ['title' => 'View',]
                );
            },
        ],
    ];
} else if($have_access_update && $have_access_debt_statistic){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => '{view} {update}',
        'urlCreator' => function ($action, Clients $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        },
        'buttons' => [
            'view' => function ($url, $model, $key) {
                $url = Url::to(['clients/clients-debt', 'id' => $model->id]);
                return Html::a(
                    '<svg aria-hidden="true" style="display:inline-block;font-size:inherit;height:1em;overflow:visible;vertical-align:-.125em;width:1.125em" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M573 241C518 136 411 64 288 64S58 136 3 241a32 32 0 000 30c55 105 162 177 285 177s230-72 285-177a32 32 0 000-30zM288 400a144 144 0 11144-144 144 144 0 01-144 144zm0-240a95 95 0 00-25 4 48 48 0 01-67 67 96 96 0 1092-71z"></path></svg>',
                    $url,
                    ['title' => 'View',]
                );
            },
        ],
    ];
}else if($have_access_delete && $have_access_debt_statistic){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => '{view} {delete}',
        'urlCreator' => function ($action, Clients $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        },
        'buttons' => [
            'view' => function ($url, $model, $key) {
                $url = Url::to(['clients/clients-debt', 'id' => $model->id]);
                return Html::a(
                    '<svg aria-hidden="true" style="display:inline-block;font-size:inherit;height:1em;overflow:visible;vertical-align:-.125em;width:1.125em" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M573 241C518 136 411 64 288 64S58 136 3 241a32 32 0 000 30c55 105 162 177 285 177s230-72 285-177a32 32 0 000-30zM288 400a144 144 0 11144-144 144 144 0 01-144 144zm0-240a95 95 0 00-25 4 48 48 0 01-67 67 96 96 0 1092-71z"></path></svg>',
                    $url,
                    ['title' => 'View',]
                );
            },
        ],
    ];
}else if($have_access_delete && $have_access_update){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => '{update} {delete}',
        'urlCreator' => function ($action, Clients $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        },
    ];
}else if($have_access_update){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => '{update}',
        'urlCreator' => function ($action, Clients $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        },
    ];
}else if($have_access_delete){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => '{delete}',
        'urlCreator' => function ($action, Clients $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        },
    ];
}else if($have_access_debt_statistic){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => '{view}',
        'urlCreator' => function ($action, Clients $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        },
        'buttons' => [
            'view' => function ($url, $model, $key) {
                $url = Url::to(['clients/clients-debt', 'id' => $model->id]);
                return Html::a(
                    '<svg aria-hidden="true" style="display:inline-block;font-size:inherit;height:1em;overflow:visible;vertical-align:-.125em;width:1.125em" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M573 241C518 136 411 64 288 64S58 136 3 241a32 32 0 000 30c55 105 162 177 285 177s230-72 285-177a32 32 0 000-30zM288 400a144 144 0 11144-144 144 144 0 01-144 144zm0-240a95 95 0 00-25 4 48 48 0 01-67 67 96 96 0 1092-71z"></path></svg>',
                    $url,
                    ['title' => 'View',]
                );
            },
        ],
    ];
}
?>
<div class="clients-index">
    <div class="titleAndPrev">
        <div class="titleAndConfig">
            <i class='bx bxs-log-out iconPrevPage' onclick="window.location = document.referrer"></i>
            <h3><?= Html::encode($this->title) ?></h3>
        </div>
        <?php if($have_access_custom_field){ ?>
            <h3><?= Html::a('', ['create-fields'], ['class' => 'bx bx-cog right-btn']) ?></h3>
        <?php } ?>

    </div>
    <p>
        <?php if($have_access_create){ ?>
            <?= Html::a('Ստեղծել հաճախորդ', ['create'], ['class' => 'btn rounded-pill btn-secondary']) ?>
        <?php } ?>
    </p>
    <div class="card pageStyle">
    <?= CustomGridView::widget([
        'summary' => 'Ցուցադրված է <b>{totalCount}</b>-ից <b>{begin}-{end}</b>-ը',
        'summaryOptions' => ['class' => 'summary'],
        'dataProvider' => new ActiveDataProvider([
            'query' => $dataProvider->query->andWhere(['status' => '1']),
        ]),
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            ...$action_column,
            [
                'attribute' => 'route_id',
                'value' => function ($model) {
                    if ($model->routeName) {
                        return $model->routeName->route;
                    } else {
                        return 'Դատարկ';
                    }
                }
            ],
            [
                'attribute' => 'client_warehouse_id',
                'value' => function ($model) {
                    if ($model->warehouseName) {
                        return $model->warehouseName->name;
                    } else {
                        return 'Դատարկ';
                    }
                }
            ],
            'name',
            'phone',
        ],
    ]); ?>
    </div>
</div>
