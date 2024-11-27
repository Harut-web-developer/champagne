<?php
?>

<div class="row">
    <div class="col mb-3">
        <span><?=$document_items['name']?></span>
        <input type="hidden" class="wastrelId" value="<?=$document_items['id']?>">
    </div>
</div>
<div class="row">
    <div class="col mb-3">
        <input type="number" id="wastrel" value="" min="0" max="<?=$document_items['count']?>" class="form-control">
    </div>
</div>
