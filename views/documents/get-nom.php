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
//                if ($nomenclature['count_balance'] != 0){?>


                <tr class="documentsTableTr">
                    <td>
                        <span><?=$keys + 1?></span>
                        <input class="nom_id" data-id="<?=$nomenclature['id']?>" type="hidden">
                    </td>
                    <td class="imageNom"><img src="/upload/<?=$nomenclature['image']?>"></td>
                    <td class="documentsName"><?=$nomenclature['name']?></td>
                    <td class="documentsCount">
                        <input type="number" class="form-control documentsCountInput" value="<?= $id_count[$nomenclature['id']] ?? '' ?>">
                        <input class="documentsPriceInput" type="hidden" value="<?=$nomenclature['price']?>">
                    </td>
                </tr>
                <?php
//                }

            }
            ?>
            </tbody>
        </table>
    </div>
</div>
<?php $page = @$_GET['paging'] ?? 1; ?>
<?php $count = intval(ceil($total/10)) ;
if (@$_GET['nomenclature'] != ''){
    $count = 1 ;
}
?>
<?php if(isset($urlId)){ ?>
    <nav aria-label="Page navigation example" class="pagination">
        <ul class="pagination pagination-sm">
            <li class="page-item prev <?= ($page <= 1) ? 'disabled' : '' ?>">
                <a class="page-link by_ajax_update" href="#" data-href="/documents/get-nomiclature-update?paging=<?= $page-1 ?>"><i class="tf-icon bx bx-chevrons-left"></i></a>
            </li>
            <?php for ($i = 1;$i <= $count; $i++){ ?>
                <?php if($i > 0 && $i <= $count+1){?>
                    <li class="page-item <?= ($page==$i) ? 'active' : '' ?> page-item-active-insearche">
                        <a class="page-link by_ajax_update" href="#" data-href="/documents/get-nomiclature-update?paging=<?= $i ?>"><?= $i ?>
                        </a>
                    </li>
                    <?php
                    if (!empty($_GET['nomenclature'])) {
                        echo "<script>
                    $(document).ready(function() {
                        $('.page-item-active-insearche').addClass('active');
                    });
                    $('.page-item-active-insearche').on('click', function(event) {
                        $('.searchForDocumentUpdate').val('');
                    });
                  </script>";
                    }
                    ?>
                <?php } ?>
            <?php } ?>
            <?php if(intval($page) < $count){ ?>
                <li class="page-item next">
                    <a class="page-link by_ajax_update" href="#" data-href="/documents/get-nomiclature-update?paging=<?= $page+1 ?>"><i class="tf-icon bx bx-chevrons-right"></i></a>
                </li>
            <?php } ?>
        </ul>
    </nav>
<?php } else { ?>
<nav aria-label="Page navigation example" class="pagination">
    <ul class="pagination pagination-sm">
        <li class="page-item prev <?= ($page <= 1) ? 'disabled' : '' ?>">
            <a class="page-link by_ajax" href="#" data-href="/documents/get-nomiclature?paging=<?= $page-1 ?>"><i class="tf-icon bx bx-chevrons-left"></i></a>
        </li>
        <?php for ($i = 1;$i <= $count; $i++){ ?>
            <?php if($i > 0 && $i <= $count+1){?>
                <li class="page-item <?= ($page==$i) ? 'active' : '' ?> page-item-active-insearche">
                    <a class="page-link by_ajax" href="#" data-href="/documents/get-nomiclature?paging=<?= $i ?>"><?= $i ?>
                    </a>
                </li>
                <?php
                if (!empty($_GET['nomenclature'])) {
                    echo "<script>
                    $(document).ready(function() {
                        $('.page-item-active-insearche').addClass('active');
                    });
                    $('.page-item-active-insearche').on('click', function(event) {
                        $('.searchForDocument').val('');
                    });
                  </script>";
                }
                ?>
            <?php } ?>
        <?php } ?>

        <?php if(intval($page) < $count){ ?>
            <li class="page-item next">
                <a class="page-link by_ajax" href="#" data-href="/documents/get-nomiclature?paging=<?= $page+1 ?>"><i class="tf-icon bx bx-chevrons-right"></i></a>
            </li>
        <?php } ?>
    </ul>
</nav>
<?php } ?>
