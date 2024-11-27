    <?php foreach ($premissions_check as $premission){ ?>
        <div class="premission-content-items">
            <label for="premission<?=$premission['id']?>" class="items-title"><?=$premission['name']?></label>
            <input id="premission<?=$premission['id']?>" type="checkbox" value="<?=$premission['id']?>" name="premission[]">
        </div>
       <?php } ?>