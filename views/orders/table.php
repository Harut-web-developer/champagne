<?php

use app\widgets\CustomLinkPager;

/** @var yii\web\View $this */
/** @var app\models\Orders $model */
/** @var yii\widgets\ActiveForm $form */

?>
<?php if ($model->id){
//    echo "<pre>";
//    var_dump($model);
//    var_dump($users);
//    var_dump($clients);
//    var_dump($nomenclatures);
//    var_dump($order_items);
//    var_dump($total);
//    die;
    ?>
<span class="non-active">Վաճառք</span>
<table class="table">
    <thead>
    <tr>
        <th scope="col">Օգտատեր</th>
        <th scope="col">Հաճախորդ</th>
        <th scope="col">Ընդհանուր գումար</th>
        <th scope="col">Ընդհանուր քանակ</th>
        <th scope="col">Մեկնաբանություն</th>
        <th scope="col">Պատվերի ամսաթիվ</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td><?=$users[$model->user_id]; ?></td>
        <td><?=$clients[$model->user_id]; ?></td>
        <td><?=$total?></td>
        <td><?=$users[$model->user_id]; ?></td>
        <td><?=$users[$model->user_id]; ?></td>
        <td><?=$users[$model->user_id]; ?></td>
    </tr>
    </tbody>
</table>
<?php } ?>