<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\GroupsName $model */

$this->title = $model->groups_name;
$this->params['breadcrumbs'][] = ['label' => 'Groups Names', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
$this->params['sub_page'] = $sub_page;

?>
<div class="groups-name-view">

    <h1><?= Html::encode($this->title) ?></h1>
    <div class="card">
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                <tr>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($clients_groups as $i => $value) {?>
                    <tr>
                        <td>
                            <?= $value['name'] ?>
                        </td>
                    </tr>
                <?php }?>

                </tbody>
            </table>
        </div>
    </div>
    <!--    --><?php //= DetailView::widget([
    //        'model' => $model,
    //        'attributes' => [
    //            'id',
    //            'groups_name',
    //        ],
    //    ]) ?>

</div>
