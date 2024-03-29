<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Clients $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Clients', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['sub_page'] = $sub_page;
$this->params['date_tab'] = $date_tab;

\yii\web\YiiAsset::register($this);
?>
<div class="discount-view">

    <h1><?= Html::encode($this->title) ?></h1>

<!--    <p>-->
<!--        --><?php //= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
<!--        --><?php //= Html::a('Delete', ['delete', 'id' => $model->id], [
//            'class' => 'btn btn-danger',
//            'data' => [
//                'confirm' => 'Are you sure you want to delete this item?',
//                'method' => 'post',
//            ],
//        ]) ?>
<!--    </p>-->

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'route_id',
                'value' => function ($model) {
                    if ($model->routeName) {
                        return $model->routeName->route;
                    } else {
                        return 'Դատարկ';
                    }
                }
            ],
            'sort_',
            [
                'attribute' => 'client_warehouse_id',
                'value' => function ($model) {
                    if ($model->warehouseName) {
                        return $model->warehouseName->name;
                    } else {
                        return 'Դատարկ';
                    }
                }
            ],
            'name',
            'location',
            'phone',
//            'status',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
