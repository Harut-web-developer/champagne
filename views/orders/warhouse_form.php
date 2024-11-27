<input value="<?= $warehouse ?>" class="warehouse_id" type="hidden" name="warehouse">
<div class="clientLimit">
    <div class="limitPrice">
        <span class="limitTitle">Պարտքի սահմանաչափ</span>
        <span class="limitValue"><?php echo $debt_limit['debt_limit'] !== null ? $debt_limit['debt_limit'] : 0 ?></span>
    </div>
    <div class="clientPriceDebt">
        <span class="debtTitle">Դեբիտորական պարտք</span>
        <span class="debtValue"><?php echo $client_debt_price['client_debt_price'] !== null ? $client_debt_price['client_debt_price'] : 0 ?></span>
    </div>
</div>