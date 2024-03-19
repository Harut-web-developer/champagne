<?php
$itemsPerPage = 20;
$totalPages = ceil(count($discount_sortable) / $itemsPerPage);
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$startIndex = ($page - 1) * $itemsPerPage;
$discount_sortablePerPage = array_slice($discount_sortable, $startIndex, $itemsPerPage);

$this->params['sub_page'] = $sub_page;
$this->params['date_tab'] = $date_tab;
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
                foreach ($discount_sortablePerPage as $keys => $items) {?>
                    <tr>
                        <td><?=($startIndex + $keys + 1)?></td>
                        <td>
                            <?php
                            if ($items['name'] == ''){
                                echo 'Դատարկ';
                            }else{
                                echo $items['name'];
                            }?>
                        </td>
                        <td>
                            <?php
                            if ($items['type'] == 'percent'){
                                echo 'Տոկոս';
                            }else{
                                echo 'Գումար';
                            }?>
                        </td>
                        <td><?=$items['discount']?></td>
                        <td>
                            <?php
                            if ($items['start_date'] == ''){
                                echo 'Դատարկ';
                            }else{
                                echo $items['start_date'];
                            }?>
                        </td>
                        <td>
                            <?php
                            if ($items['end_date'] == ''){
                                echo 'Դատարկ';
                            }else{
                                echo $items['end_date'];
                            }?>
                        </td>
                        <td>
                            <?php
                            if ($items['discount_check'] == '1'){
                                echo 'Կիրառել մյուս զեղչերի հետ';
                            }elseif ($items['discount_check'] == '0'){
                                echo 'Կիրառելի չէ մյուս զեղչերի հետ';
                            }
                            ?>
                        </td>
                        <td><?=$items['discount_sortable']?></td>
                        <td>
                            <?php
                            if ($items['discount_option'] == '1'){
                                echo 'Մեկ անգամյա';
                            }elseif ($items['discount_option'] == '2'){
                                echo 'Բազմակի';
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            if ($items['discount_filter_type'] == 'count'){
                                echo 'Ըստ քանակի';
                            }elseif ($items['discount_filter_type'] == 'price'){
                                echo 'Ըստ գնի';
                            }else{
                                echo 'Դատարկ';
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            if (empty($items['min'])){
                                echo 'Դատարկ';
                            }else{
                                echo $items['min'];
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            if (empty($items['max'])){
                                echo 'Դատարկ';
                            }else{
                                echo $items['max'];
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            if ($items['status'] == 0){
                                echo 'Ջնջված';
                            }elseif($items['status'] == 2){
                                echo 'Ժամկետանց';
                            }
                            ?>
                        </td>
                    </tr>
                <?php }?>

                </tbody>
            </table>
        </div>
        <?php if (count($discount_sortable) > $itemsPerPage){ ?>
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
        <?php } ?>
    </div>
</div>