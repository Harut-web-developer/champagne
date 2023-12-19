<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Discount $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Discounts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['sub_page'] = $sub_page;
\yii\web\YiiAsset::register($this);
if ($model->id){
    if ($model->discount_check == '1'){
        $value_check = 'Կիրառել մյուս զեղչերի հետ';
    }elseif ($model->discount_check == '0'){
        $value_check = 'Կիրառելի չէ մյուս զեղչերի հետ';
    }else{
        $value_check = '';
    }

    if ($model->discount_option == '1'){
        $value = 'Մեկ անգամյա';
    }elseif ($model->discount_option == '2'){
        $value = 'Բազմակի';
    }else{
        $value = '';
    }

    if ($model->type == 'percent'){
        $value_type = 'Տոկոսով';
    }elseif ($model->type == 'money'){
        $value_type = 'Գումարով';
    }else{
        $value_type = '';
    }

    if ($model->discount_filter_type == 'count'){
        $value_filter_type = 'Ըստ քանակի';
    }elseif ($model->discount_filter_type == 'price'){
        $value_filter_type = 'Ըստ գնի';
    }else{
        $value_filter_type = '';
    }

    if (empty($model->min)){
        $value_min = '';
    }else{
        $value_min = $model->min;
    }

    if (empty($model->max)){
        $value_max = '';
    }else{
        $value_max = $model->max;
    }

}
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
            'id',
            [
                'attribute' => 'Տեսակ',
                'value' => $value_type,
            ],            'discount',
            [
                'attribute' => 'Ստուգում',
                'value' => $value_check,
            ],
            'discount_sortable',
            [
                'attribute' => 'Զեղչի ձև',
                'value' => $value,
            ],
//            'discount_filter_type',
            [
                'attribute' => 'Ֆիլտրել',
                'value' => $value_filter_type,
            ],
            [
                'attribute' => 'Նվազագույն',
                'value' => $value_min,
            ],
            [
                'attribute' => 'Առավելագույն',
                'value' => $value_max,
            ],
            'start_date',
            'end_date',
//            'status',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
