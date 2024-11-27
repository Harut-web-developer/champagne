<div class="modal fade show" id="modalCenter" tabindex="-1" style="display: block;" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="/orders/exit">
                <?=yii\helpers\Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken)?>
                <div class="modal-body">
                    <input type="hidden" name="orders_id" value="<?=$id?>">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameWithTitle" class="form-label"><h5>Նշել առաքիչին</h5></label>
                            <select name="deliver_id" class="form-control" required>
                                <option value="">Ընտրել առաքիչին</option>
                                <?php
                                foreach ($deliver as $item){?>
                                    <option value="<?=$item['id']?>"><?=$item['name']?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn rounded-pill btn-outline-secondary" data-bs-dismiss="modal">
                        Փակել
                    </button>
                    <button type="submit" class="btn rounded-pill btn-secondary">Պահպանել</button>
                </div>
            </form>

        </div>
    </div>
</div>
<script>
    $('body').find('#modalCenter').modal('show');
</script>
