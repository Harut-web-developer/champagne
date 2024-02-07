<?php

use app\models\ManagerDeliverCondition;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use app\widgets\CustomGridView;

/** @var yii\web\View $this */
/** @var app\models\ManagerDeliverConditionSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Ստեցծել մենեջեր-առաքիչ';
$this->params['breadcrumbs'][] = $this->title;
$this->params['sub_page'] = $sub_page;
$this->params['date_tab'] = $date_tab;
?>
<div class="manager-deliver-condition-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Ստեցծել մենեջեր-առաքիչ', ['create'], ['class' => 'btn rounded-pill btn-secondary']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <div class="card pageStyle">
        <table class="table">
            <thead>
                <tr>
                    <td>Manager</td>
                    <td>Araqichner</td>
                </tr>
            </thead>
          <tbody>
        <?php if(!empty($managers)){
            foreach ($managers as $manager => $manager_simple){ ?>
                <tr>
                    <td><?php echo $manager_simple->username;?></td>
                    <td><a href="/edite"></a>(<?php echo  count($manager_simple->drivers($manager_simple->id));?>)
<!--                        <select name="" id="">-->
<!--                            --><?php //if(count($manager_simple->drivers($manager_simple->id))){
//                                foreach ($manager_simple->drivers($manager_simple->id) as $m =>$v){ ?>
<!--                                    <option value="">--><?php //echo $v->id;?><!--</option>-->
<!--                            --><?// } } ?>
<!--                        </select>-->
                    </td>
                </tr>
        <? }} ?>
          </tbody>
        </table>
    </div>
</div>
