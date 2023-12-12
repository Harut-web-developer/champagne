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
$this->params['sub_page'] = $sub_page;

$have_access_create = Users::checkPremission(21);
$have_access_update = Users::checkPremission(22);
$have_access_delete = Users::checkPremission(23);
$have_access_delivered = Users::checkPremission(55);
$action_column = [];
if ($have_access_update && $have_access_delete && $have_access_delivered){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => '{delivered} {update} {delete}',
        'buttons' => [
            'delivered' => function ($url, $model, $key) {
                // The content of the new template with your SVG icon
                return Html::a('<i class="bx bxs-check-circle" style="color:#0f5132" ></i>', $url, [
                    'title' => Yii::t('yii', 'delivered'), // Add a title if needed
                ]);
            },
        ],
        'urlCreator' => function ($action, Orders $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        }
    ];
} else if($have_access_update && $have_access_delivered){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => '{delivered} {update}',
        'urlCreator' => function ($action, Orders $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        }
    ];
}else if($have_access_delete && $have_access_delivered){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => '{delivered} {delete}',
        'urlCreator' => function ($action, Orders $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        }
    ];
}
else if($have_access_update && $have_access_delete){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => '{update} {delete}',
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
}else if($have_access_update){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => '{update}',
        'urlCreator' => function ($action, Orders $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        }
    ];
}else if($have_access_delivered){
    $action_column[] = [
        'header' => 'Գործողություն',
        'class' => ActionColumn::className(),
        'template' => '{delivered}',
        'urlCreator' => function ($action, Orders $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id]);
        }
    ];
}
?>
<?php if(!isset($data_size)){ ?>
    <div class="orders-index">
        <h1><?= Html::encode($this->title) ?></h1>
<!--        --><?php
//        $dataProvider->pagination->pageSize = 10;
//        ?>
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <p>
                <?php if ($have_access_create) { ?>
                    <?= Html::a('Ստեղծել վաճառքներ', ['create'], ['class' => 'btn rounded-pill btn-secondary']) ?>
                <?php } ?>
            </p>
            <select class="form-select orderStatus" aria-label="Default select example" style="width: 150px; margin-left: auto;">
                <?php
                $session = Yii::$app->session;
                if($session['role_id'] == '1'){?>
<!--                    <option selected value="3">Ընտրել</option>-->
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
            <img class="downloadXLSX" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAAACXBIWXMAAAsTAAALEwEAmpwYAAAC+klEQVR4nO2ZzWsTURDAFwW/kYre9CKKiB4ERT2of4AUveXqF1htZtaQ7rw0NDN9ehMUsTfxIggepPSioOJRz3rQg3gRqYI91I/Wg1rUykvzkk2atNl2d/uCHRjIMpnZ+c2b93Y28bxlWZZlmVf8gt4GJMNAMolKpheoU6DkJarBjJd68oo/LyLx2UqsUwMAkuFYk68oKLmcFsBkXAkvCQTGVPFyMRT3z24nudoxAEsCgTEDpA6BCQCkCoEJAaQGgQkCpAKRNICRZkdsbA+7NABaQsQxdsQFYEaSyBDEL5wBAMWPokIA8S9nAJJqvXnlvwEAxbetj9Z6BSh5V2+Xi9YOhcGzzgGgkimfSjuane9AMpbP59dW4FY1wqEjAKbKd6xfT5/egsQ/yjaSYggs52QL4Yz+xj69u+pLfBcUT+RyustcZ7XeYFbDZYBpVHLP+mb7S0eR5GYISJzdxFjtd/7jB3qP3czZAu81n6FY3Iwk35wHwBm93xgHSK4vJJa3JADE333fX10HoPhNJwFcmRWH+NScrafkJygeygZyOAiC9YtOfOEA/MmcNmXfoLQd+3mf3Q9I8qqF30f7vdglevUHz4cHMyC5Ya/9QE40qzwmlXxUAFD8OpPJrDR+ZhWQZNz8rtRbLG6qxiN53uAzlFjyUQEwkOM1P86HbAN1z4awT2HgUN39Auk2LQXEH8Lxkgcgfmp9zLyDSkarVSYZO631GmsHxQ+sLVvZL1WbSbwWdzQ1AF/xuUuBHDDa7EUdiEvWjsRnWh2T6MQxGkG9DgZ4DIpPdiLAX6AStHs/zz0Avhblfp5TAOUZSW9MFQAUT8QHIA/DsXM53QXEz0xbtTOmI/GTHq3XRQMgGYkLAJTcqqs21Y7TtjWQ7mgAfXqXGQliASAZCcfOFvVO01YRVnDcvGt7UeVCfmCreUlZbDuZfzvNU7puFZQcawuC5Asovd9zUZDkyFx/JoLir72BHPRcFmwB0RHJexVphOio5K1Uxu33qPhtOz3/DyrGtgq43BHiAAAAAElFTkSuQmCC">
        </div>
        <div class="card">
            <?= GridView::widget([
                'summary' => 'Ցուցադրված է <b>{totalCount}</b>-ից <b>{begin}-{end}</b>-ը',
                'summaryOptions' => ['class' => 'summary'],
                'dataProvider' => new ActiveDataProvider([
                    'query' => $dataProvider->query->andWhere(['status' => 1]),
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
                    'orders_date',
                    ...$action_column,
                ],
            ]); ?>
        </div>

<?php
}
else{ ?>

        <?php $dataProvider->pagination = false; ?>
            <?= GridView::widget([
                'tableOptions' => [
                    'class'=>'table chatgbti_',
                ],
                'options' => [
                    'class' => 'summary deletesummary'
                ],
                'summary' => 'Ցուցադրված է <b>{totalCount}</b>-ից <b>{begin}-{end}</b>-ը',
                'summaryOptions' => ['class' => 'summary'],
                'dataProvider' => new ActiveDataProvider([
                    'query' => $dataProvider->query->andWhere($status ?? ['status' => 1]),
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
<?php } ?>
    </div>
