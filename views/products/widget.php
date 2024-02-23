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

<?php  if (!isset($page_value)){ ?>
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
                'attribute' => 'Մնացորդ',
                'value' => function ($model) {
                    if ($model->count < 0) {
                        return $model->count * (-1);
                    } else {
                        return $model->count;
                    }
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
<?php } else { ?>
    <div class="products-index">
        <div class="titleAndPrev">
            <div class="titleAndConfig">
                <i class='bx bxs-log-out iconPrevPage' onclick="window.location = document.referrer"></i>
                <h3><?= Html::encode($this->title) ?></h3>
            </div>
            <div class="filtersField" style="display: flex; justify-content: space-between; align-items: baseline;align-items: baseline;">
                <?php if($session['role_id'] == '1' || $session['role_id'] == '2'){ ?>
                    <select class="form-select productStatus" aria-label="Default select example" style="width: auto; margin: 0px 10px 15px 5px;">
                        <option selected value="0">Ընդհանուր</option>
                        <?php foreach ($warehouse as $item => $value){ ?>
                            <option value="<?=$value['id']?>"><?=$value['name']?></option>
                        <?php } ?>
                    </select>
                <?php }?>
            </div>
        </div>
        <div class="card pageStyle">
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
                            if ($model->count < 0) {
                                return $model->count * (-1);
                            } else {
                                return $model->count;
                            }
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
        </div>
    </div>
<?php } ?>
