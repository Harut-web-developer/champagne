<?php

use app\models\GroupsName;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\GroupsNameSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Հաճախորդների խմբեր';
$this->params['breadcrumbs'][] = $this->title;
$this->params['sub_page'] = $sub_page;

?>
<div class="groups-name-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <!--        --><?php //if($have_access_create){ ?>
        <?= Html::a('Ստեղծել հաճախորդ', ['create'], ['class' => 'btn rounded-pill btn-secondary']) ?>
        <!--        --><?php //} ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

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
//        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'id',
            'groups_name',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, GroupsName $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>
    </div>

</div>
