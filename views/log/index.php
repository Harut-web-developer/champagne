<?php

use app\models\Log;
use app\models\Users;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\LogSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Տեղեկամատյան';
$this->params['breadcrumbs'][] = $this->title;
$this->params['sub_page'] = $sub_page;
$have_access_create = Users::checkPremission(25);
$have_access_update = Users::checkPremission(26);
$have_access_delete = Users::checkPremission(27);
$action_column = [];
if ($have_access_update && $have_access_delete){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => '{update} {delete}',
        'urlCreator' => function ($action, Log $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        }
    ];
} else if($have_access_update){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => '{update}',
        'urlCreator' => function ($action, Log $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        }
    ];
}else if($have_access_delete){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => '{delete}',
        'urlCreator' => function ($action, Log $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        }
    ];
}
?>
<div class="log-index">
    <div class="titleAndPrevPage">
        <i class='bx bxs-log-out iconPrevPage' onclick="window.location = document.referrer"></i>
        <h3><?= Html::encode($this->title) ?></h3>
    </div>
    <p>
<!--        --><?php //if($have_access_create){ ?>
<!--            --><?php //= Html::a('Ստեղծել տեղեկամատյան', ['create'], ['class' => 'btn rounded-pill btn-secondary']) ?>
<!--        --><?php //} ?>
    </p>
    <div class="card">
    <?= GridView::widget([
        'summary' => 'Ցուցադրված է <b>{totalCount}</b>-ից <b>{begin}-{end}</b>-ը',
        'summaryOptions' => ['class' => 'summary'],
        'dataProvider' => new ActiveDataProvider([
            'query' => $dataProvider->query,
            //                'pagination' => [
            //                    'pageSize' => 20,
            //                ],
        ]),
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'Օգտագործող',
                'value' => function ($model) {
                    if ($model->userName) {
                        return $model->userName->name;
                    } else {
                        return 'Դատարկ';
                    }
                }
            ],
            [
                'attribute' => 'action',
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model->action) {
                        return Html::a('Դիտել', $model->action);
                    } else {
                        return Html::a('Դիտել', '#');
                    }
                }
            ],
            [
                'attribute' => 'description',
                'format' => 'raw',
                'value' => function ($model) {
                    return nl2br(htmlspecialchars($model->description));
                }
            ],
            'create_date',
//            ...$action_column,
        ],
    ]); ?>

    </div>
</div>
