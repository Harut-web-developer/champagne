<ul class="p-0 m-0">
    <?php
    if (!empty($clients_payment)){
        foreach ($clients_payment as $item){
            ?>
            <li class="d-flex mb-4 pb-1">
                <div class="avatar flex-shrink-0 me-3">
                    <img src="/img/icons/unicons/chart.png" alt="User" class="rounded">
                </div>
                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                    <div class="me-2">
                        <h6 class="mb-0"><?=$item['name']?></h6>
                        <small class="text-muted d-block mb-1">Վճարված</small>
                    </div>
                    <div class="user-progress d-flex align-items-center gap-1">
                        <h6 class="mb-0"><?=number_format($item['payment_sum']) . ' դր.'?></h6>
                        <!--                                            <span class="text-muted">դր.</span>-->
                    </div>
                </div>
            </li>
            <?php
        }
    }else{
        ?>
        <li class="d-flex mb-4 pb-1">Վճարված ապրանք չկա</li>
        <?php
    }
    ?>
</ul>
