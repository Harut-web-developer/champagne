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
use app\widgets\CustomGridView;


/** @var yii\web\View $this */
/** @var app\models\OrdersSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Վաճառքներ';
$this->params['breadcrumbs'][] = $this->title;
$this->params['sub_page'] = $sub_page;
$this->params['date_tab'] = $date_tab;


$have_access_create = Users::checkPremission(21);
$have_access_update = Users::checkPremission(22);
$have_access_delete = Users::checkPremission(23);
$have_access_delivered = Users::checkPremission(55);
$have_access_available = Users::checkPremission(56);
$action_column = [];
if ($approved == 2){
    $btn = '{reports}';
    $btn1 = '';
    $btn2 = '{reports}';
    $btn3 = '{reports}';
    $btn4 = '{reports}';
    $btn5 = '';
    $btn6 = '';
    $btn7 = '{reports}';
    $btn8 = '';
    $btn9 = '{reports}';
    $btn10 = '{reports}';
    $btn11 = '';
    $btn12 = '{reports}';
    $btn13 = '';
    $btn14 = '';
}elseif ($approved == 0){
    $btn = '{reports} {delivered} {update}';
    $btn1 = '{update} {delivered}';
    $btn2 = '{reports} {delivered} {update}';
    $btn3 = '{reports} {update}';
    $btn4 = '{reports} {delivered}';
    $btn5 = '{delivered} {update}';
    $btn6 = '{update}';
    $btn7 = '{reports} {update}';
    $btn8 = '{delivered}';
    $btn9 = '{reports}  {delivered}';
    $btn10 = '{reports}';
    $btn11 = '{update}';
    $btn12 = '{reports}';
    $btn13 = '';
    $btn14 = '{delivered}';
}elseif ($approved == 1 || $approved == 3 || $approved == 4){
    $btn = '{reports} {delivered} {update} {delete}';
    $btn1 = '{update} {delivered}';
    $btn2 = '{reports} {delivered} {update}';
    $btn3 = '{reports} {update} {delete}';
    $btn4 = '{reports} {delivered} {delete}';
    $btn5 = '{delivered} {update}';
    $btn6 = '{update} {delete}';
    $btn7 = '{reports} {update}';
    $btn8 = '{delivered} {delete}';
    $btn9 = '{reports}  {delivered}';
    $btn10 = '{reports} {delete}';
    $btn11 = '{update}';
    $btn12 = '{reports}';
    $btn13 = '{delete}';
    $btn14 = '{delivered}';
}
if ($have_access_update && $have_access_delete && $have_access_delivered && $have_access_available){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => $btn,
        'buttons' => [
            'delivered' => function ($url, $model, $key) {
                return Html::a('<i class="bx bxs-check-circle" style="color:#0f5132" ></i>', $url, [
                    'title' => Yii::t('yii', 'delivered'), // Add a title if needed
                ]);
            },
            'reports' => function ($url, $model, $key) {
                return Html::a('<img width="22" height="21" src="https://img.icons8.com/material-rounded/24/export-excel.png" alt="export-excel"/>', $url, [
                    'title' => Yii::t('yii', 'reports'),
                    'target' => '_blank',
                ]);
            },
        ],
        'urlCreator' => function ($action, Orders $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        }
    ];
} else if($have_access_update && $have_access_delivered && $have_access_delete){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => $btn1,
        'buttons' => [
            'delivered' => function ($url, $model, $key) {
                return Html::a('<i class="bx bxs-check-circle" style="color:#0f5132" ></i>', $url, [
                    'title' => Yii::t('yii', 'delivered'), // Add a title if needed
                ]);
            },
        ],
        'urlCreator' => function ($action, Orders $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        }
    ];
}else if($have_access_update && $have_access_delivered && $have_access_available){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => $btn2,
        'buttons' => [
            'delivered' => function ($url, $model, $key) {
                return Html::a('<i class="bx bxs-check-circle" style="color:#0f5132" ></i>', $url, [
                    'title' => Yii::t('yii', 'delivered'), // Add a title if needed
                ]);
            },
            'reports' => function ($url, $model, $key) {
                return Html::a('<img width="22" height="21" src="https://img.icons8.com/material-rounded/24/export-excel.png" alt="export-excel"/>', $url, [
                    'title' => Yii::t('yii', 'reports'),
                    'target' => '_blank',
                ]);
            },
        ],
        'urlCreator' => function ($action, Orders $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        }
    ];
}else if($have_access_update && $have_access_delete && $have_access_available){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => $btn3,
        'buttons' => [
            'reports' => function ($url, $model, $key) {
                return Html::a('<img width="22" height="21" src="https://img.icons8.com/material-rounded/24/export-excel.png" alt="export-excel"/>', $url, [
                    'title' => Yii::t('yii', 'reports'),
                    'target' => '_blank',
                ]);
            },
        ],
        'urlCreator' => function ($action, Orders $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        }
    ];
}else if($have_access_delivered && $have_access_delete && $have_access_available){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => $btn4,
        'buttons' => [
            'delivered' => function ($url, $model, $key) {
                return Html::a('<i class="bx bxs-check-circle" style="color:#0f5132" ></i>', $url, [
                    'title' => Yii::t('yii', 'delivered'), // Add a title if needed
                ]);
            },
            'reports' => function ($url, $model, $key) {
                return Html::a('<img width="22" height="21" src="https://img.icons8.com/material-rounded/24/export-excel.png" alt="export-excel"/>', $url, [
                    'title' => Yii::t('yii', 'reports'),
                    'target' => '_blank',
                ]);
            },
        ],
        'urlCreator' => function ($action, Orders $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        }
    ];
}else if($have_access_update && $have_access_delivered){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => $btn5,
        'buttons' => [
            'delivered' => function ($url, $model, $key) {
                return Html::a('<i class="bx bxs-check-circle" style="color:#0f5132" ></i>', $url, [
                    'title' => Yii::t('yii', 'delivered'), // Add a title if needed
                ]);
            },
        ],
        'urlCreator' => function ($action, Orders $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        }
    ];
}else if($have_access_update && $have_access_delete){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => $btn6,
        'urlCreator' => function ($action, Orders $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        }
    ];
}else if($have_access_update && $have_access_available){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => $btn7,
        'buttons' => [
            'reports' => function ($url, $model, $key) {
                return Html::a('<img width="22" height="21" src="https://img.icons8.com/material-rounded/24/export-excel.png" alt="export-excel"/>', $url, [
                    'title' => Yii::t('yii', 'reports'),
                    'target' => '_blank',
                ]);
            },
        ],
        'urlCreator' => function ($action, Orders $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        }
    ];
}else if($have_access_delivered && $have_access_delete){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => $btn8,
        'buttons' => [
            'delivered' => function ($url, $model, $key) {
                return Html::a('<i class="bx bxs-check-circle" style="color:#0f5132" ></i>', $url, [
                    'title' => Yii::t('yii', 'delivered'), // Add a title if needed
                ]);
            },
        ],
        'urlCreator' => function ($action, Orders $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        }
    ];
}else if($have_access_delivered && $have_access_available){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => $btn9,
        'buttons' => [
            'delivered' => function ($url, $model, $key) {
                return Html::a('<i class="bx bxs-check-circle" style="color:#0f5132" ></i>', $url, [
                    'title' => Yii::t('yii', 'delivered'), // Add a title if needed
                ]);
            },
            'reports' => function ($url, $model, $key) {
                return Html::a('<img width="22" height="21" src="https://img.icons8.com/material-rounded/24/export-excel.png" alt="export-excel"/>', $url, [
                    'title' => Yii::t('yii', 'reports'),
                    'target' => '_blank',
                ]);
            },
        ],
        'urlCreator' => function ($action, Orders $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        }
    ];
}else if($have_access_available && $have_access_delete){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => $btn10,
        'buttons' => [
            'reports' => function ($url, $model, $key) {
                return Html::a('<img width="22" height="21" src="https://img.icons8.com/material-rounded/24/export-excel.png" alt="export-excel"/>', $url, [
                    'title' => Yii::t('yii', 'reports'),
                    'target' => '_blank',
                ]);
            },
        ],
        'urlCreator' => function ($action, Orders $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        }
    ];
}else if($have_access_update){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => $btn11,
        'urlCreator' => function ($action, Orders $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        }
    ];
}else if($have_access_available){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => $btn12,
        'buttons' => [
            'reports' => function ($url, $model, $key) {
                return Html::a('<img width="22" height="21" src="https://img.icons8.com/material-rounded/24/export-excel.png" alt="export-excel"/>', $url, [
                    'title' => Yii::t('yii', 'reports'),
                    'target' => '_blank',
                ]);
            },
        ],
        'urlCreator' => function ($action, Orders $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        }
    ];
}else if($have_access_delete){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => $btn13,
        'urlCreator' => function ($action, Orders $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        }
    ];
}else if($have_access_delivered){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => $btn14,
        'buttons' => [
            'delivered' => function ($url, $model, $key) {
                return Html::a('<i class="bx bxs-check-circle" style="color:#0f5132" ></i>', $url, [
                    'title' => Yii::t('yii', 'delivered'), // Add a title if needed
                ]);
            },
        ],
        'urlCreator' => function ($action, Orders $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        }
    ];
}
?>

    <?= CustomGridView::widget([
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
            'orders_date',
            ...$action_column,
        ],
    ]); ?>
<!--<div>-->

