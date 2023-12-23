<?php
$this->params['sub_page'] = $sub_page;
?>

<div class="discount-index">
    <div class="card">
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                <tr>
                    <th>#</th>
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
                    <th>Կարգավիճակ</th>
                </tr>
                </thead>
                <tbody class="table-border-bottom-0 sortable-ul">
                <?php
                foreach ($discount_sortable as $keys => $item) {?>
                    <tr>
                        <td>
                            <span class="fw-medium"><?=$keys + 1?></span>
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
                        <td>
                            <?php
                            if ($item['status'] == 0){
                                echo 'Ջնջված';
                            }elseif($item['status'] == 2){
                                echo 'Ժամկետանց';
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

