<?php
use yii\widgets\ActiveForm;

?>
<label for="keeper">Պահեստապետ</label>
<select required name="user_id" id="keeper" class="form-control">
    <option value="<?=$users['id']?>"><?=$users['name']?></option>
</select>
