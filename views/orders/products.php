<?php
//echo "<pre>";
//var_dump($dataProvider);
//echo "</pre>";
//exit();
?>
<div class="card">
<!--    <h5 class="card-header">Table Basic</h5>-->
    <div class="table-responsive text-nowrap">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Մենեջեր</th>
                    <th>ԱՆՎԱՆԱԿԱՐԳ</th>
                    <th>ՔԱՆԱԿ</th>
                    <th>ԳԻՆ</th>
                    <th>Պատվերի ամսաթիվ</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
            <?php if (!empty($dataProvider)){
               foreach ($dataProvider as $key => $value){?>
                   <tr>
                       <td><?=$key + 1?></td>
                       <td><?=$value['user_name']?></td>
                       <td><?=$value['name']?></td>
                       <td><?=round($value['count'])?></td>
                       <td><?=number_format($value['price'],2,',','')?></td>
                       <td><?=$value['orders_date']?></td>
                   </tr>

               <?php }}?>
            </tbody>
        </table>
    </div>
</div>
