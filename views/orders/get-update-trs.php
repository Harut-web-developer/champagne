    <tr>
        <th>№</th>
        <th>Անուն</th>
        <th>Քանակ</th>
        <th>Զեղչ</th>
        <th>Գինը մինչև զեղչելը</th>
        <th>Զեղչված գին</th>
        <th>Ընդհանուր գումար</th>
        <th>Ընդհանուր զեղչված գումար</th>
    </tr>
    <?php
    $itemsArray = [];
    foreach($order_items as $keys => $item){
        $itemsArray[] = $item['product_id'];
        ?>
        <tr class="tableNomenclature fromDB">
            <td>
                <span class="acordingNumber"><?=$keys + 1?></span>
                <input class="orderItemsId" type="hidden" name="order_items[]" value="<?=$item['id']?>">
                <input class="prodId" type="hidden" name="product_id[]" value="<?=$item['product_id']?>">
                <input class="nomId"  type="hidden" name="nom_id[]" value="<?=$item['nom_id']?>">
                <input class="cost" type="hidden" name="cost[]" value="<?=$item['cost']?>">
                <input class="countDiscountId" type="hidden" name="count_discount_id[]" value="<?=$item['count_discount_id']?>">
            </td>
            <td class="name"><?=$item['name']?></td>
            <td class="count">
                <span name="count_[]" value="" class="form-control countProductForUpdate"><?=$item['count']?></span>
            </td>
            <td class="discount">
                <?php
                if ($item['discount'] == 0){
                    ?>
                    <span>0</span>
                    <input type="hidden" name="discount[]" value="0">
                    <?php
                }else{
                    ?>
                    <span><?=$item['discount'] / $item['count']?></span>
                    <input type="hidden" name="discount[]" value="<?=$item['discount'] / $item['count']?>">
                    <?php
                }
                ?>
            </td>
            <td class="beforePrice"><span><?=$item['beforePrice']?></span>
                <input type="hidden" name="beforePrice[]" value="<?=$item['beforePrice']?>">
            </td>
            <td class="price"><span><?=$item['price']?></span>
                <input type="hidden" name="price[]" value="<?=$item['price']?>">
            </td>
            <td class="totalBeforePrice">
                <span><?=$item['totalBeforePrice']?></span>
                <input type="hidden" name="total_before_price[]" value="<?=$item['totalBeforePrice']?>">
            </td>
            <td class="totalPrice">
                <span><?=$item['total_price']?></span>
                <input type="hidden" name="total_price[]" value="<?=$item['total_price']?>">
            </td>
        </tr>
    <?php }?>


