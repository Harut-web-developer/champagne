<?php

namespace app\controllers;

use app\models\Clients;
use app\models\ClientsGroups;
use app\models\Discount;
use app\models\DiscountClients;
use app\models\DiscountProducts;
use app\models\DiscountSearch;
use app\models\GroupsName;
use app\models\Log;
use app\models\Nomenclature;
use app\models\Premissions;
use app\models\Products;
use app\models\Users;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use function React\Promise\all;

/**
 * DiscountController implements the CRUD actions for Discount model.
 */
class DiscountController extends Controller
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
//    public function behaviors()
//    {
//        return array_merge(
//            parent::behaviors(),
//            [
//                'verbs' => [
//                    'class' => VerbFilter::className(),
//                    'actions' => [
//                        'delete' => ['POST'],
//                    ],
//                ],
//            ]
//        );
//    }

    /**
     * Lists all Discount models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $have_access = Users::checkPremission(44);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $sub_page = [
            ['name' => 'Ոչ ակտիվ զեղչեր','address' => '/discount/inactive'],
        ];
        $date_tab = [];

//        $searchModel = new DiscountSearch();
//        $dataProvider = $searchModel->search($this->request->queryParams);
        $discount_overdue = Discount::find()->where(['status' => 1])->all();

        foreach ($discount_overdue as $key => $value){
            if (!empty($value['end_date']) && $value['end_date'] < date('Y-m-d')){
                $value->status = '2';
                $value->save(false);
            }
        }
        $discount_sortable = Discount::find()->where(['status' => 1])->orderBy(['discount_sortable'=> SORT_ASC])->asArray()->all();

        return $this->render('index', [
//            'searchModel' => $searchModel,
//            'dataProvider' => $dataProvider,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,

            'discount_sortable' => $discount_sortable
        ]);
    }
    public function actionSave()
    {
        if ($this->request->isPost) {
            if (!empty($_POST['sort'])) {
                foreach ($_POST['sort'] as $i => $row) {
                    $discount = Discount::find()->where(['and',['status'=> 1],['id'=>intval($row)]])->one();
                    $discount->discount_sortable = $i;
                    $discount->save(false);
                }
            }
//            return 'success';
        }
//        else {
//            return 'error';
//        }
    }
    public function actionInactive(){
        $sub_page = [
            ['name' => 'Ակտիվ զեղչեր','address' => '/discount'],
        ];
        $date_tab = [];

        $discount_sortable = Discount::find()->where(['or',['status' => '0'],['status' => '2']])->asArray()->all();
        return $this->render('inactive', [
            'date_tab' => $date_tab,
            'sub_page' => $sub_page,
            'discount_sortable' => $discount_sortable
        ]);
    }
    /**
     * Displays a single Discount model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $sub_page = [];
        $date_tab = [];
        if ($this->findModel($id)->status == 0) {
            return $this->redirect(Yii::$app->request->referrer);
        }
        return $this->render('view', [
            'model' => $this->findModel($id),
            'date_tab' => $date_tab,
            'sub_page' => $sub_page,
        ]);
    }

    /**
     * Creates a new Discount model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $have_access = Users::checkPremission(41);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $sub_page = [];
        $date_tab = [];

        $model = new Discount();
        $url = Url::to('', 'http');
        $url = str_replace('create', 'view', $url);
        $premission = Premissions::find()
            ->select('name')
            ->where(['id' => 41])
            ->asArray()
            ->one();
        if ($this->request->isPost) {
            date_default_timezone_set('Asia/Yerevan');
            $post = $this->request->post();
            $model->name = $post["Discount"]['name'];
            $model->discount_option = $post["Discount"]['discount_option'];
            $model->type = $post["Discount"]['type'];
            $model->discount = $post["Discount"]['discount'];
            if (empty($post["Discount"]['start_date']) && empty($post["Discount"]['end_date'])){
                $model->start_date = null;
            }elseif (empty($post["Discount"]['start_date'])){
                $model->start_date = date('Y-m-d');
            }else{
                $model->start_date = $post["Discount"]['start_date'];
            }
            if (empty($post["Discount"]['end_date'])){
                $model->end_date = null;
            }else {
                $model->end_date = $post["Discount"]['end_date'];
            }
            $model->discount_check = $post["Discount"]['discount_check'];
            if (!empty($post['Discount']['discount_filter_type'])){
                $model->discount_filter_type = $post['Discount']['discount_filter_type'];
                $model->min = $post['min'];
                $model->max = $post['max'];
            }
            $model->comment = $post["Discount"]['comment'];
            $model->created_at = date('Y-m-d H:i:s');
            $model->updated_at = date('Y-m-d H:i:s');
            $model = Discount::getDefVals($model);
            $model->save();
            if(!empty($post['clients'])){
                $client_groups_id = [];
                $index = 0;
                foreach ($post['clients'] as $value) {
                    if (preg_match('/groups\[\'id\'\] = (\d+)/', $value, $matches)) {
                        $number = $matches[1];
                        $client_groups_id[$index] = intval($number);
                        $index++;
                    }
                }
                $all_client_groups_id = [];
                for ($t=0; $t < count($client_groups_id); $t++)
                {
                    $all_client_groups_id[$t] = ClientsGroups::find()
                        ->select('clients_id')
                        ->where(['groups_id' => $client_groups_id[$t]])
                        ->asArray()
                        ->all();
                }
                $all_client_groups_ids = [];
                for ($k=0; $k < count($all_client_groups_id); $k++){
                    for ($s=0; $s < count($all_client_groups_id[$k]); $s++)
                    array_push($all_client_groups_ids, ($all_client_groups_id[$k][$s]['clients_id'] . ', ' . $client_groups_id[$k]));
                }
                foreach ($post['clients'] as $value) {
                    if (!(preg_match('/groups\[\'id\'\] = (\d+)/', $value, $matches))) {
                        array_push($all_client_groups_ids, $value);
                    }
                }
                $leftValues = array_map(function ($item) {
                    $parts = explode(', ', $item);
                    return isset($parts[0]) ? $parts[0] : null;
                }, $all_client_groups_ids);
                $rigthValues = array_map(function ($item) {
                    $parts = explode(', ', $item);
                    return isset($parts[1]) ? $parts[1] : null;
                }, $all_client_groups_ids);

                for ($i = 0; $i < count($leftValues);$i++){
                    $discount_clients = new DiscountClients();
                    $discount_clients->discount_id = $model->id;
                    $discount_clients->client_id = intval($leftValues[$i]);
                    if ($rigthValues[$i] != 'NULL')
                    {
                        $discount_clients->group_id = intval($rigthValues[$i]);
                    }
                    else
                    {
                        $discount_clients->group_id = 0;
                    }
                    $discount_clients->created_at = date('Y-m-d H:i:s');
                    $discount_clients->updated_at = date('Y-m-d H:i:s');
                    $discount_clients->save(false);
                }
            }

            if(!empty($post['products'])) {
                for ($j = 0; $j < count($post['products']);$j++){
                    $discount_products = new DiscountProducts();
                    $discount_products->discount_id = $model->id;
                    $discount_products->product_id = $post['products'][$j];
                    $discount_products->created_at = date('Y-m-d H:i:s');
                    $discount_products->updated_at = date('Y-m-d H:i:s');
                    $discount_products->save(false);
                }
            }
            Log::afterSaves('Create', $model, '', $url.'?'.'id'.'='.$model->id, $premission);
                return $this->redirect(['index', 'id' => $model->id]);
        } else {
            $model->loadDefaultValues();
        }
            $clients = Clients::find()->select('id,name')->asArray()->all();
            $products = Nomenclature::find()->select('id,name')->asArray()->all();
            $discount_client_groups = GroupsName::find()->select('*')->asArray()->all();

        return $this->render('create', [
            'model' => $model,
            'clients' => $clients,
            'products' => $products,
            'discount_client_groups' => $discount_client_groups,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,


        ]);
    }

    /**
     * Updates an existing Discount model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $have_access = Users::checkPremission(42);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $sub_page = [];
        $date_tab = [];
        if ($this->findModel($id)->status == 0) {
            return $this->redirect(Yii::$app->request->referrer);
        }
        $model = $this->findModel($id);
        $url = Url::to('', 'http');
        $oldattributes = Discount::find()
            ->select('*')
            ->where(['id' => $id])
            ->asArray()
            ->one();
        $premission = Premissions::find()
            ->select('name')
            ->where(['id' => 42])
            ->asArray()
            ->one();
        if ($this->request->isPost) {
            date_default_timezone_set('Asia/Yerevan');
            $post = $this->request->post();
            $model->name = $post["Discount"]['name'];
            $model->discount_option = $post["Discount"]['discount_option'];
            $model->type = $post["Discount"]['type'];
            $model->discount = $post["Discount"]['discount'];
            if (empty($post["Discount"]['start_date']) && empty($post["Discount"]['end_date'])){
                $model->start_date = null;
            }elseif (empty($post["Discount"]['start_date'])){
                $model->start_date = date('Y-m-d');
            }else{
                $model->start_date = $post["Discount"]['start_date'];
            }
            if (empty($post["Discount"]['end_date'])){
                $model->end_date = null;
            }else {
                $model->end_date = $post["Discount"]['end_date'];
            }
            $model->discount_check = $post["Discount"]['discount_check'];
            if (!empty($post['Discount']['discount_filter_type'])){
                $model->discount_filter_type = $post['Discount']['discount_filter_type'];
                $model->min = $post['min'];
                $model->max = $post['max'];
            }
            $model->comment = $post["Discount"]['comment'];
            $model->updated_at = date('Y-m-d H:i:s');
            $model->save();
            if(!empty($post['clients'])){
                $client_groups_id = [];
                $index = 0;
                foreach ($post['clients'] as $value) {
                    if (preg_match('/groups\[\'id\'\] = (\d+)/', $value, $matches)) {
                        $number = $matches[1];
                        $client_groups_id[$index] = intval($number);
                        $index++;
                    }
                }
                $all_client_groups_id = [];
                for ($t=0; $t < count($client_groups_id); $t++)
                {
                    $all_client_groups_id[$t] = ClientsGroups::find()
                        ->select('clients_id')
                        ->where(['groups_id' => $client_groups_id[$t]])
                        ->asArray()
                        ->all();
                }
                $all_client_groups_ids = [];
                for ($k=0; $k < count($all_client_groups_id); $k++){
                    for ($s=0; $s < count($all_client_groups_id[$k]); $s++)
                        array_push($all_client_groups_ids, ($all_client_groups_id[$k][$s]['clients_id'] . ', ' . $client_groups_id[$k]));
                }
                foreach ($post['clients'] as $value) {
                    if (!(preg_match('/groups\[\'id\'\] = (\d+)/', $value, $matches))) {
                        array_push($all_client_groups_ids, $value);
                    }
                }
                $leftValues = array_map(function ($item) {
                    $parts = explode(', ', $item);
                    return isset($parts[0]) ? $parts[0] : null;
                }, $all_client_groups_ids);
                $rigthValues = array_map(function ($item) {
                    $parts = explode(', ', $item);
                    return isset($parts[1]) ? $parts[1] : null;
                }, $all_client_groups_ids);
                $discount_clients_check = DiscountClients::find()->where(['discount_id' => $id])->exists();
                if ($discount_clients_check){
                    DiscountClients::deleteAll(['discount_id' => $id]);
                }
                for ($i = 0; $i < count($leftValues);$i++){
                    $discount_clients = new DiscountClients();
                    $discount_clients->discount_id = $model->id;
                    $discount_clients->client_id = intval($leftValues[$i]);
                    if ($rigthValues[$i] != 'NULL')
                    {
                        $discount_clients->group_id = intval($rigthValues[$i]);
                    }
                    else
                    {
                        $discount_clients->group_id = 0;
                    }
                    $discount_clients->created_at = date('Y-m-d H:i:s');
                    $discount_clients->updated_at = date('Y-m-d H:i:s');
                    $discount_clients->save(false);
                }
            }else{
                $discount_clients_check = DiscountClients::find()->where(['discount_id' => $id])->exists();
                if ($discount_clients_check){
                    DiscountClients::deleteAll(['discount_id' => $id]);
                }
            }
            if(!empty($post['products'])) {
                $discount_products_check = DiscountProducts::find()->where(['discount_id' => $id])->exists();
                if ($discount_products_check) {
                    DiscountProducts::deleteAll(['discount_id' => $id]);
                }
                for ($j = 0; $j < count($post['products']);$j++){
                    $discount_products = new DiscountProducts();
                    $discount_products->discount_id = $model->id;
                    $discount_products->product_id = $post['products'][$j];
                    $discount_products->created_at = date('Y-m-d H:i:s');
                    $discount_products->updated_at = date('Y-m-d H:i:s');
                    $discount_products->save(false);
                }
            }else{
                $discount_products_check = DiscountProducts::find()->where(['discount_id' => $id])->exists();
                if ($discount_products_check) {
                    DiscountProducts::deleteAll(['discount_id' => $id]);
                }
            }
            Log::afterSaves('Update', $model, $oldattributes, $url, $premission);
            return $this->redirect(['index', 'id' => $model->id]);
        }
        $clients = Clients::find()->select('id,name')->asArray()->all();
        $discount_clients_id = DiscountClients::find()->select('client_id')->where(['discount_id' => $id, 'group_id' => 0])->asArray()->all();
        $discount_clients_id = array_column($discount_clients_id,'client_id');
        $products = Nomenclature::find()->select('id,name')->asArray()->all();
        $discount_products_id = DiscountProducts::find()->select('product_id')->where(['discount_id' => $id])->asArray()->all();
        $discount_products_id = array_column($discount_products_id,'product_id');
        $min = Discount::find()->select('min')->where(['id' => $id])->asArray()->one();
        $max = Discount::find()->select('max')->where(['id' => $id])->asArray()->one();
        $discount_client_groups = GroupsName::find()->select('*')->asArray()->all();
        $discount_client_groups_name = GroupsName::find()
            ->select('groups_name')
            ->leftJoin('discount_clients', 'discount_clients.group_id = groups_name.id')
            ->where(['discount_clients.discount_id' => $id])
            ->asArray()
            ->all();
        $uniqueArray = array_unique($discount_client_groups_name, SORT_REGULAR);
        $discount_client_groups_name = array_values($uniqueArray);
        $discount_client_groups_name = array_column($discount_client_groups_name, 'groups_name');

        $discount_client_groups_id = DiscountClients::find()
            ->select('client_id, group_id')
            ->where(['discount_id' => $id])
            ->andWhere(['<>','group_id', 0])
            ->asArray()
            ->all();
        $discount_client_groups_id = array_column($discount_client_groups_id, 'client_id');

        return $this->render('update', [
            'model' => $model,
            'clients' => $clients,
            'products' => $products,
            'discount_clients_id' => $discount_clients_id,
            'discount_client_groups_id' => $discount_client_groups_id,
            'discount_client_groups_name' => $discount_client_groups_name,
            'discount_products_id' => $discount_products_id,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,
            'min' => $min,
            'max' => $max,
            'discount_client_groups' => $discount_client_groups,
        ]);
    }

    /**
     * Deletes an existing Discount model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $have_access = Users::checkPremission(43);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $oldattributes = Discount::find()
            ->select('discount')
            ->where(['id' => $id])
            ->asArray()
            ->one();

        $premission = Premissions::find()
            ->select('name')
            ->where(['id' => 43])
            ->asArray()
            ->one();
        $discount = Discount::findOne($id);
        $discount->status = '0';
        $discount->save();
        Log::afterSaves('Delete', '', $oldattributes['discount'], '#', $premission);
        return $this->redirect(['index']);
    }
    public function actionCheckDate(){
        if ($this->request->isPost){
            $post = $this->request->post();
            if (!empty($post['start']) && empty($post['end']) && $post['start'] < date('Y-m-d')){
                return json_encode('later');
            }elseif (!empty($post['start']) && !empty($post['end']) && $post['start'] > $post['end']){
                return json_encode('more');
            }elseif (!empty($post['start']) && !empty($post['end']) && $post['start'] < date('Y-m-d')){
                return json_encode('later');
            }else{
                return json_encode('exist');
            }
        }
    }

    public function actionCheckFilterValue(){
        if ($this->request->isPost){
            $post = $this->request->post();
            if (!empty($post['min']) && !empty($post['max']) && $post['min'] > $post['max']){
                return json_encode('maxMoreThanMin');
            }
        }
    }

    /**
     * Finds the Discount model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Discount the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Discount::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
