<tr>
    <th>№</th>
    <th>Անուն</th>
    <th>Քանակ</th>
    <th>Գինը առանց ԱԱՀ-ի</th>
    <th>Գինը ներառյալ ԱԱՀ-ն</th>
</tr>
<?php
$itemsArray = [];
foreach ($document_items as $keys => $document_item){
$itemsArray[] = $document_item['nom_id'];
?>
<tr class="oldTr" id="tr_<?=$document_item['nom_id']?>">
    <td>
        <span><?=$keys + 1?></span>
        <input class="docItemsId" type="hidden" name="document_items[]" value="<?=$document_item['id']?>">
        <input class="itemsId" type="hidden" name="items[]" value="<?=$document_item['nom_id']?>">
    </td>
    <td class="name"><?=$document_item['name']?></td>

    <td class="count"style="justify-content: center; text-align: center;">
        <span name="count_[]" class="form-control countDocuments"><?=$document_item['count']?></span>
    </td>

    <td class="count"style="justify-content: center; text-align: center;">
        <span name="price[]" class="form-control PriceDocuments"><?=$document_item['price']?></span>
    </td>

    <td class="pricewithaah">
        <span><?=number_format($document_item['price_with_aah'],2,'.', '')?></span>
        <input type="hidden" name="pricewithaah[]" value="<?=number_format($document_item['price_with_aah'],2,'.', '')?>" class="form-control PriceWithaah">
    </td>
</tr>
<?php
}
?>