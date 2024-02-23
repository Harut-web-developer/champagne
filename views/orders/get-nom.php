<?php
use app\models\Products;
?>
<div class="card">
    <div class="table-responsive text-nowrap">
        <table class="table">
            <thead>
            <tr>
                <th>#</th>
                <th>Նկար</th>
                <th>Անուն</th>
                <th>Քանակ</th>
            </tr>
            </thead>
            <tbody class="table-border-bottom-0 tbody_">
            <?php

            foreach ($nomenclatures as $keys => $nomenclature){
                ?>
                <tr class="addOrdersTableTr">
                    <td>
                        <span><?=$keys + 1?></span>
                        <input class="prodId" data-id="<?=$nomenclature['id']?>" type="hidden">
                        <input class="nomId" data-product="<?=$nomenclature['nomenclature_id']?>" type="hidden">
                    </td>
                    <td class="imageNom"><img src="/upload/<?=$nomenclature['image']?>"></td>
                    <td class="nomenclatureName"><?=$nomenclature['name']?></td>
                    <td class="ordersAddCount">
                        <input type="number" class="form-control ordersCountInput" step="1" min="1" value="<?= $id_count[$nomenclature['id']] ?? '' ?>">
                        <span>Մնացորդը՝ <?=$nomenclature['all_count_balance']?> </span>
                        <input class="ordersPriceInput" type="hidden" value="<?=$nomenclature['price']?>">
                        <input class="ordersCostInput" type="hidden" value="<?=$nomenclature['cost']?>">
                    </td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </div>
</div>

<?php $page = @$_GET['paging'] ?? 1; ?>
<?php  $count = intval(ceil($total/10)) ;
if (@$_GET['nomenclature'] != ''){
    $count = 1 ;
}
?>
<?php if(isset($urlId)){ ?>
<nav aria-label="Page navigation example" class="pagination">
    <ul class="pagination pagination-sm">
        <li class="page-item prev <?= ($page <= 1) ? 'disabled' : '' ?>">
            <a class="page-link by_ajax_update" href="#" data-href="/orders/get-nomiclature-update?paging=<?= $page-1 ?>"><i class="tf-icon bx bx-chevrons-left"></i></a>
        </li>
        <?php for ($i = 1;$i <= $count; $i++){ ?>
            <?php if($i > 0 && $i <= $count+1){?>
                <li class="page-item <?= ($page==$i) ? 'active' : '' ?> page-item-active-insearche">
                    <a class="page-link by_ajax_update" href="#" data-href="/orders/get-nomiclature-update?paging=<?= $i ?>"><?= $i ?></a>
                </li>
                <?php
                if (!empty($_GET['nomenclature'])) {
                    echo "<script>
                    $(document).ready(function() {
                        $('.page-item-active-insearche').addClass('active');
                    });
                    $('.page-item-active-insearche').on('click', function(event) {
                        $('.searchForOrderUpdate').val('');
                    });
                  </script>";
                }
                ?>
            <?php } ?>
        <?php } ?>

        <?php if(intval($page) < $count){ ?>
            <li class="page-item next">
                <a class="page-link by_ajax_update" href="#" data-href="/orders/get-nomiclature-update?paging=<?= $page+1 ?>"><i class="tf-icon bx bx-chevrons-right"></i></a>
            </li>
        <?php } ?>
    </ul>
</nav>
<?php } else { ?>
    <nav aria-label="Page navigation example" class="pagination">
        <ul class="pagination pagination-sm">
            <li class="page-item prev <?= ($page <= 1) ? 'disabled' : '' ?>">
                <a class="page-link by_ajax" href="#" data-href="/orders/get-nomiclature?paging=<?= $page-1 ?>"><i class="tf-icon bx bx-chevrons-left"></i></a>
            </li>
            <?php for ($i = 1;$i <= $count; $i++){ ?>
                <?php if($i > 0 && $i <= $count+1){?>
                    <li class="page-item <?= ($page==$i) ? 'active' : '' ?> page-item-active-insearche">
                        <a class="page-link by_ajax" href="#" data-href="/orders/get-nomiclature?paging=<?= $i ?>"><?= $i ?></a>
                    </li>

                    <?php
                    if (!empty($_GET['nomenclature'])) {
                        echo "<script>
                    $(document).ready(function() {
                        $('.page-item-active-insearche').addClass('active');
                    });
                    $('.page-item-active-insearche').on('click', function(event) {
                        $('.searchForOrder').val('');
                    });
                  </script>";
                    }
                    ?>
                <?php } ?>
            <?php } ?>

            <?php if(intval($page) < $count){ ?>
                <li class="page-item next">
                    <a class="page-link by_ajax" href="#" data-href="/orders/get-nomiclature?paging=<?= $page+1 ?>"><i class="tf-icon bx bx-chevrons-right"></i></a>
                </li>
            <?php } ?>
        </ul>
    </nav>
<?php } ?>