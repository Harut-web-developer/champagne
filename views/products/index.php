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
$access_buttons = '';
if($have_access_delete){
    $access_buttons .='{delete}';
}
if($have_access_update){
    $access_buttons .='{update}';
}
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => $access_buttons,
        'buttons' =>[
            'delete' => function ($url, $model, $key) {
                $del_icon = '<svg aria-hidden="true" style="display:inline-block;font-size:inherit;height:1em;overflow:visible;vertical-align:-.125em;width:.875em;color:red" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M32 464a48 48 0 0048 48h288a48 48 0 0048-48V128H32zm272-256a16 16 0 0132 0v224a16 16 0 01-32 0zm-96 0a16 16 0 0132 0v224a16 16 0 01-32 0zm-96 0a16 16 0 0132 0v224a16 16 0 01-32 0zM432 32H312l-9-19a24 24 0 00-22-13H167a24 24 0 00-22 13l-9 19H16A16 16 0 000 48v32a16 16 0 0016 16h416a16 16 0 0016-16V48a16 16 0 00-16-16z"></path></svg>';
                return Html::a($del_icon, $url, [
                    'title' => Yii::t('yii', 'Ջնջել'),
                    'data' => [
                        'confirm' => Yii::t('yii', 'Վստա՞հ եք, որ ցանկանում եք ջնջել այս տարրը:'),
                        'method' => 'post',
                    ],
                ]);
            },
        ],
        'urlCreator' => function ($action, Products $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        }
    ];
?>
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
    </div>
</div>
