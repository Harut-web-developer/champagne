<label class="label_clients" for="singleClients">Հաճախորդ</label>
<select id="singleClients" class="js-example-basic-single form-control" name="clients_id">
<!--    <option  value=""></option>-->
    <?php if (isset($clients)){
        foreach ($clients as $client){
            ?>
            <option  value="<?= $client['id'] ?>"><?= $client['name'] ?></option>
        <?php } }?>
</select>
<script>
    $('.js-example-basic-single').select2();
</script>