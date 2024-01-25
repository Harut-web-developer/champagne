<?php

use app\models\Route;
use app\models\Users;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use app\widgets\CustomGridView;


/** @var yii\web\View $this */
/** @var app\models\RouteSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Երթուղի';
$this->params['breadcrumbs'][] = $this->title;
$this->params['sub_page'] = $sub_page;
$this->params['date_tab'] = $date_tab;

$have_access_create = Users::checkPremission(49);
$have_access_update = Users::checkPremission(50);
$have_access_delete = Users::checkPremission(51);
$have_access_view = Users::checkPremission(54);
$have_access_shipping = Users::checkPremission(69);

$action_column = [];
if ($have_access_update && $have_access_delete && $have_access_view && $have_access_shipping){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => '{shipping-route} {view} {update} {delete}',

        'urlCreator' => function ($action, Route $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        },
        'buttons' => [
        'view' => function ($url, $model, $key) {
            $url = Url::to(['route/route-sort', 'id' => $model->id]);
            return Html::a(
                '<svg aria-hidden="true" style="display:inline-block;font-size:inherit;height:1em;overflow:visible;vertical-align:-.125em;width:1.125em" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M573 241C518 136 411 64 288 64S58 136 3 241a32 32 0 000 30c55 105 162 177 285 177s230-72 285-177a32 32 0 000-30zM288 400a144 144 0 11144-144 144 144 0 01-144 144zm0-240a95 95 0 00-25 4 48 48 0 01-67 67 96 96 0 1092-71z"></path></svg>',
                $url,
                ['title' => 'View',]
            );
        },
        'shipping-route' => function ($url, $model, $key) {
            $url = Url::to(['route/shipping-route', 'id' => $model->id]);
            return Html::a('<img width="22" height="21" src="https://uxwing.com/wp-content/themes/uxwing/download/location-travel-map/address-icon.png" alt="export-excel"/>',
                $url,
                ['title' => Yii::t('yii', 'shipping-route')]
            );
        },
    ],
    ];
} else if($have_access_update && $have_access_delete && $have_access_view){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => '{view} {update} {delete}',
        'urlCreator' => function ($action, Route $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        },
        'buttons' => [
            'view' => function ($url, $model, $key) {
                $url = Url::to(['route/route-sort', 'id' => $model->id]);
                return Html::a(
                    '<svg aria-hidden="true" style="display:inline-block;font-size:inherit;height:1em;overflow:visible;vertical-align:-.125em;width:1.125em" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M573 241C518 136 411 64 288 64S58 136 3 241a32 32 0 000 30c55 105 162 177 285 177s230-72 285-177a32 32 0 000-30zM288 400a144 144 0 11144-144 144 144 0 01-144 144zm0-240a95 95 0 00-25 4 48 48 0 01-67 67 96 96 0 1092-71z"></path></svg>',
                    $url,
                    ['title' => 'View',]
                );
            },
        ]
    ];
}else if($have_access_view && $have_access_delete && $have_access_shipping){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => '{shipping-route} {view} {delete}',
        'urlCreator' => function ($action, Route $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        },
        'buttons' => [
            'shipping-route' => function ($url, $model, $key) {
                $url = Url::to(['route/shipping-route', 'id' => $model->id]);
                return Html::a('<img width="22" height="21" src="https://uxwing.com/wp-content/themes/uxwing/download/location-travel-map/address-icon.png" alt="export-excel"/>',
                    $url,
                    ['title' => Yii::t('yii', 'shipping-route')]
                );
            },
            'view' => function ($url, $model, $key) {
                $url = Url::to(['route/route-sort', 'id' => $model->id]);
                return Html::a(
                    '<svg aria-hidden="true" style="display:inline-block;font-size:inherit;height:1em;overflow:visible;vertical-align:-.125em;width:1.125em" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M573 241C518 136 411 64 288 64S58 136 3 241a32 32 0 000 30c55 105 162 177 285 177s230-72 285-177a32 32 0 000-30zM288 400a144 144 0 11144-144 144 144 0 01-144 144zm0-240a95 95 0 00-25 4 48 48 0 01-67 67 96 96 0 1092-71z"></path></svg>',
                    $url,
                    ['title' => 'View',]
                );
            },
        ],
    ];
}else if($have_access_view && $have_access_update && $have_access_shipping){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => '{shipping-route} {view} {update}',
        'urlCreator' => function ($action, Route $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        },
        'buttons' => [
            'view' => function ($url, $model, $key) {
                $url = Url::to(['route/route-sort', 'id' => $model->id]);
                return Html::a(
                    '<svg aria-hidden="true" style="display:inline-block;font-size:inherit;height:1em;overflow:visible;vertical-align:-.125em;width:1.125em" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M573 241C518 136 411 64 288 64S58 136 3 241a32 32 0 000 30c55 105 162 177 285 177s230-72 285-177a32 32 0 000-30zM288 400a144 144 0 11144-144 144 144 0 01-144 144zm0-240a95 95 0 00-25 4 48 48 0 01-67 67 96 96 0 1092-71z"></path></svg>',
                    $url,
                    ['title' => 'View',]
                );
            },
            'shipping-route' => function ($url, $model, $key) {
                $url = Url::to(['route/shipping-route', 'id' => $model->id]);
                return Html::a('<img width="22" height="21" src="https://uxwing.com/wp-content/themes/uxwing/download/location-travel-map/address-icon.png" alt="export-excel"/>',
                    $url,
                    ['title' => Yii::t('yii', 'shipping-route')]
                );
            },
        ],
    ];
} else if($have_access_update && $have_access_delete && $have_access_shipping){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => '{shipping-route} {update} {delete}',
        'urlCreator' => function ($action, Route $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        },
        'buttons' => [
            'shipping-route' => function ($url, $model, $key) {
                $url = Url::to(['route/shipping-route', 'id' => $model->id]);
                return Html::a('<img width="22" height="21" src="https://uxwing.com/wp-content/themes/uxwing/download/location-travel-map/address-icon.png" alt="export-excel"/>',
                    $url,
                    ['title' => Yii::t('yii', 'shipping-route')]
                );
            },
        ]
    ];
} else if($have_access_update && $have_access_delete){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => '{update} {delete}',
        'urlCreator' => function ($action, Route $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        },
    ];
}else if($have_access_view && $have_access_delete){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => '{view} {delete}',
        'urlCreator' => function ($action, Route $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        },
        'buttons' => [
            'view' => function ($url, $model, $key) {
                $url = Url::to(['route/route-sort', 'id' => $model->id]);
                return Html::a(
                    '<svg aria-hidden="true" style="display:inline-block;font-size:inherit;height:1em;overflow:visible;vertical-align:-.125em;width:1.125em" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M573 241C518 136 411 64 288 64S58 136 3 241a32 32 0 000 30c55 105 162 177 285 177s230-72 285-177a32 32 0 000-30zM288 400a144 144 0 11144-144 144 144 0 01-144 144zm0-240a95 95 0 00-25 4 48 48 0 01-67 67 96 96 0 1092-71z"></path></svg>',
                    $url,
                    ['title' => 'View',]
                );
            },
        ],
    ];
} else if($have_access_delete && $have_access_shipping){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => '{shipping-route} {delete}',
        'urlCreator' => function ($action, Route $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        },
        'buttons' => [
            'shipping-route' => function ($url, $model, $key) {
                $url = Url::to(['route/shipping-route', 'id' => $model->id]);
                return Html::a('<img width="22" height="21" src="https://uxwing.com/wp-content/themes/uxwing/download/location-travel-map/address-icon.png" alt="export-excel"/>',
                    $url,
                    ['title' => Yii::t('yii', 'shipping-route')]
                );
            },
        ]
    ];
}else if($have_access_view && $have_access_update){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => '{view} {update}',
        'urlCreator' => function ($action, Route $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        },
        'buttons' => [
            'view' => function ($url, $model, $key) {
                $url = Url::to(['route/route-sort', 'id' => $model->id]);
                return Html::a(
                    '<svg aria-hidden="true" style="display:inline-block;font-size:inherit;height:1em;overflow:visible;vertical-align:-.125em;width:1.125em" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M573 241C518 136 411 64 288 64S58 136 3 241a32 32 0 000 30c55 105 162 177 285 177s230-72 285-177a32 32 0 000-30zM288 400a144 144 0 11144-144 144 144 0 01-144 144zm0-240a95 95 0 00-25 4 48 48 0 01-67 67 96 96 0 1092-71z"></path></svg>',
                    $url,
                    ['title' => 'View',]
                );
            },
        ],
    ];
} else if($have_access_update && $have_access_shipping){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => '{shipping-route} {update}',
        'urlCreator' => function ($action, Route $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        },
        'buttons' => [
            'shipping-route' => function ($url, $model, $key) {
                $url = Url::to(['route/shipping-route', 'id' => $model->id]);
                return Html::a('<img width="22" height="21" src="https://uxwing.com/wp-content/themes/uxwing/download/location-travel-map/address-icon.png" alt="export-excel"/>',
                    $url,
                    ['title' => Yii::t('yii', 'shipping-route')]
                );
            },
        ]
    ];
}else if($have_access_view && $have_access_shipping){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => '{shipping-route} {view}',
        'urlCreator' => function ($action, Route $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        },
        'buttons' => [
            'view' => function ($url, $model, $key) {
                $url = Url::to(['route/route-sort', 'id' => $model->id]);
                return Html::a(
                    '<svg aria-hidden="true" style="display:inline-block;font-size:inherit;height:1em;overflow:visible;vertical-align:-.125em;width:1.125em" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M573 241C518 136 411 64 288 64S58 136 3 241a32 32 0 000 30c55 105 162 177 285 177s230-72 285-177a32 32 0 000-30zM288 400a144 144 0 11144-144 144 144 0 01-144 144zm0-240a95 95 0 00-25 4 48 48 0 01-67 67 96 96 0 1092-71z"></path></svg>',
                    $url,
                    ['title' => 'View',]
                );
            },
            'shipping-route' => function ($url, $model, $key) {
                $url = Url::to(['route/shipping-route', 'id' => $model->id]);
                return Html::a('<img width="22" height="21" src="https://uxwing.com/wp-content/themes/uxwing/download/location-travel-map/address-icon.png" alt="export-excel"/>',
                    $url,
                    ['title' => Yii::t('yii', 'shipping-route')]
                );
            },
        ],
    ];
}else if($have_access_view){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => '{view}',
        'urlCreator' => function ($action, Route $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        },
        'buttons' => [
        'view' => function ($url, $model, $key) {
            $url = Url::to(['route/route-sort', 'id' => $model->id]);
            return Html::a(
                '<svg aria-hidden="true" style="display:inline-block;font-size:inherit;height:1em;overflow:visible;vertical-align:-.125em;width:1.125em" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M573 241C518 136 411 64 288 64S58 136 3 241a32 32 0 000 30c55 105 162 177 285 177s230-72 285-177a32 32 0 000-30zM288 400a144 144 0 11144-144 144 144 0 01-144 144zm0-240a95 95 0 00-25 4 48 48 0 01-67 67 96 96 0 1092-71z"></path></svg>',
                $url,
                ['title' => 'View', ]
            );
        },
    ],
    ];
}else if($have_access_shipping){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => '{shipping-route}',
        'urlCreator' => function ($action, Route $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        },
        'buttons' => [
            'shipping-route' => function ($url, $model, $key) {
                $url = Url::to(['route/shipping-route', 'id' => $model->id]);
                return Html::a('<img width="22" height="21" src="https://uxwing.com/wp-content/themes/uxwing/download/location-travel-map/address-icon.png" alt="export-excel"/>',
                    $url,
                    ['title' => Yii::t('yii', 'shipping-route')]
                );
            },
        ],
    ];
}else if($have_access_update){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => '{update}',
        'urlCreator' => function ($action, Route $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        }
    ];
}else if($have_access_delete){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => '{delete}',
        'urlCreator' => function ($action, Route $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        }
    ];
}
?>
<div class="route-index">
    <div class="titleAndPrevPage">
        <i class='bx bxs-log-out iconPrevPage' onclick="window.location = document.referrer"></i>
        <h3><?= Html::encode($this->title) ?></h3>
    </div>
    <p>
        <?php if($have_access_create){ ?>
            <?= Html::a('Ստեղծել երթուղի', ['create'], ['class' => 'btn rounded-pill btn-secondary']) ?>
        <?php } ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <div class="card pageStyle">
    <?= CustomGridView::widget([
        'summary' => 'Ցուցադրված է <b>{totalCount}</b>-ից <b>{begin}-{end}</b>-ը',
        'summaryOptions' => ['class' => 'summary'],
        'dataProvider' => new ActiveDataProvider([
            'query' => $dataProvider->query->andWhere(['status' => '1']),
//                'pagination' => [
//                    'pageSize' => 20,
//                ],
        ]),
//        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'route',
            ...$action_column,
        ],
    ]); ?>

    </div>
</div>
