<input class="form-control col-md-3 mb-3 searchForDocument" value="<?=$search_name?>" type="search" placeholder="Որոնել...">
<div class="card">
    <div class="table-responsive text-nowrap">
        <table class="table">
            <thead>
            <tr>
                <th>#</th>
                <th>Ընտրել</th>
                <th>Նկար</th>
                <th>Անուն</th>
                <th>Քանակ</th>
            </tr>
            </thead>
            <tbody class="table-border-bottom-0 tbody_">
            <?php
            foreach ($nomenclatures as $keys => $nomenclature){
                ?>
                <tr class="documentsTableTr">
                    <td><?=$keys + 1?></td>
                    <td>
                        <input data-id="<?=$nomenclature['id']?>" type="checkbox">
                    </td>
                    <td class="imageNom"><img src="/upload/<?=$nomenclature['image']?>"></td>
                    <td class="documentsName"><?=$nomenclature['name']?></td>
                    <td class="documentsCount">
                        <input type="number" class="form-control documentsCountInput">
                        <input class="documentsPriceInput" type="hidden" value="<?=$nomenclature['price']?>">
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
<?php  $count = intval(ceil($total/10)) ; ?>
<nav aria-label="Page navigation example" class="pagination">
    <ul class="pagination pagination-sm">
        <li class="page-item prev <?= ($page <= 1) ? 'disabled' : '' ?>">
            <a class="page-link by_ajax" href="#" data-href="/documents/get-nomiclature?paging=<?= $page-1 ?>"><i class="tf-icon bx bx-chevrons-left"></i></a>
        </li>
        <?php for ($i = 1;$i <= $count; $i++){ ?>
            <?php if($i > 0 && $i <= $count+1){ ?>
                <li class="page-item <?= ($page==$i) ? 'active' : '' ?>">
                    <a class="page-link by_ajax" href="#" data-href="/documents/get-nomiclature?paging=<?= $i ?>"><?= $i ?>
                    </a>
                </li>
            <?php } ?>
        <?php } ?>

        <?php if(intval($page) < $count){ ?>
            <li class="page-item next">
                <a class="page-link by_ajax" href="#" data-href="/documents/get-nomiclature?paging=<?= $page+1 ?>"><i class="tf-icon bx bx-chevrons-right"></i></a>
            </li>
        <?php } ?>
    </ul>
</nav>