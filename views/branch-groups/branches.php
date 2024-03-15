<?php
$this->params['sub_page'] = $sub_page;
$this->params['date_tab'] = $date_tab;
?>
<div class="card pageStyle">

<table class="table">
    <thead>
        <tr>
            <th>Հ/Հ</th>
            <th>Անուն</th>
            <th>Ընդհանուր պարտք</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $total = 0;
    foreach ($branches as $key => $branche){
        $total += floatval($branche['total_price']);
        ?>
        <tr>
            <td><?=$key + 1?></td>
            <td><?=$branche['name']?></td>
            <td><? if (!empty($branche['total_price'])){
                        echo number_format($branche['total_price'],2,',','') . 'դր.';
                    }else{
                        echo number_format(0,2,',','') . 'դր.';
                    }?>
            </td>
        </tr>
    <?php } ?>
        <tr>
            <td><?=count($branches) + 1?></td>
            <td></td>
            <td><?=number_format($total,2,',','') . 'դր.'?></td>
        </tr>
    </tbody>
</table>
</div>
