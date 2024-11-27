<?php
$this->params['sub_page'] = $sub_page;
$this->params['date_tab'] = $date_tab;
if (!isset($res['query_nomenclature'])){
    $count_nom = 0;
}else{
    $count_nom = count($res['query_nomenclature']);
}
if (!isset($res['query_users'])){
    $count_users = 0;
}else{
    $count_users = count($res['query_users']);
}
if (!isset($res['query_clients'])){
    $count_clients = 0;
}else{
    $count_clients = count($res['query_clients']);
}
?>
<div class="titleAndPrevPage">
    <i class='bx bxs-log-out iconPrevPage' onclick="window.location = document.referrer"></i>
</div>
<table class="table">
    <thead>
    <tr>
        <?php if ($count_nom != 0){ ?>
            <th scope="col">Անվանակարգ</th>
        <?php }
        if ($count_users != 0){ ?>
            <th scope="col">Օգտատեր</th>
        <?php }
        if ($count_clients != 0){ ?>
            <th scope="col">Հաճախորդներ</th>
        <?php } ?>
    </tr>
    </thead>
    <tbody class="searchbody">
    <?php
//    echo "<pre>";
//    var_dump($res);
//    var_dump(max(count($res['query_nomenclature']), count($res['query_users']), count($res['query_clients'])));
//    die;

    $maxRows = max($count_nom, $count_users, $count_clients);
    for ($i = 0; $i < $maxRows; $i++) {
        ?>
        <tr>
            <?php if ($count_nom != 0){ ?>
                <td>
                    <a href="<?= isset($res['query_nomenclature'][$i]['id']) ? Yii::$app->urlManager->createUrl(['nomenclature/view', 'id' => $res['query_nomenclature'][$i]['id']]) : '#' ?>" class="nav-link">
                        <?= isset($res['query_nomenclature'][$i]['name']) ? $res['query_nomenclature'][$i]['name'] : '' ?>
                    </a>
                </td>
            <?php }?>
            <?php if ($count_users != 0){ ?>
                <td>
                    <a href="<?= isset($res['query_users'][$i]['id']) ? Yii::$app->urlManager->createUrl(['users/view', 'id' => $res['query_users'][$i]['id']]) : '#' ?>" class="nav-link">
                        <?= isset($res['query_users'][$i]['name']) ? $res['query_users'][$i]['name'] : '' ?>
                    </a>
                </td>
            <?php }?>
            <?php if ($count_clients != 0){ ?>
                <td>
                    <a href="<?= isset($res['query_clients'][$i]['id']) ? Yii::$app->urlManager->createUrl(['clients/view', 'id' => $res['query_clients'][$i]['id']]) : '#' ?>" class="nav-link">
                        <?= isset($res['query_clients'][$i]['name']) ? $res['query_clients'][$i]['name'] : '' ?>
                    </a>
                </td>
            <?php }?>
        </tr>
        <?php
    }
    ?>
    </tbody>
</table>
