<?php

use app\models\CustomfieldsBlocksInputValues;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\CustomfieldsBlocksTitle;
use app\models\CustomfieldsBlocksInputs;
use app\models\Users;
use app\models\DocumentItems;

/** @var yii\web\View $this */
/** @var app\models\Documents $model */
/** @var yii\widgets\ActiveForm $form */

$blocks = CustomfieldsBlocksTitle::find()->where(['page'=>'documents','block_type'=>1])->orderBy(['order_number'=>SORT_ASC])->all();
$req = true;
if(isset($action__)){
    $req = false;
}
$type = $model->document_type;
$session = Yii::$app->session;
?>
<?php if ($model->id){
    if ($model->document_type === '1'){
        $value = 'Մուտք';
    }elseif ($model->document_type === '2'){
        $value = 'Ելք';
    }elseif ($model->document_type === '3'){
        $value = 'Տեղափոխություն';
    }elseif ($model->document_type === '4'){
        $value = 'Խոտան';
    }elseif ($model->document_type === '6'){
        $value = 'Վերադարձրած';
    }elseif ($model->document_type === '7'){
        $value = 'Մերժված';
    }elseif ($model->document_type === '8'){
        $value = 'Մուտք(վերադարցրած)';
    }elseif ($model->document_type === '9'){
        $value = 'Պատվերից ելքագրված';
    }elseif ($model->document_type === '10'){
        $value = 'Ետ վերադարցրած';
    }
    ?>
    <?php if (Users::checkPremission(56)){ ?>
        <img style="height: 20px; float: right;"  class="downloadUpdateXLSX" onclick="window.open('/documents/reports?type=1&id=<?= $model->id ?>//','newwindow','width:500, height:700')" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAAACXBIWXMAAAsTAAALEwEAmpwYAAAC+klEQVR4nO2ZzWsTURDAFwW/kYre9CKKiB4ERT2of4AUveXqF1htZtaQ7rw0NDN9ehMUsTfxIggepPSioOJRz3rQg3gRqYI91I/Wg1rUykvzkk2atNl2d/uCHRjIMpnZ+c2b93Y28bxlWZZlmVf8gt4GJMNAMolKpheoU6DkJarBjJd68oo/LyLx2UqsUwMAkuFYk68oKLmcFsBkXAkvCQTGVPFyMRT3z24nudoxAEsCgTEDpA6BCQCkCoEJAaQGgQkCpAKRNICRZkdsbA+7NABaQsQxdsQFYEaSyBDEL5wBAMWPokIA8S9nAJJqvXnlvwEAxbetj9Z6BSh5V2+Xi9YOhcGzzgGgkimfSjuane9AMpbP59dW4FY1wqEjAKbKd6xfT5/egsQ/yjaSYggs52QL4Yz+xj69u+pLfBcUT+RyustcZ7XeYFbDZYBpVHLP+mb7S0eR5GYISJzdxFjtd/7jB3qP3czZAu81n6FY3Iwk35wHwBm93xgHSK4vJJa3JADE333fX10HoPhNJwFcmRWH+NScrafkJygeygZyOAiC9YtOfOEA/MmcNmXfoLQd+3mf3Q9I8qqF30f7vdglevUHz4cHMyC5Ya/9QE40qzwmlXxUAFD8OpPJrDR+ZhWQZNz8rtRbLG6qxiN53uAzlFjyUQEwkOM1P86HbAN1z4awT2HgUN39Auk2LQXEH8Lxkgcgfmp9zLyDSkarVSYZO631GmsHxQ+sLVvZL1WbSbwWdzQ1AF/xuUuBHDDa7EUdiEvWjsRnWh2T6MQxGkG9DgZ4DIpPdiLAX6AStHs/zz0Avhblfp5TAOUZSW9MFQAUT8QHIA/DsXM53QXEz0xbtTOmI/GTHq3XRQMgGYkLAJTcqqs21Y7TtjWQ7mgAfXqXGQliASAZCcfOFvVO01YRVnDcvGt7UeVCfmCreUlZbDuZfzvNU7puFZQcawuC5Asovd9zUZDkyFx/JoLir72BHPRcFmwB0RHJexVphOio5K1Uxu33qPhtOz3/DyrGtgq43BHiAAAAAElFTkSuQmCC">
    <?php } ?>
    <div class="documents-form">
        <div class="card card-primary">
            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
            <div class="dinamic-form">
                <input type="hidden" name="page" value="documents">
                <div class="default-panel" data-id="41" data-page="documents">
                    <div class="panel-title">
                        <span class="non-active"><?=$model->DefaultTitle->title?></span>
                        <input type="text" name="newblocks[<?php echo $model->DefaultTitle->id;?>]" value="<?=$model->DefaultTitle->title?>"  class="only-active form-control">
                        <button type="button" class="btn btn-default btn-sm edite-block-title" ><i class='bx bx-edit-alt'></i></button>
                        <button type="button" class="btn btn-default btn-sm edite-block-title-save" ><i class='bx bx-save'></i></button>
                    </div>
                    <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName">
                        <?php if ($session['role_id'] == 4){?>
                            <?= $form->field($model, 'warehouse_id')->hiddenInput(['value' => $warehouse])->label(false)?>
                        <?php }else{?>
                            <?= $form->field($model, 'warehouse_id')->dropDownList($warehouse) ?>
                        <?php }?>
                    </div>
                    <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName">
                        <?= $form->field($model, 'document_type')->textInput([ 'value' => $value, 'readonly' => true ]) ?>
                    </div>
                    <?php if ($model->document_type == 10) { ?>
                        <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName ">
                            <label for="singleClients">Հաճախորդներ</label>
                            <select id="singleClients" class="js-example-basic-single form-control" name="client_id">
                                <option  value="<?=$delivered_documents['clients_id']?>"><?=$delivered_documents['name']?></option>
                            </select>
                        </div>
                        <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName ">
                            <label for="deliveredorders">Հաստատված փաստաթղթեր</label>
                            <select id="deliveredorders" class="js-example-basic-single form-control" name="order_id">
                                <option value="<?=$delivered_documents['id']?>">Հաստատված պատվեր <?=$delivered_documents['orders_date']?></option>
                            </select>
                        </div>
                        <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName">
                            <label for="deliverorders">Առաքիչ</label>
                            <select id="deliverorders" class="js-example-basic-single form-control" name="Documents[deliver_id]">
                                <option value="<?=$delivered_documents['deliver_id']?>"> <?=$delivered_documents['deliver_name']?></option>
                            </select>
                        </div>

                    <?php } ?>

                    <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName toWarehouse">
                        <?= $form->field($model, 'to_warehouse')->dropDownList($to_warehouse) ?>
                    </div>
                    <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName">
                        <?php
                        if($session['role_id'] == 1 || $session['role_id'] == 2 || $session['role_id'] == 3){?>
                           <?= $form->field($model, 'user_id')->dropDownList($users) ?>
                       <?php }elseif ($session['role_id'] == 4){?>
                            <?= $form->field($model, 'user_id')->hiddenInput(['value' => $session['user_id']])->label(false) ?>
                        <?php } ?>
                    </div>

                    <label class="rateLabel" for="rate">Փոխարժեք</label>
                    <div id="rate" class="form-group col-md-12 col-lg-12 col-sm-12 rateDocument">
                        <div class="rateType">
                            <?= $form->field($model, 'rate_id')->dropDownList($rates)->label(false) ?>
                        </div>
                        <div class="rateValue">
                            <?= $form->field($model, 'rate_value')->input('number')->label(false) ?>
                        </div>
                    </div>

                    <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName">
                        <label for="aah">ԱԱՀ</label>
                        <select class="form-control" name="aah" id="aah">
                            <option value="true" <?php if(isset($aah['AAH']) && $aah['AAH'] === 'true'){ echo  'selected';} ?>>20%</option>
                            <option value="false" <?php if(isset($aah['AAH']) && $aah['AAH'] === 'false'){ echo  'selected';} ?>>0%</option>
                        </select>
                    </div>
                    <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName">
                        <?php
                        if ($type == '7'){?>
                            <?= $form->field($model, 'comment')->textArea(['maxlength' => true, 'disabled' => true]) ?>
                        <?php }elseif($type == '6'){?>
                            <?= $form->field($model, 'comment')->textArea(['maxlength' => true, 'disabled' => true]) ?>
                        <?php }else{?>
                            <?= $form->field($model, 'comment')->textArea(['maxlength' => true]) ?>
                        <?php }?>
                    </div>
                    <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName">
                        <?= $form->field($model, 'date')->input('datetime-local') ?>
                    </div>
                    <?php if ($type == '1' || $type == '2' || $type == '3' || $type == '4'){?>
                        <?php $fields = CustomfieldsBlocksInputs::find()->where(['iblock_id'=>41])->all(); ?>
                        <?php if(!empty($fields)){ ?>
                            <?php foreach ($fields as $fild => $fild_simple){ ?>
                                <?php echo CustomfieldsBlocksInputs::createElement($fild_simple,$model->id);?>
                            <?php } ?>
                        <?php } ?>
                    <?php }?>
                </div>
                <?php if ($type == '1' || $type == '2' || $type == '3' || $type == '4'){ $fieldValues = ''; ?>
                    <?php if(!empty($blocks)){ ?>
                        <?php foreach ($blocks as $block => $block_val){ ?>
                            <?php $fields = CustomfieldsBlocksInputs::find()->where(['iblock_id'=>$block_val->id])->all();
                                foreach ($fields as $field) {
                                    $value = CustomfieldsBlocksInputValues::find()
                                        ->where(['input_id' => $field->id])
                                        ->andWhere(['item_id' => $model->id])
                                        ->one();
                                    if ($value) {
                                        $fieldValues = true;
                                        break;
                                    } else {
                                        $fieldValues = false;
                                    }
                                }
                                if ($fieldValues){ ?>
                                    <div class="default-panel"  data-id="<?php echo $block_val->id;?>" data-page="documents">
                                        <div class="panel-title">
                                            <span class="non-active"><?=$block_val->title?></span>
                                            <input type="text" name="newblocks[<?php echo $block_val->id;?>]" value="<?=$block_val->title?>"  class="only-active form-control">
                                        </div>
                                        <?php if(!empty($fields)){ ?>
                                            <?php foreach ($fields as $fild => $fild_simple){ ?>
                                                <?php echo CustomfieldsBlocksInputs::createElement($fild_simple,$model->id);?>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                        <?php } ?>
                    <?php } ?>
                <?php }?>
            </div>
            <div class="default-panel">
                <div class="panel-title premission">
                    <span class="non-active">Ապրանքացուցակ</span>
                </div>
                <div class="card">
                    <div class="table-responsive text-nowrap">
                        <table class="table documentsAddingTable">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Անուն</th>
                                <?php
                                $items = DocumentItems::find()->where(['and',['document_id' => $model->id],['not', ['wastrel' => null]]])->exists();
                                if ($items){?>
                                    <th>Խոտան</th>
                                <?php }?>
                                <th>Քանակ</th>
                                <th>Գինը առանց ԱԱՀ-ի</th>
                                <th>Գինը ներառյալ ԱԱՀ-ն</th>
                                <?php
                                if ($type == '1' || $type == '2' || $type == '3' || $type == '4' || $type == '6'){?>
                                    <th>Գործողություն</th>
                                <?php }?>
                            </tr>
                            </thead>
                            <tbody class="old_tbody">
                            <?php
                            $itemsArray = [];
                            foreach ($document_items as $keys => $document_item){
                                $itemsArray[] = $document_item['nom_id'];
                                ?>
                                <tr class="oldTr" id="tr_<?=$document_item['nom_id']?>">
                                    <td>
                                        <span><?=$keys + 1?></span>
                                        <input class="docItemsId" type="hidden" name="document_items[]" value="<?=$document_item['id']?>">
                                        <input class="itemsId" type="hidden" name="items[]" value="<?=$document_item['nom_id']?>">
                                    </td>
                                    <td class="name"><?=$document_item['name']?></td>
                                    <?php if ($items && ($type == '8' || $type == '6' || $type == '7' || $type == '10')){?>
                                        <td>
                                            <input type="number" class="form-control input_wastrel" readonly value="<?= ($document_item['wastrel'] != null) ? $document_item['wastrel'] : 0 ?>">                                        </td>
                                    <?php }?>
                                    <?php
                                    if ($model->document_type == '7'){?>
                                        <td class="count"><input type="number" name="count_[]" disabled value="<?=$document_item['count']?>" class="form-control countDocuments" min="1" step="any"></td>
                                        <td class="price"><input type="text" name="price[]" disabled value="<?=$document_item['price']?>" class="form-control PriceDocuments"></td>
                                    <?php }else if ($model->document_type == '1'){?>
                                        <td class="count"><input type="number" name="count_[]" value="<?=$document_item['count']?>" class="form-control countDocuments" min="1" step="1"></td>
                                        <td class="price"><input type="text" name="price[]"  value="<?=$document_item['price']?>" class="form-control PriceDocuments"></td>
                                    <?php }else{?>
                                        <td class="count"><input type="number" name="count_[]" readonly value="<?=$document_item['count']?>" class="form-control countDocuments" min="1" step="1"></td>
                                        <td class="price"><input type="text" name="price[]" readonly value="<?=$document_item['price']?>" class="form-control PriceDocuments"></td>
                                    <?php }?>

                                    <td class="pricewithaah">
                                        <span><?=number_format($document_item['price_with_aah'],2,'.', '')?></span>
                                        <input type="hidden" name="pricewithaah[]" value="<?=number_format($document_item['price_with_aah'],2,'.', '')?>" class="form-control PriceWithaah">
                                    </td>
                                    <?php
                                    if ($type == '1' || $type == '2' || $type == '3' || $type == '4'){?>
                                        <td><button  type="button" class="btn rounded-pill btn-outline-danger deleteDocumentItems">Ջնջել</button></td>
                                    <?php }?>
                                    <?php
                                    if ($type == '6'){?>
                                        <td><button type="button" data-document="<?=$document_item['id']?>" class="btn rounded-pill btn-outline-info changeWastrel" data-bs-toggle="modal" data-bs-target="#modalCenter">
                                                Խոտան
                                            </button>
                                            <div class="modal fade" id="modalCenter" tabindex="-1" style="display: none;" aria-modal="true" role="dialog">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
<!--                                                            <h5 class="modal-title" id="modalCenterTitle">Modal title</h5>-->
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body changeModalBody">

                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                                                Փակել
                                                            </button>
                                                            <button type="button" class="btn btn-primary addChange" data-bs-dismiss="modal">Պահպանել</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    <?php }?>
                                </tr>
                                <?php
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Button trigger modal -->
                <?php
                if ($type == '1' || $type == '2' || $type == '3' || $type == '4'){?>
                    <button type="button" class="btn rounded-pill btn-secondary addDocuments addDocuments_get_type_val_update" data-bs-toggle="modal" data-bs-target="#documentsModal">Ավելացնել ապրանք</button>
                <?php }?>
                <!-- Modal -->
                <div class="modal fade" id="documentsModal" tabindex="-1" style="display: none;" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel3">Ապրանքացուցակ</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <input class="form-control col-md-3 mb-3 searchForDocumentUpdate" type="search" placeholder="Որոնել...">
                                <div id="ajax_content">

                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn rounded-pill btn-secondary updateDocuments" data-bs-dismiss="modal">Ավելացնել ցուցակում</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <?php
                if ($type == '1' || $type == '2' || $type == '3' || $type == '4'){?>
                    <?= Html::submitButton('Պահպանել', ['class' => 'btn rounded-pill  btn-secondary submit_save']) ?>
                <?php }?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
<?php
}else{ ?>
    <div class="documents-form">
        <div class="card card-primary">
            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
            <div class="dinamic-form">
                <input type="hidden" name="page" value="documents">
                <div class="default-panel" data-id="41" data-page="documents">
                    <div class="panel-title">
                        <span class="non-active"><?=$model->DefaultTitle->title?></span>
                        <input type="text" name="newblocks[<?php echo $model->DefaultTitle->id;?>]" value="<?=$model->DefaultTitle->title?>"  class="only-active form-control">
                        <button type="button" class="btn btn-default btn-sm edite-block-title" ><i class='bx bx-edit-alt'></i></button>
                        <button type="button" class="btn btn-default btn-sm edite-block-title-save" ><i class='bx bx-save'></i></button>
                    </div>
                    <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName">
                        <?php if ($session['role_id'] == 4){
                            $storekeeper = Users::findOne($session['user_id']);
                            ?>
                            <?= $form->field($model, 'warehouse_id')->hiddenInput(['value' => $storekeeper->warehouse_id])->label(false)?>
                        <?php }else{?>
                            <?= $form->field($model, 'warehouse_id')->dropDownList(['null' => 'Ընտրել պահեստը'] + $warehouse) ?>
                        <?php }?>
                    </div>
                    <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName">
                        <?= $form->field($model, 'document_type')->dropDownList([ '1' => 'Մուտք', '2' => 'Ելք','3' => 'Տեղափոխություն','4' => 'Խոտան','10' => 'Հետ վերադարձ', ]) ?>
                    </div>
                    <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName toWarehouse">
                        <?= $form->field($model, 'to_warehouse')->dropDownList(['' => 'Ընտրել պահեստը'] + $to_warehouse) ?>
                    </div>
                    <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName docType">
                        <label for="singleClients">Հաճախորդներ</label>
                        <select id="singleClients" class="js-example-basic-single form-control" name="client_id">
                            <option  value=""></option>
                            <?php foreach ($clietns as $clietn){ ?>
                                <option value="<?=$clietn['id']?>"><?=$clietn['name']?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName deliveredOrders">

                    </div>
                    <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName deliverOrdersId">

                    </div>
                        <?php
                        if($session['role_id'] == 1 || $session['role_id'] == 2 || $session['role_id'] == 3){?>
                            <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName changeKeeper">

                            </div>
                        <?php }elseif ($session['role_id'] == 4){?>
                            <input name="user_id" type="hidden" value="<?=$session['user_id']?>">
                        <?php } ?>

                    <label class="rateLabel" for="rate">Փոխարժեք</label>
                    <div id="rate" class="form-group col-md-12 col-lg-12 col-sm-12 rateDocument">
                        <div class="rateType">
                            <?= $form->field($model, 'rate_id')->dropDownList($rates)->label(false) ?>
                        </div>
                        <div class="rateValue">
                            <?= $form->field($model, 'rate_value')->input('number', ['required' => true,'value' => 1, 'readonly' => true])->label(false) ?>
                        </div>

                    </div>
                    <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName">
                        <label for="aah">ԱԱՀ</label>
                        <select class="form-control" name="aah" id="aah">
                            <option value="true">20%</option>
                            <option value="false">0%</option>
                        </select>
                    </div>

                    <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName">
                        <?= $form->field($model, 'comment')->textArea(['maxlength' => true]) ?>
                    </div>
                    <div class="form-group col-md-12 col-lg-12 col-sm-12 ordersName">
                        <?= $form->field($model, 'date')->input('datetime-local') ?>
                    </div>
                    <?php $fields = CustomfieldsBlocksInputs::find()->where(['iblock_id'=>41])->andWhere(['status'=>'1'])->all(); ?>
                    <?php if(!empty($fields)){ ?>
                        <?php foreach ($fields as $fild => $fild_simple){ ?>
                            <?php echo CustomfieldsBlocksInputs::createElement($fild_simple,$model->id);?>
                        <?php } ?>
                    <?php } ?>
                </div>
                <?php if(!empty($blocks)){ $fieldValues = ''; ?>
                    <?php foreach ($blocks as $block => $block_val){ ?>
                        <?php $fields = CustomfieldsBlocksInputs::find()->where(['iblock_id'=>$block_val->id])->andWhere(['status'=>'1'])->all();
                        foreach ($fields as $field) {
                            if ($field->id) {
                                $fieldValues = true;
                                break;
                            } else {
                                $fieldValues = false;
                            }
                        }
                        if ($fieldValues){ ?>
                            <div class="default-panel"  data-id="<?php echo $block_val->id;?>" data-page="documents">
                                <div class="panel-title">
                                    <span class="non-active"><?=$block_val->title?></span>
                                    <input type="text" name="newblocks[<?php echo $block_val->id;?>]" value="<?=$block_val->title?>"  class="only-active form-control">
                                    <button type="button" class="btn btn-default btn-sm edite-block-title" ><i class='bx bx-edit-alt'></i></button>
                                    <button type="button" class="btn btn-default btn-sm edite-block-title-save" ><i class='bx bx-save'></i></button>
                                    <button type="button" class="btn btn-default btn-sm edite-block-trash"><i class="bx bx-trash"></i></button>
                                </div>
                                <?php if(!empty($fields)){ ?>
                                    <?php foreach ($fields as $fild => $fild_simple){ ?>
                                        <?php echo CustomfieldsBlocksInputs::createElement($fild_simple,$model->id);?>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    <?php } ?>
                <?php } ?>
            </div>
            <div class="default-panel">
                <div class="panel-title premission">
                    <span class="non-active">Ապրանքացուցակ</span>
                </div>
                <div class="card">
                    <div class="table-responsive text-nowrap">
                        <table class="table documentsAddingTable">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Անուն</th>
                                <th>Քանակ</th>
                                <th class=" d-none forDocTypeTen">Խոտան</th>
                                <th>Գինը առանց ԱԱՀ-ի</th>
                                <th>Գինը ներառյալ ԱԱՀ-ն</th>
                                <th>Գործողություն</th>
                            </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Button trigger modal -->
                <!--disabled-->
                <button type="button" class="btn rounded-pill btn-secondary addDocuments addDocuments_get_type_val" data-bs-toggle="modal" data-bs-target="#documentsModal" disabled>Ավելացնել ապրանք</button>
                <!-- Modal -->
                <div class="modal fade" id="documentsModal" tabindex="-1" style="display: none;" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel3">Ապրանքացուցակ</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <input class="form-control col-md-3 mb-3 searchForDocument" type="search" placeholder="Որոնել...">
                                <div id="ajax_content">

                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn rounded-pill btn-secondary createDocuments" data-bs-dismiss="modal">Ավելացնել ցուցակում</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <?= Html::submitButton('Պահպանել', ['class' => 'btn rounded-pill  btn-secondary saveAll submit_save']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
<?php
}
?>


<?php
$this->registerJsFile(
    '@web/js/documents.js',
    ['depends' => [\yii\web\JqueryAsset::class]]
);
?>

<style>
    /* Vendor prefixes for cross-browser support */
    input[type="search"]::-webkit-search-cancel-button,
    input[type="search"]::-webkit-search-clear-button {
        -webkit-appearance: none;
        appearance: none;
        display: none;
    }
</style>