<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Users $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['sub_page'] = $sub_page;
\yii\web\YiiAsset::register($this);
?>
<div class="users-view">

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
            'id',
            'name',
            'username',
            'password',
            'email',
            'phone',
            'role_id',
            'auth_key',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
