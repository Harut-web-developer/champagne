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

$access_buttons = '';
if($have_access_delete){
    $access_buttons .='{delete}';
}
if($have_access_update){
    $access_buttons .='{delivered}{update}';
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
?>

<?= CustomGridView::widget([
    'summary' => 'Ցուցադրված է <b>{totalCount}</b>-ից <b>{begin}-{end}</b>-ը',
    'summaryOptions' => ['class' => 'summary'],
    'dataProvider' => new ActiveDataProvider([
        'query' => $dataProvider->query->andWhere(['status' => '1']),
    ]),
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        ...$action_column,
        'document_type',
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
<script>
    $(document).ready(function() {
        $('.documentsCard').find('tbody tr').each(function () {
            let document_type = $(this).find('td:nth-child(3)').text();
            if (document_type == 6) {
                $(this).find('td:nth-child(2) a:not([title="Հաստատել"])').remove();
            }else {
                $(this).find('td:nth-child(2) a[title="Հաստատել"]').remove();
            }
        })
    })
</script>
