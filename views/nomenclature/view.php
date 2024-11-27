<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Nomenclature $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Nomenclatures', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['sub_page'] = $sub_page;
$this->params['date_tab'] = $date_tab;

\yii\web\YiiAsset::register($this);
?>
<div class="nomenclature-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
<!--        --><?php //= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
<!--        --><?php //= Html::a('Delete', ['delete', 'id' => $model->id], [
//            'class' => 'btn btn-danger',
//            'data' => [
//                'confirm' => 'Are you sure you want to delete this item?',
//                'method' => 'post',
//            ],
//        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            [
                'attribute' => 'image',
                'format' => 'raw',
                'value' => function($model){
                    return Html::img(Yii::getAlias('/upload/'). $model->image,[
                        'alt'=>'yii2 - картинка в gridview',
                        'style' => 'width:130px;'
                    ]);
                },
            ],
            'price',
            'cost',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
