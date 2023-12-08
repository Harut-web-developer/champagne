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
$have_access_create = Users::checkPremission(21);
$have_access_update = Users::checkPremission(22);
$have_access_delete = Users::checkPremission(23);
$action_column = [];
if ($have_access_update && $have_access_delete){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => '{update} {delete}',
        'urlCreator' => function ($action, Orders $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        }
    ];
} else if($have_access_update){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => '{update}',
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
}
?>
<div class="orders-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?php if ($have_access_create) { ?>
            <?= Html::a('Ստեղծել վաճառքներ', ['create'], ['class' => 'btn rounded-pill btn-secondary']) ?>
        <?php } ?>
    </p>

    <?php
    Modal::begin([
        'id' => 'modal',
        'size' => 'modal-lg',
    ]);
    echo "<div id='modalContent'>";
    echo "<h4>Վաճառքներ</h4>";
    // ... Other modal content ...
    echo "</div>";
    Modal::end();
    ?>

    <?php
    $gridColumns = [
        'id' => 'ID',
        'user_id' => 'Օգտատեր',
        'clients_id' => 'Հաճախորդ',
        'status' => 'Status',
        'comment' => 'Մեկնաբանություն',
        'total_price' => 'Ընդհանուր գումար',
        'total_count' => 'Ընդհանուր քանակ',
        'created_at' => 'Created At',
        'updated_at' => 'Updated At',
    ];
    echo ExportMenu::widget([
        'dataProvider' =>$dataProvider,
        'columns' => $gridColumns,
    ]);
    ?>
    <div class="card">
    <?= GridView::widget([
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
    </div>
</div>
