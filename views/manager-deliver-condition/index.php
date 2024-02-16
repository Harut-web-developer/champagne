<?php

use app\models\ManagerDeliverCondition;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use app\widgets\CustomGridView;
use app\models\Users;

/** @var yii\web\View $this */
/** @var app\models\ManagerDeliverConditionSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Մենեջերին կցված առաքիչներ';
$this->params['breadcrumbs'][] = $this->title;
$this->params['sub_page'] = $sub_page;
$this->params['date_tab'] = $date_tab;
$have_access_create = Users::checkPremission(77);
$have_access_update = Users::checkPremission(78);
$have_access_delete = Users::checkPremission(79);
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
    'urlCreator' => function ($action, ManagerDeliverCondition $model, $key, $index, $column) {
        return Url::toRoute([$action, 'id' => $model->id]);
    }
];
?>

<div class="manager-deliver-condition-index">

    <div class="titleAndPrevPage">
        <i class='bx bxs-log-out iconPrevPage' onclick="window.location = document.referrer"></i>
        <h3><?= Html::encode($this->title) ?></h3>
    </div>
    <p>
        <?php if($have_access_create){ ?>
            <?= Html::a('Ստեղծել մենեջեր-առաքիչ կապ', ['create'], ['class' => 'btn rounded-pill btn-secondary']) ?>
        <?php } ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <div class="card">

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
                    'attribute' => 'manager_id',
                    'value' => function ($model) {
                        if ($model->managerName) {
                            return $model->managerName->name;
                        } else {
                            return 'Դատարկ';
                        }
                    }
                ],

            ],
        ]); ?>

    </div>
</div>
