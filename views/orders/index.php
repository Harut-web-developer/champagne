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
use app\widgets\CustomGridView;

/** @var yii\web\View $this */
/** @var app\models\OrdersSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Վաճառքներ';
$this->params['breadcrumbs'][] = $this->title;
$this->params['sub_page'] = $sub_page;
$this->params['date_tab'] = $date_tab;
$session = Yii::$app->session;


$have_access_create = Users::checkPremission(21);
$have_access_update = Users::checkPremission(22);
$have_access_delete = Users::checkPremission(23);
$have_access_delivered = Users::checkPremission(55);
$have_access_available = Users::checkPremission(56);
$have_access_exit_document = Users::checkPremission(76);
$action_column = [];
$access_buttons = '';
if($have_access_delete){
    $access_buttons .='{delete}';
}
if($have_access_exit_document){
    $access_buttons .='{exit}';
}
if($have_access_update){
    $access_buttons .='{update}';
}
if($have_access_delivered){
    $access_buttons .='{delivered}';
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
         if($model->status == 1) {
             return Html::a('<i class="bx bxs-check-circle" style="color:#0f5132" ></i>', $url, [
                 'title' => Yii::t('yii', 'Հաստատել'), // Add a title if needed
             ]);
         } elseif($model->status == 2) {
             return '';
         }
    },
        'update' => function ($url, $model, $key) {
            $icon = '<svg aria-hidden="true" style="display:inline-block;font-size:inherit;height:1em;overflow:visible;vertical-align:-.125em;width:1em" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M498 142l-46 46c-5 5-13 5-17 0L324 77c-5-5-5-12 0-17l46-46c19-19 49-19 68 0l60 60c19 19 19 49 0 68zm-214-42L22 362 0 484c-3 16 12 30 28 28l122-22 262-262c5-5 5-13 0-17L301 100c-4-5-12-5-17 0zM124 340c-5-6-5-14 0-20l154-154c6-5 14-5 20 0s5 14 0 20L144 340c-6 5-14 5-20 0zm-36 84h48v36l-64 12-32-31 12-65h36v48z"></path></svg>';
            if ($model->status == 1){
                return Html::a($icon, $url, [
                    'title' => Yii::t('yii', 'Թարմացնել'),
                ]);
            }else{
                return '';
            }
        },
        'delete' => function ($url, $model, $key) {
            $del_icon = '<svg aria-hidden="true" style="display:inline-block;font-size:inherit;height:1em;overflow:visible;vertical-align:-.125em;width:.875em" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M32 464a48 48 0 0048 48h288a48 48 0 0048-48V128H32zm272-256a16 16 0 0132 0v224a16 16 0 01-32 0zm-96 0a16 16 0 0132 0v224a16 16 0 01-32 0zm-96 0a16 16 0 0132 0v224a16 16 0 01-32 0zM432 32H312l-9-19a24 24 0 00-22-13H167a24 24 0 00-22 13l-9 19H16A16 16 0 000 48v32a16 16 0 0016 16h416a16 16 0 0016-16V48a16 16 0 00-16-16z"></path></svg>';
            if ($model->status == 1){
                return Html::a($del_icon, $url, [
                    'title' => Yii::t('yii', 'Ջնջել'),
                    'data' => [
                        'confirm' => Yii::t('yii', 'Վստա՞հ եք, որ ցանկանում եք ջնջել այս տարրը:'),
                        'method' => 'post',
                    ],
                ]);
            }else{
                return '';
            }
        },
        'exit'=>function ($url, $model, $key) {
            if($model->is_exit == 1 && $model->status == 1){
                return '<i class="bx bx-receipt exitOrders" data-id="'. $key . '" title="Ելքագրել" style="color:red; padding:0px 2px"></i>';
            }else{
                return '';
            }
        },
],
    'urlCreator' => function ($action, Orders $model, $key, $index, $column) {
        return Url::toRoute([$action, 'id' => $model->id]);
    }
];
?>

    <div class="orders-index">
        <div class="titleAndPrevPage">
            <i class='bx bxs-log-out iconPrevPage' onclick="window.location = document.referrer"></i>
            <h3><?= Html::encode($this->title) ?></h3>
        </div>
        <div class="filtersParentsField" style="display: flex; justify-content: space-between; align-items: baseline;flex-wrap: wrap">
            <p>
                <?php if ($have_access_create) { ?>
                    <?= Html::a('Ստեղծել վաճառքներ', ['create'], ['class' => 'btn rounded-pill btn-secondary']) ?>
                <?php } ?>
            </p>
            <div class="filtersField filtersFieldord" style="display: flex; justify-content: space-between; align-items: baseline;align-items: baseline;">
                <select class="form-control byType" style="margin: 0px 0px 10px 0px;">
                    <option value="order">Ըստ պատվերի</option>
                    <option value="product">Ըստ ապրանքի</option>
                </select>
                <input type="date" class="form-control ordersDate" style="margin: 0px 10px 15px 5px;">
                <?php
                $users = Users::find()->select('id,name')->where(['=','role_id',2])->asArray()->all();
                if($session['role_id'] == '1' || $session['role_id'] == '4'){?>
                    <select class="form-control changeManager">
                        <option value="null">Ընտրել մենեջերին</option>
                        <?php
                        foreach ($users as $user){
                            ?>
                            <option value="<?=$user['id']?>"><?=$user['name']?></option>
                            <?php
                        }
                        ?>
                    </select>
                <?php }elseif ($session['role_id'] == '2'){ ?>
                    <input class="changeManager" type="hidden" value="<?=$session['user_id']?>">
                <?php }?>
                <select class="form-control changeClients" style="width: 210px; margin: 0px 10px 15px 5px;">
                    <option value="null">Բոլոր հաճախորդները</option>
                    <?php
                    foreach ($clients as $client){
                        ?>
                        <option value="<?=$client['id']?>"><?=$client['name']?></option>
                        <?php
                    }
                    ?>
                </select>
                <select class="form-select orderStatus" aria-label="Default select example" style="width: 150px; margin: 0px 10px 15px 5px;">
                    <?php
                    if($session['role_id'] == '1'){?>
                        <option selected value="3">Ընդհանուր</option>
                        <option value="0">Մերժված</option>
                        <option value="2">Հաստատված</option>
                        <option value="1">Ընթացքի մեջ</option>
                    <?php }else{ ?>
                        <option selected value="4">Ընդհանուր</option>
                        <option value="2">Հաստատված</option>
                        <option value="1">Ընթացքի մեջ</option>
                    <?php }?>
                </select>
                <div class="iconsPrintAndXlsx">
                    <div>
                        <img class="downloadXLSX" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAAACXBIWXMAAAsTAAALEwEAmpwYAAAC+klEQVR4nO2ZzWsTURDAFwW/kYre9CKKiB4ERT2of4AUveXqF1htZtaQ7rw0NDN9ehMUsTfxIggepPSioOJRz3rQg3gRqYI91I/Wg1rUykvzkk2atNl2d/uCHRjIMpnZ+c2b93Y28bxlWZZlmVf8gt4GJMNAMolKpheoU6DkJarBjJd68oo/LyLx2UqsUwMAkuFYk68oKLmcFsBkXAkvCQTGVPFyMRT3z24nudoxAEsCgTEDpA6BCQCkCoEJAaQGgQkCpAKRNICRZkdsbA+7NABaQsQxdsQFYEaSyBDEL5wBAMWPokIA8S9nAJJqvXnlvwEAxbetj9Z6BSh5V2+Xi9YOhcGzzgGgkimfSjuane9AMpbP59dW4FY1wqEjAKbKd6xfT5/egsQ/yjaSYggs52QL4Yz+xj69u+pLfBcUT+RyustcZ7XeYFbDZYBpVHLP+mb7S0eR5GYISJzdxFjtd/7jB3qP3czZAu81n6FY3Iwk35wHwBm93xgHSK4vJJa3JADE333fX10HoPhNJwFcmRWH+NScrafkJygeygZyOAiC9YtOfOEA/MmcNmXfoLQd+3mf3Q9I8qqF30f7vdglevUHz4cHMyC5Ya/9QE40qzwmlXxUAFD8OpPJrDR+ZhWQZNz8rtRbLG6qxiN53uAzlFjyUQEwkOM1P86HbAN1z4awT2HgUN39Auk2LQXEH8Lxkgcgfmp9zLyDSkarVSYZO631GmsHxQ+sLVvZL1WbSbwWdzQ1AF/xuUuBHDDa7EUdiEvWjsRnWh2T6MQxGkG9DgZ4DIpPdiLAX6AStHs/zz0Avhblfp5TAOUZSW9MFQAUT8QHIA/DsXM53QXEz0xbtTOmI/GTHq3XRQMgGYkLAJTcqqs21Y7TtjWQ7mgAfXqXGQliASAZCcfOFvVO01YRVnDcvGt7UeVCfmCreUlZbDuZfzvNU7puFZQcawuC5Asovd9zUZDkyFx/JoLir72BHPRcFmwB0RHJexVphOio5K1Uxu33qPhtOz3/DyrGtgq43BHiAAAAAElFTkSuQmCC">
                    </div>
                    <div>
                        <img class="print_orders_table" src="/upload/icons8-print-94.png">
                    </div>
                </div>
            </div>
        </div>
        <div class="card pageStyle ordersCard">
            <?= CustomGridView::widget([
                'summary' => 'Ցուցադրված է <b>{totalCount}</b>-ից <b>{begin}-{end}</b>-ը',
                'summaryOptions' => ['class' => 'summary'],
                'dataProvider' => new ActiveDataProvider([
                    'query' => $dataProvider->query,
                ]),
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    ...$action_column,
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
                        'attribute' => 'Կարգավիճակ',
                        'value' => function ($model) {
                            if ($model->status == 1) {
                                return 'Ընթացքի մեջ';
                            } elseif($model->status == 2){
                                return 'Հաստատված';
                            } elseif($model->status == 0){
                                return 'Մերժված';
                            } elseif($model->status == 4){
                                return 'Ետ վերադարցրած';
                            }
                        }
                    ],
                    [
                        'attribute' => 'Փաստաթուղթ',
                        'value' => function ($model) {
                            if ($model->is_exit == 1) {
                                return 'Չելքագրված';
                            }else{
                                return 'Ելքագրված';
                            }
                        }
                    ],
                    'total_price',
                    'total_count',
                    'orders_date',
                ],
            ]); ?>
        </div>
    </div>
<div class="modalsExit">

</div>