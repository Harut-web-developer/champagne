<?php

use app\models\Discount;
use app\models\Users;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;


/** @var yii\web\View $this */
/** @var app\models\DiscountSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Զեղչեր';
$this->params['breadcrumbs'][] = $this->title;
$this->params['sub_page'] = $sub_page;
$this->params['date_tab'] = $date_tab;
$have_access_create = Users::checkPremission(41);
$have_access_update = Users::checkPremission(42);
$have_access_delete = Users::checkPremission(43);

?>
<div class="discount-index">
    <div class="titleAndPrevPage">
        <i class='bx bxs-log-out iconPrevPage' onclick="window.location = document.referrer"></i>
        <h3><?= Html::encode($this->title) ?></h3>
    </div>
        <p>
            <?php if($have_access_create){ ?>
                <?= Html::a('Ստեղծել զեղչ', ['create'], ['class' => 'btn rounded-pill btn-secondary']) ?>
            <?php } ?>
        </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <div class="card">
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Գործողուրյուն</th>
                    <th>Անուն</th>
                    <th>Տեսակ</th>
                    <th>Տոկոս</th>
                    <th>Զեղչի սկիզբ</th>
                    <th>Զեղչի ավարտ</th>
                    <th>Ստուգում</th>
                    <th>Զեղչի տեսակավորում</th>
                    <th>Զեղչի ձև</th>
                    <th>Զեղչի տեսակ</th>
                    <th>Նվազագույն</th>
                    <th>Առավելագույն</th>
                </tr>
                </thead>
                <tbody class="table-border-bottom-0 sortable-ul">
                <?php
                foreach ($discount_sortable as $keys => $item) {?>
                    <tr>
                        <td>
                            <input type="hidden" name="sort[]" value="<?=$item['id']?>">
                            <span class="fw-medium"><?=$keys + 1?></span>
                        </td>
                        <td>
                            <?php
                            if ($have_access_update){
                                ?>
                                <a href="/discount/update?id=<?=$item['id']?>">
                                    <svg aria-hidden="true" style="display:inline-block;font-size:inherit;height:1em;overflow:visible;vertical-align:-.125em;width:1em" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M498 142l-46 46c-5 5-13 5-17 0L324 77c-5-5-5-12 0-17l46-46c19-19 49-19 68 0l60 60c19 19 19 49 0 68zm-214-42L22 362 0 484c-3 16 12 30 28 28l122-22 262-262c5-5 5-13 0-17L301 100c-4-5-12-5-17 0zM124 340c-5-6-5-14 0-20l154-154c6-5 14-5 20 0s5 14 0 20L144 340c-6 5-14 5-20 0zm-36 84h48v36l-64 12-32-31 12-65h36v48z"></path></svg>
                                </a>
                                <?php
                            }
                            ?>
                            <?php
                            if ($have_access_delete){
                                ?>
                                <a class="deleteBtn" style="color:red" href="/discount/delete?id=<?=$item['id']?>">
                                    <svg aria-hidden="true" style="display:inline-block;font-size:inherit;height:1em;overflow:visible;vertical-align:-.125em;width:.875em" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M32 464a48 48 0 0048 48h288a48 48 0 0048-48V128H32zm272-256a16 16 0 0132 0v224a16 16 0 01-32 0zm-96 0a16 16 0 0132 0v224a16 16 0 01-32 0zm-96 0a16 16 0 0132 0v224a16 16 0 01-32 0zM432 32H312l-9-19a24 24 0 00-22-13H167a24 24 0 00-22 13l-9 19H16A16 16 0 000 48v32a16 16 0 0016 16h416a16 16 0 0016-16V48a16 16 0 00-16-16z"></path></svg>
                                </a>
                                <?php
                            }
                            ?>

                        </td>
                        <td>
                            <?php
                            if ($item['name'] == ''){
                                echo 'Դատարկ';
                            }else{
                                echo $item['name'];
                            }?>
                        </td>
                        <td>
                            <?php
                                if ($item['type'] == 'percent'){
                                    echo 'Տոկոս';
                                }else{
                                    echo 'Գումար';
                                }?>
                        </td>
                        <td><?=$item['discount']?></td>
                        <td>
                            <?php
                            if ($item['start_date'] == ''){
                                echo 'Դատարկ';
                            }else{
                                echo $item['start_date'];
                            }?>
                        </td>
                        <td>
                            <?php
                            if ($item['end_date'] == ''){
                                echo 'Դատարկ';
                            }else{
                                echo $item['end_date'];
                            }?>
                        </td>
                        <td>
                            <?php
                            if ($item['discount_check'] == '1'){
                                echo 'Կիրառել մյուս զեղչերի հետ';
                            }elseif ($item['discount_check'] == '0'){
                                echo 'Կիրառելի չէ մյուս զեղչերի հետ';
                            }
                            ?>
                        </td>
                        <td><?=$item['discount_sortable']?></td>
                        <td>
                            <?php
                            if ($item['discount_option'] == '1'){
                                echo 'Մեկ անգամյա';
                            }elseif ($item['discount_option'] == '2'){
                                echo 'Բազմակի';
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            if ($item['discount_filter_type'] == 'count'){
                                echo 'Ըստ քանակի';
                            }elseif ($item['discount_filter_type'] == 'price'){
                                echo 'Ըստ գնի';
                            }else{
                                echo 'Դատարկ';
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            if (empty($item['min'])){
                                echo 'Դատարկ';
                            }else{
                                echo $item['min'];
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            if (empty($item['max'])){
                                echo 'Դատարկ';
                            }else{
                                echo $item['max'];
                            }
                            ?>
                        </td>

                    </tr>
               <?php }?>

                </tbody>
            </table>
        </div>
<!--    --><?php //= GridView::widget([
//        'summary' => 'Ցուցադրված է <b>{totalCount}</b>-ից <b>{begin}-{end}</b>-ը',
//        'summaryOptions' => ['class' => 'summary'],
//        'dataProvider' => new ActiveDataProvider([
//            'query' => $dataProvider->query->andWhere(['status' => '1']),
////                'pagination' => [
////                    'pageSize' => 20,
////                ],
//        ]),
//        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],
//
//            'type',
//            'discount',
//            [
//                'attribute' => 'Զեղչի սկիզբ',
//                'value' => function ($model) {
//                    if ($model->start_date) {
//                        return $model->start_date;
//                    } else {
//                        return 'Դատարկ';
//                    }
//                }
//            ],
//            [
//                'attribute' => 'Զեղչի ավարտ',
//                'value' => function ($model) {
//                    if ($model->end_date) {
//                        return $model->end_date;
//                    } else {
//                        return 'Դատարկ';
//                    }
//                }
//            ],
//            ...$action_column,
//        ],
//    ]); ?>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

<script>
    $('.sortable-ul').sortable({
        stop: function() {
            $.ajax({
                url: '/discount/save',
                method: 'post',
                data: $('.sortable-ul input').serialize(),
                success: function(response) {
                },
            });
        }
    });
</script>