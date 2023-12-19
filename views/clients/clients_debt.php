<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Clients $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Clients', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['sub_page'] = $sub_page;
\yii\web\YiiAsset::register($this);
?>
<div class="clients-view">
    <div class="card">
        <h5 class="card-header">Վիճակագրություն</h5>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                <tr>
                    <th>#</th>
                    <th>order number</th>
                    <th>order debt</th>
                    <th>payment sum</th>
                    <th>Balance</th>
                </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                <?php
                $debt_total = 0;
                //                echo '<pre>';
                //                var_dump($payments);
                //                var_dump($client_orders);
                foreach ($client_orders as $keys => $client_order){ ?>
                    <tr>
                        <td><?= $keys + 1 ?></td>
                        <td><?= $client_order['id'] ?></td>
                        <td><?= $client_order['debt'] ?></td>
                        <td><?= $payments; ?></td>
                        <?php if($payments){
                            if($payments >= intval($client_order['debt'])){
                                $balance_order = 0;
                                $payments -= intval($client_order['debt']);
                            } else {
                                $balance_order =  intval($client_order['debt']) - $payments;
                                $debt_total += intval($client_order['debt']) - $payments;
                                $payments = 0;
                            }
                        } else {
                            $debt_total += intval($client_order['debt']) - $payments;
                        } ?>
                        <td><?= (@$debt_total) ? $debt_total : @$balance_order ?></td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
    <!--    <h1>--><?php //= Html::encode($this->title) ?><!--</h1>-->
    <!---->
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
    <!---->
    <!--    --><?php //= DetailView::widget([
    //        'model' => $model,
    //        'attributes' => [
    //            'id',
    //            'route_id',
    //            'name',
    //            'location',
    //            'phone',
    //            'status',
    //            'created_at',
    //            'updated_at',
    //        ],
    //    ]) ?>

</div>
