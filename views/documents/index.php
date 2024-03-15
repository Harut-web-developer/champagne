<?php

use app\models\CustomfieldsBlocksInputValues;
use app\models\Documents;
use app\models\Users;
use app\models\Warehouse;
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
$session = Yii::$app->session;

$have_access_create = Users::checkPremission(37);
$have_access_update = Users::checkPremission(38);
$have_access_delete = Users::checkPremission(39);
$have_access_available = Users::checkPremission(56);
$have_access_custom_field = Users::checkPremission(71);
$have_access_confirm_return = Users::checkPremission(75);
$have_access_refuse = Users::checkPremission(81);

$action_column = [];
$access_buttons = '';
if($have_access_delete){
    $access_buttons .='{delete}';
}
if($have_access_confirm_return){
    $access_buttons .='{delivered}';
}
if($have_access_update){
    $access_buttons .='{update}';
}
if($have_access_refuse){
    $access_buttons .='{refuse}';
}
if($have_access_available){
    $access_buttons .='{reports}';
}

    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => $access_buttons,
        'buttons' =>[
            'reports'=>function ($url, $model, $key) {
                return Html::a('<img width="22" height="21" src="https://img.icons8.com/material-rounded/24/export-excel.png" alt="export-excel"/>', $url, [
                    'title' => Yii::t('yii', 'Հաշվետվություն'),
                    'class' => 'reportsOrders',
                    'target' => '_blank',
                ]);
            },

            'delivered'=>function ($url, $model, $key) {
                if ($model->document_type == 6){
                    return Html::a('<i class="bx bxs-check-circle" style="color:#0f5132; padding:0px 2px" ></i>', $url, [
                        'title' => Yii::t('yii', 'Հաստատել'),
                    ]);
                }else{
                    return '';
                }
            },
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
            'refuse'=>function ($url, $model, $key) {
                if ($model->document_type == 6){
                    return '<i class="bx bx-block refuseDocument" data-id="'. $key . '" title="Մերժել" style="color:red; padding:0px 2px"></i>';
                }else{
                    return '';
                }
            },
        ],

        'urlCreator' => function ($action, Documents $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        }
    ];
$fields_arr = [];

if(!empty($new_fields)){
    for ($i = 0; $i < count($new_fields); $i++){
        $fields_arr[$i]['attribute'] = $new_fields[$i]['attribute'];
        $fields_arr[$i]['value'] = function ($model,$key, $index, $column) {
            return CustomfieldsBlocksInputValues::getValue($model->id, $column->filterAttribute);
        };
    }
}
?>
<div class="documents-index">
    <div class="titleAndPrev">
        <div class="titleAndConfig">
            <i class='bx bxs-log-out iconPrevPage' onclick="window.location = document.referrer"></i>
            <h3><?= Html::encode($this->title) ?></h3>
        </div>
        <?php if($have_access_custom_field){ ?>
            <h3><?= Html::a('', ['create-fields'], ['class' => 'bx bx-cog right-btn']) ?></h3>
        <?php } ?>
    </div>

    <div class="filtersParentsField" style="display: flex; justify-content: space-between; align-items: baseline;flex-wrap: wrap">
        <p>
            <?php if($have_access_create){ ?>
                <?= Html::a('Ստեղծել փաստաթուղթ', ['create'], ['class' => 'btn rounded-pill btn-secondary']) ?>
            <?php } ?>
        </p>
        <div class="filtersField filtersFielddoc" style="display: flex; justify-content: space-between; align-items: baseline;align-items: baseline;">
            <?php
            if($session['role_id'] == '1'){?>
                <select class="form-select documentWarehouseStatus" aria-label="Default select example" style="width: 150px; margin: 0px 10px 15px 5px;">
                    <option selected value="0">Ընդհանուր</option>
                    <?php
                    $warehouse = Warehouse::find()->select('id,name')->asArray()->all();

                    foreach ($warehouse as $warehouse_value) { ?>
                    <option value="<?=$warehouse_value['id']?>"><?=$warehouse_value['name']?></option>
                    <?php }?>
                </select>
            <?php }?>
            <?php
            if($session['role_id'] == '1' || $session['role_id'] == '4'){?>
            <input type="date" class="form-control documentsDate" style="width: 150px; margin: 0px 10px 15px 5px;">
            <select class="form-select documentStatus" aria-label="Default select example" style="width: 150px; margin: 0px 10px 15px 5px;">
                    <option selected value="0">Ընդհանուր</option>
                    <option value="1">Մուտք</option>
                    <option value="2">Ելք</option>
                    <option value="3">Տեղափոխություն</option>
                    <option value="4">Խոտան</option>
                    <option value="6">Վերադարձ</option>
                    <option value="7">Մերժված</option>
                    <option value="8">Մուտք(վերադարցրած)</option>
                    <option value="9">Պատվերից ելքագրված</option>
                    <option value="10">Ետ վերադարցրած</option>
            </select>
            <?php }?>
            <div class="iconsPrintAndXlsx">
                <div>
                    <img class="documents_downloadXLSX" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAAACXBIWXMAAAsTAAALEwEAmpwYAAAC+klEQVR4nO2ZzWsTURDAFwW/kYre9CKKiB4ERT2of4AUveXqF1htZtaQ7rw0NDN9ehMUsTfxIggepPSioOJRz3rQg3gRqYI91I/Wg1rUykvzkk2atNl2d/uCHRjIMpnZ+c2b93Y28bxlWZZlmVf8gt4GJMNAMolKpheoU6DkJarBjJd68oo/LyLx2UqsUwMAkuFYk68oKLmcFsBkXAkvCQTGVPFyMRT3z24nudoxAEsCgTEDpA6BCQCkCoEJAaQGgQkCpAKRNICRZkdsbA+7NABaQsQxdsQFYEaSyBDEL5wBAMWPokIA8S9nAJJqvXnlvwEAxbetj9Z6BSh5V2+Xi9YOhcGzzgGgkimfSjuane9AMpbP59dW4FY1wqEjAKbKd6xfT5/egsQ/yjaSYggs52QL4Yz+xj69u+pLfBcUT+RyustcZ7XeYFbDZYBpVHLP+mb7S0eR5GYISJzdxFjtd/7jB3qP3czZAu81n6FY3Iwk35wHwBm93xgHSK4vJJa3JADE333fX10HoPhNJwFcmRWH+NScrafkJygeygZyOAiC9YtOfOEA/MmcNmXfoLQd+3mf3Q9I8qqF30f7vdglevUHz4cHMyC5Ya/9QE40qzwmlXxUAFD8OpPJrDR+ZhWQZNz8rtRbLG6qxiN53uAzlFjyUQEwkOM1P86HbAN1z4awT2HgUN39Auk2LQXEH8Lxkgcgfmp9zLyDSkarVSYZO631GmsHxQ+sLVvZL1WbSbwWdzQ1AF/xuUuBHDDa7EUdiEvWjsRnWh2T6MQxGkG9DgZ4DIpPdiLAX6AStHs/zz0Avhblfp5TAOUZSW9MFQAUT8QHIA/DsXM53QXEz0xbtTOmI/GTHq3XRQMgGYkLAJTcqqs21Y7TtjWQ7mgAfXqXGQliASAZCcfOFvVO01YRVnDcvGt7UeVCfmCreUlZbDuZfzvNU7puFZQcawuC5Asovd9zUZDkyFx/JoLir72BHPRcFmwB0RHJexVphOio5K1Uxu33qPhtOz3/DyrGtgq43BHiAAAAAElFTkSuQmCC">
                </div>
                <div>
                    <img class="print_document_table" src="/upload/icons8-print-94.png">
                </div>
            </div>
        </div>
    </div>
    <div class="card pageStyle documentsCard">
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
                'attribute' => 'Փաստաթղթի տեսակ',
                'value' => function ($model) {
                    if ($model->document_type == 1) {
                        return 'Մուտքի';
                    } elseif($model->document_type == 2) {
                        return 'Ելքի';
                    } elseif($model->document_type == 3) {
                        return 'Տեղափոխություն';
                    } elseif($model->document_type == 4) {
                        return 'Խոտան';
                    } elseif($model->document_type == 6) {
                        return 'Վերադարձրած';
                    } elseif($model->document_type == 7) {
                        return 'Մերժված';
                    } elseif($model->document_type == 8){
                        return 'Մուտք(վերադարցրած)';
                    } elseif ($model->document_type == 9){
                        return 'Պատվերից ելքագրված';
                    } elseif ($model->document_type == 10){
                        return 'Ետ վերադարցրած';
                    }
                }
            ],
            [
                'attribute' => 'Պահեստապետ',
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
                'attribute' => 'Առաքիչ',
                'value' => function ($model) {
                    if ($model->deliverName) {
                        return $model->deliverName->name;
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
            ...$fields_arr,
        ],
    ]); ?>
    </div>
</div>
<div class="modals">

</div>