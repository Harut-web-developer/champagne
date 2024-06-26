<?php

use app\models\CustomfieldsBlocksInputValues;
use app\models\Nomenclature;
use app\models\Users;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use app\widgets\CustomGridView;


/** @var yii\web\View $this */
/** @var app\models\NomenclatureSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Անվանակարգ';
$this->params['breadcrumbs'][] = $this->title;
$this->params['sub_page'] = $sub_page;
$this->params['date_tab'] = $date_tab;

$have_access_create = Users::checkPremission(9);
$have_access_update = Users::checkPremission(10);
$have_access_delete = Users::checkPremission(11);
$have_access_custom_field = Users::checkPremission(72);
$action_column = [];
$access_buttons = '';
if($have_access_delete){
    $access_buttons .='{delete}';
}
if($have_access_update){
    $access_buttons .='{update}';
}
if (!empty($access_buttons)) {
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => $access_buttons,
        'buttons' => [
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
        'urlCreator' => function ($action, Nomenclature $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        }
    ];
}
$session = Yii::$app->session;
if ($session->hasFlash('error')) {
    $error = addslashes($session->getFlash('error'));
    $this->registerJs("
        alert('{$error}');
    ");
}
$fields_arr = [];
if (!empty($new_fields)) {
    foreach ($new_fields as $index => $field) {
        if (!is_null($field['attribute'])) {
            $fields_arr[$index] = [
                'attribute' => $field['attribute'],
                'value' => function ($model, $key, $index, $column) {
                    return CustomfieldsBlocksInputValues::getValue($model->id, $column->filterAttribute);
                },
                'format' => 'raw',
            ];
        }
    }
}
?>
<div class="nomenclature-index">
    <div class="titleAndPrev">
        <div class="titleAndConfig">
            <i class='bx bxs-log-out iconPrevPage' onclick="window.location = document.referrer"></i>
            <h3><?= Html::encode($this->title) ?></h3>
        </div>
        <?php if($have_access_custom_field){ ?>
            <h3><?= Html::a('', ['create-fields'], ['class' => 'bx bx-cog right-btn']) ?></h3>
        <?php } ?>

    </div>
    <p>
        <?php if($have_access_create){ ?>
            <?= Html::a('Ստեղծել անվանակարգ', ['create'], ['class' => 'btn rounded-pill btn-secondary']) ?>
        <?php } ?>
    </p>
    <div class="card pageStyle">
        <?= CustomGridView::widget([
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
                ...$action_column,
                [
                    'attribute' => 'image',
                    'format' => 'raw',
                    'value' => function($model) {
                        return '<img  src="/upload/' . $model->image . '"width="40px">';
                    }
                ],
                'name',
                'price',
                'cost',
                ...$fields_arr,
            ],
        ]); ?>
    </div>
</div>
