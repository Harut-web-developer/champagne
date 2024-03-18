<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\ManagerDeliverCondition $model */

$this->title = $model->managerName->name;
$this->params['breadcrumbs'][] = ['label' => 'Manager Deliver Conditions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['sub_page'] = $sub_page;
$this->params['date_tab'] = $date_tab;
\yii\web\YiiAsset::register($this);
?>
<div class="manager-deliver-condition-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'manager_id',
                'value' => function ($model) {
                    if ($model->managerName) {
                        return $model->managerName->name;
                    } else {
                        return 'Դատարկ';
                    }
                }
            ],
            [
                'attribute' => 'deliver_id',
                'value' => function ($model) {
                    if ($model->deliverName_) {
                        $res = '';
                        for ($i = 0; $i < count($model->deliverName_); $i++) {
                            $res .= $model->deliverName_[$i]['name'] . ', ';
                        }
                        return $res;
                    } else {
                        return 'Դատարկ';
                    }
                }
            ],
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
