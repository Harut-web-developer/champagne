<?php
$this->params['sub_page'] = $sub_page;
$this->params['date_tab'] = $date_tab;

?>
<div class="titleAndPrevPage">
    <i class='bx bxs-log-out iconPrevPage' onclick="window.location = document.referrer"></i>
</div>
<table class="table">
    <thead>
    <tr>
        <th scope="col">Անվանակարգ</th>
        <th scope="col">Օգտատեր</th>
        <th scope="col">Հաճախորդներ</th>
    </tr>
    </thead>
    <tbody class="searchbody">
    <?php
//    echo "<pre>";
//    var_dump($res);
//    var_dump(max(count($res['query_nomenclature']), count($res['query_users']), count($res['query_clients'])));
//    die;
    $maxRows = max(count($res['query_nomenclature']), count($res['query_users']), count($res['query_clients']));
    for ($i = 0; $i < $maxRows; $i++) {
        ?>
        <tr>
            <td>
                <a href="<?= isset($res['query_nomenclature'][$i]['id']) ? Yii::$app->urlManager->createUrl(['nomenclature/view', 'id' => $res['query_nomenclature'][$i]['id']]) : '#' ?>" class="nav-link">
                    <?= isset($res['query_nomenclature'][$i]['name']) ? $res['query_nomenclature'][$i]['name'] : '' ?>
                </a>
            </td>
            <td>
                <a href="<?= isset($res['query_users'][$i]['id']) ? Yii::$app->urlManager->createUrl(['users/view', 'id' => $res['query_users'][$i]['id']]) : '#' ?>" class="nav-link">
                    <?= isset($res['query_users'][$i]['name']) ? $res['query_users'][$i]['name'] : '' ?>
                </a>
            </td>
            <td>
                <a href="<?= isset($res['query_clients'][$i]['id']) ? Yii::$app->urlManager->createUrl(['clients/view', 'id' => $res['query_clients'][$i]['id']]) : '#' ?>" class="nav-link">
                    <?= isset($res['query_clients'][$i]['name']) ? $res['query_clients'][$i]['name'] : '' ?>
                </a>
            </td>
        </tr>
        <?php
    }
    ?>
    </tbody>
</table>
