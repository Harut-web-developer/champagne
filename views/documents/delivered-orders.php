
<label for="deliveredorders">Հաստատված փաստաթղթեր</label>
<select id="deliveredorders" class="js-example-basic-single form-control" name="order_id">
    <option  value=""></option>
    <?php foreach ($delivered_documents as $delivered_document){ ?>
        <option value="<?=$delivered_document['id']?>">Հաստատված պատվեր <?=$delivered_document['orders_date']?></option>
    <?php } ?>
</select>
<script>
    $('.js-example-basic-single').select2();
</script>
