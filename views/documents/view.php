<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Documents $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Documents', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['sub_page'] = $sub_page;
$this->params['date_tab'] = $date_tab;
\yii\web\YiiAsset::register($this);

if ($model->id){
    if ($model->document_type === '1'){
        $value = 'Մուտք';
    }elseif ($model->document_type === '2'){
        $value = 'Ելք';
    }elseif ($model->document_type === '3'){
        $value = 'Տեղափոխություն';
    }else{
        $value = 'Խոտան';
    }
}
?>

<div class="documents-view">

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
            'id',
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
            'rate_value',
            [
                'attribute' => 'ՓԱՍՏԱԹՂԹԻ ՏԵՍԱԿԸ',
                'value' => $value,
            ],

            'comment',
            'date',
//            'status',
//            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
