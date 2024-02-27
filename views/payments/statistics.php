<?php
$this->params['sub_page'] = $sub_page;
$this->params['date_tab'] = $date_tab;

?>
<div class="card">
    <h5 class="card-header">Վիճակագրություն</h5>
    <div class="table-responsive text-nowrap">
        <table class="table">
            <thead>
            <tr>
                <th>#</th>
                <th>Հաճախորդ</th>
                <th>Ընդհանուր պարտք</th>
                <th>Վճարված գումար</th>
            </tr>
            </thead>
            <tbody class="table-border-bottom-0">
            <?php
                foreach ($statistics as $keys => $statistic){
                ?>
                    <tr>
                        <td><?=$keys + 1?></td>
                        <td><?=$statistic['name']?></td>
                        <td><?= array_sum(array_column($statistic['orders'] , 'total_price'))?></td>
                        <td><?= array_sum(array_column($statistic['payments'] , 'payment_sum'))?></td>
                    </tr>
                <?php
                }
            ?>
            </tbody>
        </table>
    </div>
</div>
