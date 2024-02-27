<?php

use app\models\Documents;
use app\models\Users;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use app\widgets\CustomGridView;

/** @var yii\web\View $this */
/** @var app\models\DocumentsSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Փաստաթուղթ';
$this->params['breadcrumbs'][] = $this->title;
$this->params['sub_page'] = $sub_page;
$this->params['date_tab'] = $date_tab;
$session = Yii::$app->session;

$have_access_create = Users::checkPremission(37);
$have_access_update = Users::checkPremission(38);
$have_access_delete = Users::checkPremission(39);
$have_access_available = Users::checkPremission(56);
$have_access_confirm_return = Users::checkPremission(75);
$have_access_custom_field = Users::checkPremission(71);
//$have_access_confirm_return = Users::checkPremission(75);

$action_column = [];
$access_buttons = '';
if($have_access_delete){
    $access_buttons .='{delete}';
}
if($have_access_confirm_return){
    $access_buttons .='{delivered} {refuse}';
}
if($have_access_update){
    $access_buttons .='{update}';
}
if($have_access_available){
    $access_buttons .='{reports}';
}
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => $access_buttons,
        'buttons' =>[
            'reports'=>function ($url, $model, $key) {
                return Html::a('<img width="22" height="21" src="https://img.icons8.com/material-rounded/24/export-excel.png" alt="export-excel"/>', $url, [
                    'title' => Yii::t('yii', 'Հաշվետվություն'),
                    'class' => 'reportsOrders',
                    'target' => '_blank',
                ]);
            },
            'delivered'=>function ($url, $model, $key) {
                if ($model->document_type == 6){
                    return Html::a('<i class="bx bxs-check-circle" style="color:#0f5132; padding:0px 2px" ></i>', $url, [
                        'title' => Yii::t('yii', 'Հաստատել'),
                    ]);
                }else{
                    return '';
                }
            },
            'refuse'=>function ($url, $model, $key) {
                if ($model->document_type == 6){
                    return '<i class="bx bx-block refuseDocument" data-id="'. $key . '" title="Մերժել" style="color:red; padding:0px 2px"></i>';
                }else{
                    return '';
                }
            },
],
        'urlCreator' => function ($action, Documents $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        }
    ];
?>

<?php  if (!isset($page_value)){
    if(!isset($data_size)){ ?>
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
                'attribute' => 'Փաստաթղթի տեսակ',
                'value' => function ($model) {
                    if ($model->document_type == 1) {
                        return 'Մուտքի';
                    } elseif($model->document_type == 2) {
                        return 'Ելքի';
                    } elseif($model->document_type == 3) {
                        return 'Տեղափոխություն';
                    } elseif($model->document_type == 4) {
                        return 'Խոտան';
                    } elseif($model->document_type == 6) {
                        return 'Վերադարձրած';
                    } elseif($model->document_type == 7) {
                        return 'Մերժված';
                    } elseif($model->document_type == 8){
                        return 'Մուտք(վերադարցրած)';
                    } elseif ($model->document_type == 9){
                        return 'Պատվերից ելքագրված';
                    } elseif ($model->document_type == 10){
                        return 'Ետ վերադարցրած';
                    }
                }
            ],
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
                'attribute' => 'Փոխարժեք',
                'value' => function ($model) {
                    if ($model->rateName) {
                        return $model->rateName->name;
                    } else {
                        return 'Դատարկ';
                    }
                }
            ],
            [
                'attribute' => 'Մեկնաբանություն',
                'value' => function ($model) {
                    if ($model->comment) {
                        return $model->comment;
                    } else {
                        return 'Դատարկ';
                    }
                }
            ],
            [
                'attribute' => 'Ստեցծման ժամանակ',
                'value' => function ($model) {
                    if ($model->date) {
                        return $model->date;
                    } else {
                        return 'Դատարկ';
                    }
                }
            ],
        ],
    ]); ?>
    <?php }
    else{
        $dataProvider->pagination = false; ?>
        <?= CustomGridView::widget([
            'tableOptions' => [
                'class'=>'table chatgbti_',
            ],
            'options' => [
                'class' => 'summary deletesummary'
            ],
            'summary' => 'Ցուցադրված է <b>{totalCount}</b>-ից <b>{begin}-{end}</b>-ը',
            'summaryOptions' => ['class' => 'summary'],
            'dataProvider' => $dataProvider,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'attribute' => 'Փաստաթղթի տեսակ',
                    'value' => function ($model) {
                        if ($model->document_type == 1) {
                            return 'Մուտքի';
                        } elseif($model->document_type == 2) {
                            return 'Ելքի';
                        } elseif($model->document_type == 3) {
                            return 'Տեղափոխություն';
                        } elseif($model->document_type == 4) {
                            return 'Խոտան';
                        } elseif($model->document_type == 6) {
                            return 'Վերադարձրած';
                        } elseif($model->document_type == 7) {
                            return 'Մերժված';
                        } elseif($model->document_type == 8){
                            return 'Մուտք(վերադարցրած)';
                        } elseif ($model->document_type == 9){
                            return 'Պատվերից ելքագրված';
                        } elseif ($model->document_type == 10){
                            return 'Ետ վերադարցրած';
                        }
                    }
                ],
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
                    'attribute' => 'Փոխարժեք',
                    'value' => function ($model) {
                        if ($model->rateName) {
                            return $model->rateName->name;
                        } else {
                            return 'Դատարկ';
                        }
                    }
                ],
                [
                    'attribute' => 'Մեկնաբանություն',
                    'value' => function ($model) {
                        if ($model->comment) {
                            return $model->comment;
                        } else {
                            return 'Դատարկ';
                        }
                    }
                ],
                [
                    'attribute' => 'Ստեցծման ժամանակ',
                    'value' => function ($model) {
                        if ($model->date) {
                            return $model->date;
                        } else {
                            return 'Դատարկ';
                        }
                    }
                ],
            ],
        ]); ?>
    <?php }
}
else { ?>
    <div class="documents-index">
        <div class="titleAndPrev">
            <div class="titleAndConfig">
                <i class='bx bxs-log-out iconPrevPage' onclick="window.location = document.referrer"></i>
                <h3><?= Html::encode($this->title) ?></h3>
            </div>
            <?php if($have_access_custom_field){ ?>
                <h3><?= Html::a('', ['create-fields'], ['class' => 'bx bx-cog right-btn']) ?></h3>
            <?php } ?>
        </div>

        <div class="filtersParentsField" style="display: flex; justify-content: space-between; align-items: baseline;flex-wrap: wrap">
            <p>
                <?php if($have_access_create){ ?>
                    <?= Html::a('Ստեղծել փաստաթուղթ', ['create'], ['class' => 'btn rounded-pill btn-secondary']) ?>
                <?php } ?>
            </p>
            <div class="filtersField" style="display: flex; justify-content: space-between; align-items: baseline;align-items: baseline;">
                <select class="form-select documentStatus" aria-label="Default select example" style="width: 150px; margin: 0px 10px 15px 5px;">
                    <?php
                    if($session['role_id'] == '1' || $session['role_id'] == '4'){?>
                        <option selected value="0">Ընդհանուր</option>
                        <option value="1">Մուտք</option>
                        <option value="2">Ելք</option>
                        <option value="3">Տեղափոխություն</option>
                        <option value="4">Խոտան</option>
                        <option value="6">Վերադարձ</option>
                        <option value="7">Մերժված</option>
                        <option value="8">Մուտք(վերադարցրած)</option>
                        <option value="9">Պատվերից ելքագրված</option>
                        <option value="10">Ետ վերադարցրած</option>
                    <?php }?>
                </select>
            </div>
        </div>
        <div class="card pageStyle documentsCard">
            <?php if(!isset($data_size)){ ?>
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
                            'attribute' => 'Փաստաթղթի տեսակ',
                            'value' => function ($model) {
                                if ($model->document_type == 1) {
                                    return 'Մուտքի';
                                } elseif($model->document_type == 2) {
                                    return 'Ելքի';
                                } elseif($model->document_type == 3) {
                                    return 'Տեղափոխություն';
                                } elseif($model->document_type == 4) {
                                    return 'Խոտան';
                                } elseif($model->document_type == 6) {
                                    return 'Վերադարձրած';
                                } elseif($model->document_type == 7) {
                                    return 'Մերժված';
                                } elseif($model->document_type == 8){
                                    return 'Մուտք(վերադարցրած)';
                                } elseif ($model->document_type == 9){
                                    return 'Պատվերից ելքագրված';
                                } elseif ($model->document_type == 10){
                                    return 'Ետ վերադարցրած';
                                }
                            }
                        ],
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
                            'attribute' => 'Փոխարժեք',
                            'value' => function ($model) {
                                if ($model->rateName) {
                                    return $model->rateName->name;
                                } else {
                                    return 'Դատարկ';
                                }
                            }
                        ],
                        [
                            'attribute' => 'Մեկնաբանություն',
                            'value' => function ($model) {
                                if ($model->comment) {
                                    return $model->comment;
                                } else {
                                    return 'Դատարկ';
                                }
                            }
                        ],
                        [
                            'attribute' => 'Ստեցծման ժամանակ',
                            'value' => function ($model) {
                                if ($model->date) {
                                    return $model->date;
                                } else {
                                    return 'Դատարկ';
                                }
                            }
                        ],
                    ],
                ]); ?>
            <?php }
            else{ ?>
                <?php $dataProvider->pagination = false; ?>
                <?= CustomGridView::widget([
                    'tableOptions' => [
                        'class'=>'table chatgbti_',
                    ],
                    'options' => [
                        'class' => 'summary deletesummary'
                    ],
                    'summary' => 'Ցուցադրված է <b>{totalCount}</b>-ից <b>{begin}-{end}</b>-ը',
                    'summaryOptions' => ['class' => 'summary'],
                    'dataProvider' => $dataProvider,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        [
                            'attribute' => 'Փաստաթղթի տեսակ',
                            'value' => function ($model) {
                                if ($model->document_type == 1) {
                                    return 'Մուտքի';
                                } elseif($model->document_type == 2) {
                                    return 'Ելքի';
                                } elseif($model->document_type == 3) {
                                    return 'Տեղափոխություն';
                                } elseif($model->document_type == 4) {
                                    return 'Խոտան';
                                } elseif($model->document_type == 6) {
                                    return 'Վերադարձրած';
                                } elseif($model->document_type == 7) {
                                    return 'Մերժված';
                                } elseif($model->document_type == 8){
                                    return 'Մուտք(վերադարցրած)';
                                } elseif ($model->document_type == 9){
                                    return 'Պատվերից ելքագրված';
                                } elseif ($model->document_type == 10){
                                    return 'Ետ վերադարցրած';
                                }
                            }
                        ],
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
                            'attribute' => 'Փոխարժեք',
                            'value' => function ($model) {
                                if ($model->rateName) {
                                    return $model->rateName->name;
                                } else {
                                    return 'Դատարկ';
                                }
                            }
                        ],
                        [
                            'attribute' => 'Մեկնաբանություն',
                            'value' => function ($model) {
                                if ($model->comment) {
                                    return $model->comment;
                                } else {
                                    return 'Դատարկ';
                                }
                            }
                        ],
                        [
                            'attribute' => 'Ստեցծման ժամանակ',
                            'value' => function ($model) {
                                if ($model->date) {
                                    return $model->date;
                                } else {
                                    return 'Դատարկ';
                                }
                            }
                        ],
                    ],
                ]); ?>
            <?php } ?>
        </div>
    </div>
<?php } ?>

