<?php

use app\models\Payments;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\PaymentsSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Վճարում';
$this->params['breadcrumbs'][] = $this->title;
$this->params['sub_page'] = $sub_page;
$this->params['date_tab'] = $date_tab;


?>
<div class="payments-index">
    <div class="titleAndPrevPage">
        <i class='bx bxs-log-out iconPrevPage' onclick="window.location = document.referrer"></i>
        <h3><?= Html::encode($this->title) ?></h3>
    </div>
    <p>
        <?= Html::a('Կատարել վճար', ['create'], ['class' => 'btn rounded-pill btn-secondary']) ?>
    </p>
    <div class="card">
    <?= GridView::widget([
        'summary' => 'Ցուցադրված է <b>{totalCount}</b>-ից <b>{begin}-{end}</b>-ը',
        'summaryOptions' => ['class' => 'summary'],
        'dataProvider' => new ActiveDataProvider([
            'query' => $dataProvider->query->andWhere(['status' => '1']),
//                'pagination' => [
//                    'pageSize' => 20,
//                ],
        ]),
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'Հաճախորդ',
                'value' => function ($model) {
                    if ($model->clientName) {
                        return $model->clientName->name;
                    } else {
                        return 'Դատարկ';
                    }
                }
            ],
            'payment_sum',
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
            'rate_value',
            'pay_date',

            [
                'header' => 'Գործողություն',
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Payments $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>
    </div>

</div>