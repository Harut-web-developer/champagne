<label for="deliverorders">Առաքիչ</label>
<select id="deliverorders" class="js-example-basic-single form-control" name="Documents[deliver_id]">
    <option  value=""></option>
    <?php foreach ($manager_id as $deliver_value){ ?>
        <option value="<?=$deliver_value['id']?>"> <?=$deliver_value['name']?></option>
    <?php } ?>
</select>
<script>
    $('.js-example-basic-single').select2();
</script>
