<?php foreach ($premissions_check as $premission){?>
   <div class="premission-content-items" style="flex-basis:23%">
        <label for="premission<?=$premission['id']?>" class="items-title"><?=$premission['name']?></label>
        <input id="premission<?=$premission['id']?>" type="checkbox" <?php echo (in_array($premission['id'], array_column($user_premission_select, 'premission_id'))) ? 'checked' : ''; ?> value="<?=$premission['id']?>" name="premission[]">
   </div>
<?php } ?>

