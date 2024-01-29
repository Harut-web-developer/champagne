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

$have_access_create = Users::checkPremission(37);
$have_access_update = Users::checkPremission(38);
$have_access_delete = Users::checkPremission(39);
$action_column = [];
if ($approved == 0 || $approved == 1 || $approved == 2 || $approved == 3 || $approved == 4){
    $btn = '{update} {delete}';
}
if ($have_access_update && $have_access_delete){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => $btn,
        'urlCreator' => function ($action, Documents $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        }
    ];
}?>

<?= CustomGridView::widget([
    'summary' => 'Ցուցադրված է <b>{totalCount}</b>-ից <b>{begin}-{end}</b>-ը',
    'summaryOptions' => ['class' => 'summary'],
    'dataProvider' => new ActiveDataProvider([
        'query' => $dataProvider->query->andWhere(['status' => '1']),
    ]),
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
        'comment',
        'date',
        ...$action_column,
    ],
]); ?>
