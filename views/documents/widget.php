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

$action_column[] = [
    'header' => 'Գործողություն',
    'class' => ActionColumn::className(),
    'template' => $access_buttons,
    'buttons' =>[
        'delivered'=>function ($url, $model, $key) {
            return Html::a('<i class="bx bxs-check-circle" style="color:#0f5132; padding:0px 2px" ></i>', $url, [
                'title' => Yii::t('yii', 'Հաստատել'), // Add a title if needed
            ]);
        }],
    'urlCreator' => function ($action, Documents $model, $key, $index, $column) {
        return Url::toRoute([$action, 'id' => $model->id]);
    }
];
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => $access_buttons,
        'buttons' =>[
            'delivered'=>function ($url, $model, $key) {
                return Html::a('<i class="bx bxs-check-circle" style="color:#0f5132; padding:0px 2px" ></i>', $url, [
                    'title' => Yii::t('yii', 'Հաստատել'), // Add a title if needed
                ]);
            },
            'refuse'=>function ($url, $model, $key) {
                return '<i class="bx bx-block refuseDocument" data-id="'. $key . '" title="Մերժել" style="color:red; padding:0px 2px"></i>';
            },
],
        'urlCreator' => function ($action, Documents $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        }
    ];
?>

<?php  if (!isset($page_value)){ ?>
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
                'attribute' => 'Օգտատեր',
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
<?php } else { ?>
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
                    if($session['role_id'] == '1'){?>
                        <option selected value="0">Ընդհանուր</option>
                        <option value="1">Մուտք</option>
                        <option value="2">Ելք</option>
                        <option value="3">Տեղափոխություն</option>
                        <option value="4">Խոտան</option>
                        <option value="6">Վերադարձ</option>
                    <?php }?>
                </select>
            </div>
        </div>
        <div class="card pageStyle documentsCard">
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
                        'attribute' => 'Օգտատեր',
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
        </div>
    </div>
<?php } ?>


<script>
    $(document).ready(function() {
        $('.documentsCard').find('tbody tr').each(function () {
            let document_type = $(this).find('td:nth-child(3)').text();
            if (document_type != 'Վերադարձրած') {
                $(this).find('td:nth-child(2) a[title="Հաստատել"]').remove();
                $(this).find('td:nth-child(2) .refuseDocument').remove();
            }
        })
    })
</script>
