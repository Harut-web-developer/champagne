<?php
$this->params['sub_page'] = $sub_page;
?>
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
    $maxRows = max(count($res['query_nomenclature']), count($res['query_users']), count($res['query_clients']));
    for ($i = 0; $i < $maxRows; $i++) {
        ?>
        <tr>
            <td><?= isset($res['query_nomenclature'][$i]['name']) ? $res['query_nomenclature'][$i]['name'] : '' ?></td>
            <td><?= isset($res['query_users'][$i]['name']) ? $res['query_users'][$i]['name'] : '' ?></td>
            <td><?= isset($res['query_clients'][$i]['name']) ? $res['query_clients'][$i]['name'] : '' ?></td>
        </tr>
        <?php
    }
    ?>
    </tbody>
</table>
