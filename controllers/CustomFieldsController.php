<?php

namespace app\controllers;

use app\models\Warehouse;
use Yii;
use app\models\Users;
use app\models\CustomfieldsBlocksTitle;
use app\models\CustomfieldsBlocksInputs;
use app\models\CustomfieldsBlocksInputValues;
use app\models\CustomfieldsBlocksSelectOptions;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * LogController implements the CRUD actions for Log model.
 */
class CustomFieldsController extends Controller
{
    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        Yii::$app->language = 'hy';
    }
    public function beforeAction($action)
    {
        $session = Yii::$app->session;
        if ($action->id !== 'login' && !(isset($session['user_id']) && $session['logged'])) {
            return $this->redirect(['site/login']);
        } else if ($action->id == 'login' && !(isset($session['user_id']) && $session['logged'])) {
            return $this->actionLogin();
        }
        if(!$session['username']){
            $this->redirect('/site/logout');
        }
        return parent::beforeAction($action);
    }

    /**
     * Lists all Log models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new CustomfieldsBlocksTitle();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all Log models.
     *
     * @return string
     */
    public function actionUpdateTitle()
    {
       // $model = new CustomfieldsBlocksTitle();
        if ($this->request->isPost) {
            $post = $this->request->post();
            if($post['val_'] && $post['id_']){
                $model = CustomfieldsBlocksTitle::findOne($post['id_']);
                $model->title = $post['val_'];
                return $model->save(false);
            }
            return false;
        }
    }
    public function actionCreateTitle()
    {
        // $model = new CustomfieldsBlocksTitle();
        if ($this->request->isPost) {
            $post = $this->request->post();

            if(count($post['newblocks']) >= 1){
                foreach ($post['newblocks'] as $newBlock => $block_val){
                    $model = CustomfieldsBlocksTitle::findOne(['id'=>$newBlock,'page'=>$post['page']]);

                    if(!$model){
                        $model = new CustomfieldsBlocksTitle();
                        $model->block_type = 1;
                    }
                    $model->title = $block_val;
                    $model->page = $post['page'];
                    $model->order_number = 1;
                    $model->save(false);
//                     var_dump($post['new_fild_name']);
//                     exit;
                    if(!empty($post['new_fild_name'][$newBlock])){
                       foreach ($post['new_fild_name'][$newBlock] as $input_item => $input_val){

                           if(!empty($input_val)){
                               for ($i = 0; $i < count($input_val); $i++){

                                   $new_input = new CustomfieldsBlocksInputs();
                                   $new_input->iblock_id = $model->id;
                                   if(!is_array($input_val[$i])) {
                                       $new_input->label = $input_val[$i];
                                   } else {
                                       $new_input->label = $input_val[$i][0];
                                   }
                                   $new_input->type = $input_item;
                                   $new_input->save(false);

                                   if(isset($post['new_fild_value'][$newBlock][$input_item][$i])){

                                       if(!is_array($post['new_fild_value'][$newBlock][$input_item][$i])) {

                                           if (isset($_FILES['new_fild_value']['name'][$newBlock][$input_item][$i]) && $_FILES['new_fild_value']['name'][$newBlock][$input_item][$i]) {
                                               $uploaddir = 'uploads/cf/';
                                               $uploadfile = $uploaddir . time() . basename($_FILES['new_fild_value']['name'][$newBlock][$input_item][$i]);

                                               if (move_uploaded_file($_FILES['new_fild_value']['tmp_name'][$newBlock][$input_item][$i], $uploadfile)) {
                                                   $field_val__ = $uploadfile;
                                               }
                                           } else {
                                               $field_val__ = $post['new_fild_value'][$newBlock][$input_item][$i];
                                           }
                                           if(isset($_POST['item_id'])) {
                                               $new_input_value = new CustomfieldsBlocksInputValues();
                                               $new_input_value->input_id = $new_input->id;
                                               $new_input_value->value_ = $field_val__;
                                               $new_input_value->item_id = intval($_POST['item_id']);
                                               $new_input_value->save(false);
                                           }
                                       } else {
                                           if(!empty($post['new_fild_value'][$newBlock][$input_item][$i])){
                                               for ($j = 0; $j < count($post['new_fild_value'][$newBlock][$input_item][$i]); $j++){
                                                   $new_input_value = new CustomfieldsBlocksSelectOptions();
                                                   $new_input_value->select_id = $new_input->id;
                                                   $new_input_value->value_ = $post['new_fild_value'][$newBlock][$input_item][$i][$j];
                                                   $new_input_value->save(false);
                                               }
                                           }
                                       }
                                   }
                               }
                           }
                       }
                    }
                }

                if(!empty($post['CF'])){
                    foreach ($post['CF'] as $field_ => $field_val){
                        if(isset($_FILES['CF']['name'][$field_]) && $_FILES['CF']['name'][$field_]){
                            $uploaddir = 'uploads/cf/';
                            $uploadfile = $uploaddir .time().basename($_FILES['CF']['name'][$field_]);
                            if (move_uploaded_file($_FILES['CF']['tmp_name'][$field_], $uploadfile)) {
                                $field_val = $uploadfile;
                            }
                        }
                        $new_input_value =  CustomfieldsBlocksInputValues::findOne(['input_id'=>$field_,'item_id'=> intval($_POST['item_id'])]);
                        if(!$new_input_value) {
                            $new_input_value = new CustomfieldsBlocksInputValues();
                        }

                        $new_input_value->input_id = $field_;
                        if($field_val) {
                            $new_input_value->value_ = $field_val;
                        }
                        $new_input_value->item_id = intval($_POST['item_id']);
                        $new_input_value->save(false);
                    }
                }

                return true;
            }

            return false;
        }
        return false;
    }

    /**
     * Finds the Log model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Log the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionGetTableData($page)
    {
        $res = Yii::$app->db->createCommand('SELECT customfields_blocks_inputs.label as `attribute`,customfields_blocks_inputs.id FROM customfields_blocks_title 
                                                 LEFT JOIN customfields_blocks_inputs ON customfields_blocks_inputs.iblock_id = customfields_blocks_title.id      
                                                  WHERE page = "'.$page.'" ')->queryAll();
        $fields_arr = [];
        if(!empty($res)){
            return $res;
        }
        return  $fields_arr;

    }
    public function actionDeleteBlock()
    {
        if($this->request->isPost){
            $post = Yii::$app->request->post();
            if ($post['total_'] === "true"){
                $delete_block = CustomfieldsBlocksTitle::findOne(intval($post['blockId']))->delete();
                $delete_field = CustomfieldsBlocksInputs::findOne(['iblock_id'=>intval($post['blockId'])])->deleteAll();
                if ($delete_block && $delete_field){
                    return json_encode(true);
                }
            }else{
                $delete_block = CustomfieldsBlocksTitle::findOne(['id' => intval($post['blockId'])])->delete();
                if ($delete_block){
                    return json_encode(true);
                }
            }

        }
    }
    public function actionDeleteField(){
        if($this->request->isPost){
            $post = Yii::$app->request->post();
            $delete_block_field = CustomfieldsBlocksInputs::findOne(['id'=>intval($post['removeField'])])->delete();
            if ($delete_block_field){
                return json_encode(true);
            }
        }
    }
    protected function findModel($id)
    {
        if (($model = CustomfieldsBlocksTitle::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
