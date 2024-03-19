<?php
$itemsPerPage = 10;
$totalPages = ceil(count($statistics) / $itemsPerPage);
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$startIndex = ($page - 1) * $itemsPerPage;
$statisticsPerPage = array_slice($statistics, $startIndex, $itemsPerPage);

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
            foreach ($statisticsPerPage as $keys => $statistic){
                ?>
                <tr>
                    <td><?=($startIndex + $keys + 1)?></td>
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
    <nav aria-label="Page navigation">
        <ul class="pagination pagination-sm customPages">
            <li class="<?= $page == 1 ? 'prev disabled' : 'prev' ?> page-item">
                <?php if ($page > 1) : ?>
                    <a class="page-link" href="?page=<?= $page - 1 ?>" aria-label="Previous">
                        <i class="tf-icon bx bx-chevrons-left"></i>
                    </a>
                <?php else : ?>
                    <span>«</span>
                <?php endif; ?>
            </li>

            <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                <li class="<?= $i == $page ? 'active' : '' ?> page-item">
                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <li class="<?= $page == $totalPages ? 'next disabled' : 'next' ?> page-item">
                <?php if ($page < $totalPages) : ?>
                    <a class="page-link" href="?page=<?= $page + 1 ?>" aria-label="Next">
                        <i class="tf-icon bx bx-chevrons-right"></i>
                    </a>
                <?php else : ?>
                    <span>»</span>
                <?php endif; ?>
            </li>
        </ul>
    </nav>

</div>

