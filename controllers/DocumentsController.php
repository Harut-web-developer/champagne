<?php

namespace app\controllers;

use app\models\Clients;
use app\models\CustomfieldsBlocksInputValues;
use app\models\DocumentItems;
use app\models\Documents;
use app\models\DocumentsSearch;
use app\models\ManagerDeliverCondition;
use app\models\Nomenclature;
use app\models\Log;
use app\models\Notifications;
use app\models\OrderItems;
use app\models\Orders;
use app\models\Premissions;
use PhpParser\Comment\Doc;
use yii\helpers\Url;
use app\models\Products;
use app\models\Rates;
use app\models\Users;
use app\models\Warehouse;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use function PHPUnit\Framework\exactly;

/**
 * DocumentsController implements the CRUD actions for Documents model.
 */
class DocumentsController extends Controller
{
    public function init()
    {
        parent::init();
        Yii::$app->language = 'hy';
    }

    /**
     * @inheritDoc
     */
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
     * Lists all Documents models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $have_access = Users::checkPremission(40);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $sub_page = [];
        if (Users::checkPremission(4)){
            $warehouse = ['name' => 'Պահեստ','address' => '/warehouse'];
            array_push($sub_page,$warehouse);
        }
        if (Users::checkPremission(12)){
            $nom = ['name' => 'Անվանակարգ','address' => '/nomenclature'];
            array_push($sub_page,$nom);
        }
        if (Users::checkPremission(20)){
            $prod = ['name' => 'Ապրանք','address' => '/products'];
            array_push($sub_page,$prod);
        }
        if (Users::checkPremission(28)){
            $log = ['name' => 'Տեղեկամատյան','address' => '/log'];
            array_push($sub_page,$log);
        }
        $res = Yii::$app->runAction('custom-fields/get-table-data',['page'=>'documents']);
        $date_tab = [];

        $searchModel = new DocumentsSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'new_fields'=>$res,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,

        ]);
    }

    /**
     * Displays a single Documents model.
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
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,

        ]);
    }


    /**
     * Creates a new Documents model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $session = Yii::$app->session;
        $have_access = Users::checkPremission(37);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $sub_page = [];
        $date_tab = [];

        $model = new Documents();
        $url = Url::to('', 'http');
        $url = str_replace('create', 'view', $url);
        $premission = Premissions::find()
            ->select('name')
            ->where(['id' => 37])
            ->asArray()
            ->one();
        if ($this->request->isPost) {
            $post = $this->request->post();
//            echo "<pre>";
//            var_dump($post);
//            exit();
            date_default_timezone_set('Asia/Yerevan');
            $model->user_id = $post['user_id'];
            $model->warehouse_id = $post['Documents']['warehouse_id'];
            if ($post['Documents']['document_type'] == '3'){
                $model->to_warehouse = $post['Documents']['to_warehouse'];
            }
            if ($post['Documents']['document_type'] == '10'){
                $model->orders_id = $post['order_id'];
                $model->deliver_id = $post['Documents']['deliver_id'];
            }
            $model->rate_id = $post['Documents']['rate_id'];
            $model->rate_value = $post['Documents']['rate_value'];
            $model->document_type = $post['Documents']['document_type'];
            $model->comment = $post['Documents']['comment'];
            $model->date = $post['Documents']['date'];
            $model->created_at = date('Y-m-d H:i:s');
            $model->updated_at = date('Y-m-d H:i:s');
            $model = Documents::getDefVals($model);
            $model->save(false);
                if ($post['Documents']['document_type'] == '1'){
                    for ($i = 0; $i < count($post['document_items']); $i++) {
                        $products = new Products();
                        $products->warehouse_id = $post['Documents']['warehouse_id'];
                        $products->nomenclature_id = $post['document_items'][$i];
                        $products->document_id = $model->id;
                        $products->type = 1;
                        $products->count = intval($post['count_'][$i]);
                        $products->count_balance = intval($post['count_'][$i]);
                        if ($post['aah'] == 'true'){
                            $products->price = floatval($post['pricewithaah'][$i]);
                            $products->AAH = 1;
                        }else{
                            $products->price = floatval($post['price'][$i]);
                            $products->AAH = 0;

                        }
                        $products->created_at = date('Y-m-d H:i:s');
                        $products->updated_at = date('Y-m-d H:i:s');
                        $products->save(false);

                        $document_items = new DocumentItems();
                        $document_items->document_id = $model->id;
                        $document_items->nomenclature_id = $post['document_items'][$i];
                        $document_items->count = $post['count_'][$i];
                        $document_items->price = floatval($post['price'][$i]);
                        $document_items->refuse_product_id = $products->id;
                        $document_items->price_with_aah = floatval($post['pricewithaah'][$i]);
                        $document_items->AAH = $post['aah'];
                        $document_items->created_at = date('Y-m-d H:i:s');
                        $document_items->updated_at = date('Y-m-d H:i:s');
                        $document_items->save(false);
                    }
                }
            if ($post['Documents']['document_type'] === '10'){
                for ($i = 0; $i < count($post['document_items']); $i++) {
                            $products = new Products();
                            $products->warehouse_id = $post['Documents']['warehouse_id'];
                            $products->nomenclature_id = $post['document_items'][$i];
                            $products->document_id = $model->id;
                            $products->type = 1;
                            if ($post['raw'][$i] == ''){
                                $products->count = intval($post['count_'][$i]);
                                $products->count_balance = intval($post['count_'][$i]);
                            }else{
                                $products->count = intval($post['count_'][$i]) - intval($post['raw'][$i]);
                                $products->count_balance = intval($post['count_'][$i]) - intval($post['raw'][$i]);
                            }
                            if ($post['aah'] == 'true'){
                                $products->price = floatval($post['pricewithaah'][$i]);
                                $products->AAH = 1;
                            }else{
                                $products->price = floatval($post['price'][$i]);
                                $products->AAH = 0;
                            }
                            $products->created_at = date('Y-m-d H:i:s');
                            $products->updated_at = date('Y-m-d H:i:s');
                            $products->save(false);

                            $document_items = new DocumentItems();
                            $document_items->document_id = $model->id;
                            $document_items->nomenclature_id = $post['document_items'][$i];
                            $document_items->count = $products->count;
                            if ($post['raw'][$i] != '') {
                                $document_items->wastrel = $post['raw'][$i];
                            }
                            $document_items->price = floatval($post['price'][$i]);
                            $document_items->refuse_product_id = $products->id;
                            $document_items->price_with_aah = floatval($post['pricewithaah'][$i]);
                            $document_items->AAH = $post['aah'];
                            $document_items->created_at = date('Y-m-d H:i:s');
                            $document_items->updated_at = date('Y-m-d H:i:s');
                            $document_items->save(false);

                            $orders_items = OrderItems::findOne($post['items'][$i]);
                            $str_discount = explode(',',$orders_items->string_discount);
                            $str_count = explode(',',$orders_items->string_count);
                            $str_price = explode(',',$orders_items->string_price);
                            $str_before_price = explode(',',$orders_items->string_before_price);
                            $new_str_discount = $str_discount;
                            $new_str_count = $str_count;
                            $new_str_price = $str_price;
                            $new_str_before_price = $str_before_price;
                            $orders_items->count_by -= $post['count_'][$i];
                            if ($orders_items->count_by == 0){
                                $orders_items->status = '0';
                                $orders_items->price_by = 0;
                                $orders_items->cost_by = 0;
                                $orders_items->discount_by = 0;
                                $orders_items->price_before_discount_by = 0;
                            }else{
                                $discount = 0;
                                $price = 0;
                                $before_price = 0;
                                $bal = $orders_items->count_by;
                                for ($s = count($str_count) - 1; $s <= 0 ; $s--){
                                    if (intval($str_count[$s]) - $bal >= 0){
                                        $bal = intval($str_count[$s]) - $bal;
                                        $discount += $bal * floatval($str_discount[$s]);
                                        $price += $bal *  floatval($str_price[$s]);
                                        $before_price += $bal * floatval($str_before_price[$s]);
                                        break;
                                    }else{
                                        $bal -= intval(str_count[$s]);
                                        $discount += intval($str_count[$s]) * floatval($str_discount[$s]);
                                        $price += intval($str_count[$s]) * floatval($str_price[$s]);
                                        $before_price += intval($str_count[$s]) * floatval($str_before_price[$s]);
                                        array_pop($new_str_discount);
                                        array_pop($new_str_count);
                                        array_pop($new_str_price);
                                        array_pop($new_str_before_price);
                                    }
                                }
                                $orders_items->price_by = number_format($price,2, '.', '');
                                $orders_items->cost_by = number_format(($orders_items->cost / $orders_items->count) * $orders_items->count_by,2, '.', '');
                                $orders_items->discount_by = number_format($discount,2, '.', '');
                                $orders_items->price_before_discount_by = number_format($before_price,2,'.', '');
                            }

                            $orders_items->save(false);

                            $change_products = Products::findOne($orders_items->product_id);
                            $change_products->count = $orders_items->count_by;
                            if ($orders_items->count_by == 0){
                                $change_products->status = '0';
                            }
                            $change_products->save(false);
//
                }

                $end_order_items = OrderItems::find()->select('SUM(count_by) as total_count,
                SUM(price_by) as total_price,SUM(price_before_discount_by) as total_price_before_discount,SUM(discount_by) as total_discount')
                    ->where(['order_id' => $post['order_id']])
                    ->andWhere(['status' => '1'])
                    ->asArray()
                    ->all();
                $orders = Orders::findOne($post['order_id']);
                if ($end_order_items[0]['total_price'] == null){
                    $orders->total_price = 0;
                }else{
                    $orders->total_price = $end_order_items[0]['total_price'];
                }
                if ($end_order_items[0]['total_count'] == null){
                    $orders->total_count = 0;
                }else{
                    $orders->total_count = $end_order_items[0]['total_count'];
                }
                if ($end_order_items[0]['total_price_before_discount'] == null){
                    $orders->total_price_before_discount = 0;
                }else{
                    $orders->total_price_before_discount = $end_order_items[0]['total_price_before_discount'];
                }
                if ($end_order_items[0]['total_discount'] == null){
                    $orders->total_discount = 0;
                }else{
                    $orders->total_discount = $end_order_items[0]['total_discount'];
                }
                if ($orders->status == 2){
                    $orders->status = '4';
                }elseif($orders->status == 3){
                    $orders->status = '5';
                }
                if ($orders->total_count == 0){
                    $orders->status = '0';
                }
                $orders->save(false);
            }
                if ($post['Documents']['document_type'] == '2'){
                    for ($i = 0; $i < count($post['document_items']); $i++) {
                        $first_product = Products::find()
                            ->where(['and',['nomenclature_id' => $post['document_items'][$i]],
                                ['warehouse_id' => $post['Documents']['warehouse_id']]])
                            ->andWhere(['or',['type' => 1],['type' => 3],['type' => 8]])
                            ->andWhere(['!=', 'count_balance', 0])
                            ->andWhere(['status' => '1'])
                            ->all();
                        $bal = $post['count_'][$i];;
                        foreach ($first_product as $item){
                            if ($item->count_balance - $bal >= 0) {
                                $products = new Products();
                                $item->count_balance -= $bal;
                                $item->save(false);
                                $products->count_balance = 0;
                                $products->parent_id = $item->id;
                                $products->count = $bal;
                                $products->warehouse_id = $post['Documents']['warehouse_id'];
                                $products->nomenclature_id = $post['document_items'][$i];
                                $products->document_id = $model->id;
                                $products->type = 5;
                                if ($post['aah'] == 'true'){
                                    $products->price = floatval($post['pricewithaah'][$i]);
                                    $products->AAH = 1;
                                }else{
                                    $products->price = floatval($post['price'][$i]);
                                    $products->AAH = 0;
                                }
                                $products->created_at = date('Y-m-d H:i:s');
                                $products->updated_at = date('Y-m-d H:i:s');
                                $products->save(false);

                                $document_items = new DocumentItems();
                                $document_items->document_id = $model->id;
                                $document_items->nomenclature_id = $post['document_items'][$i];
                                $document_items->count = $products->count;
                                $document_items->price = floatval($post['price'][$i]);
                                $document_items->refuse_product_id = $products->id;
                                $document_items->price_with_aah = floatval($post['pricewithaah'][$i]);
                                $document_items->AAH = $post['aah'];
                                $document_items->created_at = date('Y-m-d H:i:s');
                                $document_items->updated_at = date('Y-m-d H:i:s');
                                $document_items->save(false);
                                break;
                            }else{
                                $products = new Products();
                                $bal -= $item->count_balance;
                                $products->count = $item->count_balance;
                                $item->count_balance = 0;
                                $item->save(false);
                                $products->count_balance = 0;
                                $products->parent_id = $item->id;
                                $products->warehouse_id = $post['Documents']['warehouse_id'];
                                $products->nomenclature_id = $post['document_items'][$i];
                                $products->document_id = $model->id;
                                $products->type = 5;
                                if ($post['aah'] == 'true'){
                                    $products->price = floatval($post['pricewithaah'][$i]);
                                    $products->AAH = 1;
                                }else{
                                    $products->price = floatval($post['price'][$i]);
                                    $products->AAH = 0;
                                }
                                $products->created_at = date('Y-m-d H:i:s');
                                $products->updated_at = date('Y-m-d H:i:s');
                                $products->save(false);

                                $document_items = new DocumentItems();
                                $document_items->document_id = $model->id;
                                $document_items->nomenclature_id = $post['document_items'][$i];
                                $document_items->count = $products->count;
                                $document_items->price = floatval($post['price'][$i]);
                                $document_items->refuse_product_id = $products->id;
                                $document_items->price_with_aah = floatval($post['pricewithaah'][$i]);
                                $document_items->AAH = $post['aah'];
                                $document_items->created_at = date('Y-m-d H:i:s');
                                $document_items->updated_at = date('Y-m-d H:i:s');
                                $document_items->save(false);
                            }

                        }
                    }
                }
                if ($post['Documents']['document_type'] == '3'){
                    for ($i = 0; $i < count($post['document_items']); $i++) {
                        $first_product = Products::find()
                            ->where(['and',['nomenclature_id' => $post['document_items'][$i]],
                                ['warehouse_id' => $post['Documents']['warehouse_id']]])
                            ->andWhere(['or',['type' => 1],['type' => 3],['type' => 8]])
                            ->andWhere(['!=', 'count_balance', 0])
                            ->andWhere(['status' => '1'])
                            ->all();
                        $bal = $post['count_'][$i];;
                        foreach ($first_product as $item) {
                            if ($item->count_balance - $bal >= 0) {
                                $products = new Products();
                                $item->count_balance -= $bal;
                                $item->save(false);
                                $products->count_balance = 0;
                                $products->parent_id = $item->id;
                                $products->count = $bal;
                                $products->warehouse_id = $post['Documents']['warehouse_id'];
                                $products->nomenclature_id = $post['document_items'][$i];
                                $products->document_id = $model->id;
                                $products->type = 5;
                                if ($post['aah'] == 'true'){
                                    $products->price = floatval($post['pricewithaah'][$i]);
                                    $products->AAH = 1;
                                }else{
                                    $products->price = floatval($post['price'][$i]);
                                    $products->AAH = 0;
                                }
                                $products->created_at = date('Y-m-d H:i:s');
                                $products->updated_at = date('Y-m-d H:i:s');
                                $products->save(false);

                                $to_warehouse_products = new Products();
                                $to_warehouse_products->warehouse_id = $post['Documents']['to_warehouse']; // texapoxvac pahest
                                $to_warehouse_products->nomenclature_id = $post['document_items'][$i];
                                $to_warehouse_products->document_id = $model->id;
                                $to_warehouse_products->type = 3;
                                $to_warehouse_products->count = $bal;
                                $to_warehouse_products->count_balance = $bal;
                                $to_warehouse_products->parent_id = $products->id;
                                if ($post['aah'] == 'true'){
                                    $to_warehouse_products->price = floatval($post['pricewithaah'][$i]);
                                    $to_warehouse_products->AAH = 1;
                                }else{
                                    $to_warehouse_products->price = floatval($post['price'][$i]);
                                    $to_warehouse_products->AAH = 0;
                                }
                                $to_warehouse_products->created_at = date('Y-m-d H:i:s');
                                $to_warehouse_products->updated_at = date('Y-m-d H:i:s');
                                $to_warehouse_products->save(false);

                                $document_items = new DocumentItems();
                                $document_items->document_id = $model->id;
                                $document_items->nomenclature_id = $post['document_items'][$i];
                                $document_items->count = $products->count;
                                $document_items->price = floatval($post['price'][$i]);
                                $document_items->refuse_product_id = $products->id;
                                $document_items->price_with_aah = floatval($post['pricewithaah'][$i]);
                                $document_items->AAH = $post['aah'];
                                $document_items->created_at = date('Y-m-d H:i:s');
                                $document_items->updated_at = date('Y-m-d H:i:s');
                                $document_items->save(false);
                                break;
                            }else{
                                $products = new Products();
                                $bal -= $item->count_balance;
                                $products->count = $item->count_balance;
                                $item->count_balance = 0;
                                $item->save(false);
                                $products->count_balance = 0;
                                $products->parent_id = $item->id;
                                $products->warehouse_id = $post['Documents']['warehouse_id'];
                                $products->nomenclature_id = $post['document_items'][$i];
                                $products->document_id = $model->id;
                                $products->type = 5;
                                if ($post['aah'] == 'true'){
                                    $products->price = floatval($post['pricewithaah'][$i]);
                                    $products->AAH = 1;
                                }else{
                                    $products->price = floatval($post['price'][$i]);
                                    $products->AAH = 0;
                                }
                                $products->created_at = date('Y-m-d H:i:s');
                                $products->updated_at = date('Y-m-d H:i:s');
                                $products->save(false);

                                $to_warehouse_products = new Products();
                                $to_warehouse_products->warehouse_id = $post['Documents']['to_warehouse']; // texapoxvac pahest
                                $to_warehouse_products->nomenclature_id = $post['document_items'][$i];
                                $to_warehouse_products->document_id = $model->id;
                                $to_warehouse_products->type = 3;
                                $to_warehouse_products->count = $products->count;
                                $to_warehouse_products->count_balance = $products->count;
                                $to_warehouse_products->parent_id = $products->id;
                                if ($post['aah'] == 'true'){
                                    $to_warehouse_products->price = floatval($post['pricewithaah'][$i]);
                                    $to_warehouse_products->AAH = 1;
                                }else{
                                    $to_warehouse_products->price = floatval($post['price'][$i]);
                                    $to_warehouse_products->AAH = 0;
                                }
                                $to_warehouse_products->created_at = date('Y-m-d H:i:s');
                                $to_warehouse_products->updated_at = date('Y-m-d H:i:s');
                                $to_warehouse_products->save(false);

                                $document_items = new DocumentItems();
                                $document_items->document_id = $model->id;
                                $document_items->nomenclature_id = $post['document_items'][$i];
                                $document_items->count = $products->count;
                                $document_items->price = floatval($post['price'][$i]);
                                $document_items->refuse_product_id = $products->id;
                                $document_items->price_with_aah = floatval($post['pricewithaah'][$i]);
                                $document_items->AAH = $post['aah'];
                                $document_items->created_at = date('Y-m-d H:i:s');
                                $document_items->updated_at = date('Y-m-d H:i:s');
                                $document_items->save(false);
                            }
                        }
                    }
                }
                if($post['Documents']['document_type'] === '4'){
                    for ($i = 0; $i < count($post['document_items']); $i++) {
                        $first_product = Products::find()
                            ->where(['and',['nomenclature_id' => $post['document_items'][$i]],
                                ['warehouse_id' => $post['Documents']['warehouse_id']]])
                            ->andWhere(['or',['type' => 1],['type' => 3],['type' => 8]])
                            ->andWhere(['!=', 'count_balance', 0])
                            ->andWhere(['status' => '1'])
                            ->all();
                        $bal = $post['count_'][$i];;
                        foreach ($first_product as $item) {
                            if ($item->count_balance - $bal >= 0) {
                                $products = new Products();
                                $item->count_balance -= $bal;
                                $item->save(false);
                                $products->count_balance = 0;
                                $products->parent_id = $item->id;
                                $products->count = $bal;
                                $products->warehouse_id = $post['Documents']['warehouse_id'];
                                $products->nomenclature_id = $post['document_items'][$i];
                                $products->document_id = $model->id;
                                $products->type = 4;
                                if ($post['aah'] == 'true'){
                                    $products->price = floatval($post['pricewithaah'][$i]);
                                    $products->AAH = 1;
                                }else{
                                    $products->price = floatval($post['price'][$i]);
                                    $products->AAH = 0;
                                }
                                $products->created_at = date('Y-m-d H:i:s');
                                $products->updated_at = date('Y-m-d H:i:s');
                                $products->save(false);

                                $document_items = new DocumentItems();
                                $document_items->document_id = $model->id;
                                $document_items->nomenclature_id = $post['document_items'][$i];
                                $document_items->count = $products->count;
                                $document_items->price = floatval($post['price'][$i]);
                                $document_items->refuse_product_id = $products->id;
                                $document_items->price_with_aah = floatval($post['pricewithaah'][$i]);
                                $document_items->AAH = $post['aah'];
                                $document_items->created_at = date('Y-m-d H:i:s');
                                $document_items->updated_at = date('Y-m-d H:i:s');
                                $document_items->save(false);
                                break;
                            }else{
                                $products = new Products();
                                $bal -= $item->count_balance;
                                $products->count = $item->count_balance;
                                $item->count_balance = 0;
                                $item->save(false);
                                $products->count_balance = 0;
                                $products->parent_id = $item->id;
                                $products->warehouse_id = $post['Documents']['warehouse_id'];
                                $products->nomenclature_id = $post['document_items'][$i];
                                $products->document_id = $model->id;
                                $products->type = 4;
                                if ($post['aah'] == 'true'){
                                    $products->price = floatval($post['pricewithaah'][$i]);
                                    $products->AAH = 1;
                                }else{
                                    $products->price = floatval($post['price'][$i]);
                                    $products->AAH = 0;
                                }
                                $products->created_at = date('Y-m-d H:i:s');
                                $products->updated_at = date('Y-m-d H:i:s');
                                $products->save(false);

                                $document_items = new DocumentItems();
                                $document_items->document_id = $model->id;
                                $document_items->nomenclature_id = $post['document_items'][$i];
                                $document_items->count = $products->count;
                                $document_items->price = floatval($post['price'][$i]);
                                $document_items->refuse_product_id = $products->id;
                                $document_items->price_with_aah = floatval($post['pricewithaah'][$i]);
                                $document_items->AAH = $post['aah'];
                                $document_items->created_at = date('Y-m-d H:i:s');
                                $document_items->updated_at = date('Y-m-d H:i:s');
                                $document_items->save(false);
                            }
                        }
                    }
                }
            $model_new = [];
            foreach ($document_items as $name => $value) {
                $model_new[$name] = $value;
            }
            foreach ($model as $name => $value) {
                $model_new[$name] = $value;
            }
            $session = Yii::$app->session;
            $document_tipe = (object) [
                '1' => 'մուտքի',
                '2' => 'ելքի',
                '3' => 'տեղափոխման',
                '4' => 'խոտանի',
                '6' => 'վերադարձի',
                '7' => 'մերժման',
                '10' => 'ետ վերադարձի',
            ];
            $user_name = Users::find()->select('*')->where(['id' => $session['user_id']])->asArray()->one();
            $text = $user_name['name'] . '(ն\ը) ստեղծել է ' . $document_tipe->{$post['Documents']['document_type']} . ' փաստաթուղթ։';
            Notifications::createNotifications('Ստեղծել փաստաթուղթ', $text,'documentscreate');
            Log::afterSaves('Create', $model_new, '', $url.'?'.'id'.'='.$model->id, $premission);
            $_POST['item_id'] = $model->id;

            if($post['newblocks'] || $post['new_fild_name']){
                Yii::$app->runAction('custom-fields/create-title',$post);
            }
            return $this->redirect(['index', 'id' => $model->id]);
        } else {
            $model->loadDefaultValues();
        }

//        if ($session['role_id'] == 1){
//            $users = Users::find()->select('id,name')->where(['status' => '1'])->andWhere(['role_id' => '4'])->asArray()->all();
//            $users = ArrayHelper::map($users,'id','name');
//        }elseif($session['role_id'] == 4){
//            $users = Users::find()->select('id,name')->where(['status' => '1'])->andWhere(['id' => $session['user_id']])->asArray()->all();
//            $users = ArrayHelper::map($users,'id','name');
//        }
        $warehouse = Warehouse::find()->select('id,name')->where(['status' => '1'])->asArray()->all();
        $warehouse =  ArrayHelper::map($warehouse,'id','name');
        $rates = Rates::find()->select('id,name')->where(['status' => ['1','2']])->asArray()->all();
        $rates = ArrayHelper::map($rates,'id','name');
        $query = Nomenclature::find();
        $countQuery = clone $query;
        $total = $countQuery->count();
        $nomenclatures = $query->select('nomenclature.id,nomenclature.image,nomenclature.name,nomenclature.price,
        nomenclature.cost,products.id as products_id,products.count,')
            ->leftJoin('products','nomenclature.id = products.nomenclature_id')
            ->offset(0)
            ->groupBy('nomenclature.id')
            ->limit(10)
//            ->orderBy(['nomenclature.id'=> SORT_DESC])
            ->asArray()
            ->all();
        if ($session['role_id'] == 4){
            $storekeeper = Users::findOne($session['user_id']);
            $to_warehouse =  Warehouse::find()->select('id,name')->where(['status' => '1'])->andWhere(['not', ['id' => $storekeeper->warehouse_id]])->asArray()->all();
            $to_warehouse = ArrayHelper::map($to_warehouse,'id','name');
        }else{
            $to_warehouse =  Warehouse::find()->select('id,name')->where(['status' => '1'])->asArray()->all();
            $to_warehouse = ArrayHelper::map($to_warehouse,'id','name');
        }
        $clietns = Clients::find()->select('id,name')->where(['status' => '1'])->asArray()->all();
        return $this->render('create', [
            'model' => $model,
            'clietns' => $clietns,
            'warehouse' => $warehouse,
            'rates' => $rates,
            'nomenclatures' => $nomenclatures,
            'total' => $total,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,
            'to_warehouse' => $to_warehouse,
        ]);
    }

    public function actionChangeOrders(){
        if ($this->request->isGet){
            $client_id = $this->request->get('clientId');
            $delivered_documents = Orders::find()
                ->select('orders.id, orders.orders_date')
                ->where(['orders.status'=> ['2','3']])
                ->andWhere(['clients_id' => $client_id])
                ->asArray()
                ->all();
            return $this->renderAjax('delivered-orders',[
               'delivered_documents' => $delivered_documents,
//                'deliver' => $deliver,
            ]);
        }
    }

    public function actionDeliveredOrders(){
        if ($this->request->isGet){
            $orders = Orders::find()
                ->select('order_items.id,SUM(order_items.count_by) as count_by,nomenclature.name, order_items.nom_id_for_name, products.AAH,
                   products.price,products.created_at')
                ->leftJoin('order_items', 'order_items.order_id = orders.id')
                ->leftJoin('products', 'order_items.product_id = products.id')
                ->leftJoin('nomenclature', 'order_items.nom_id_for_name = nomenclature.id')
                ->where(['orders.id' => $this->request->get('ordersId')])
                ->andWhere(['order_items.status' => '1'])
                ->groupBy('order_items.nom_id_for_name')
                ->orderBy(['products.id' => SORT_ASC])
                ->asArray()
                ->all();
            return json_encode($orders);
        }
    }
    public function actionGetDeliver(){
        if ($this->request->isGet) {
            $manager_id = ManagerDeliverCondition::find()->select('users.id,users.name')
                ->leftJoin('orders', 'manager_deliver_condition.manager_id = orders.user_id')
                ->leftJoin('users', 'manager_deliver_condition.deliver_id = users.id')
                ->where(['orders.id' => $this->request->get('ordersId')])
                ->andWhere(['users.status' => '1'])
                ->andWhere(['manager_deliver_condition.status' => '1'])
                ->asArray()
                ->all();
            return $this->renderAjax('selected-shipper',[
                'manager_id' => $manager_id,
            ]);
        }
    }
    public function actionCreateFields()
    {
        $have_access = Users::checkPremission(71);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $sub_page = [];
        if (Users::checkPremission(4)){
            $warehouse = ['name' => 'Պահեստ','address' => '/warehouse'];
            array_push($sub_page,$warehouse);
        }
        if (Users::checkPremission(40)){
            $documents = ['name' => 'Փաստաթղթեր','address' => '/documents'];
            array_push($sub_page,$documents);
        }
        if (Users::checkPremission(12)){
            $nom = ['name' => 'Անվանակարգ','address' => '/nomenclature'];
            array_push($sub_page,$nom);
        }
        if (Users::checkPremission(20)){
            $prod = ['name' => 'Ապրանք','address' => '/products'];
            array_push($sub_page,$prod);
        }
        if (Users::checkPremission(28)){
            $log = ['name' => 'Տեղեկամատյան','address' => '/log'];
            array_push($sub_page,$log);
        }
        $date_tab = [];

        $model = new Documents();
        if ($this->request->isPost) {
            $post = $this->request->post();

            if($post['newblocks'] || $post['new_fild_name']){
                Yii::$app->runAction('custom-fields/create-title',$post);
            }
            return $this->redirect(['index']);
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create-fields', [
            'model' => $model,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,
        ]);
    }

    /**
     * Updates an existing Documents model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionGetNomiclature(){
        $page = $_GET['paging'] ?? 1;
        $search_name = $_GET['nomenclature'] ?? false;
        $pageSize = 10;
        $offset = ($page-1) * $pageSize;
        $_GET['warehouse_id'] = $_GET['warehouse_id'] ?? $_POST['warehouse_id'];
        $_POST['warehouse_id'] = $_POST['warehouse_id'] ?? $_GET['warehouse_id'];
        $warehouse_id = $_POST['warehouse_id'] ?? $_GET['warehouse_id'];
        $_GET['documents_type'] = $_GET['documents_type'] ?? $_POST['documents_type'];
        $_POST['documents_type'] = $_POST['documents_type'] ?? $_GET['documents_type'];
        $document_type = $_POST['documents_type'];
        $query = Nomenclature::find();
        $countQuery = clone $query;
        $nomenclatures = $query->select('nomenclature.id as nomenclature_id,nomenclature.name,nomenclature.image,nomenclature.cost,nomenclature.price')
            ->where(['status' => '1'])
            ->groupBy('nomenclature.id');
        if ($search_name){
            $nomenclatures->andWhere(['like', 'nomenclature.name', $search_name])
                ->offset(0);
        }else{
            $nomenclatures->offset($offset)
                ->limit($pageSize);
        }
        $nomenclatures = $nomenclatures
            ->asArray()
            ->all();
        $total = $countQuery->count();
        if ($document_type != 1){
            $query = Products::find();
            $nomenclatures = $query->select('SUM(count_balance) as count_balance,products.id,nomenclature.id as nomenclature_id,
                nomenclature.image,nomenclature.name,nomenclature.cost,products.count,products.price')
                ->leftJoin('nomenclature','nomenclature.id = products.nomenclature_id')
                ->where(['and',['products.status' => 1,'nomenclature.status' => 1]])
                ->andWhere(['or',['products.type' => 1],['products.type' => 3],['products.type' => 8]])
                ->andWhere(['products.warehouse_id' => intval($warehouse_id)])
                ->groupBy('products.nomenclature_id')
                ->having(['!=', 'SUM(count_balance)', 0]);
            if ($search_name){
                $nomenclatures->andWhere(['like', 'nomenclature.name', $search_name])
                    ->offset(0);
            }else{
                $total = $nomenclatures->count();
                $nomenclatures->offset($offset)
                    ->limit($pageSize);
            }
//            $total = $nomenclatures->count();
            $nomenclatures = $nomenclatures
                ->asArray()
                ->all();
        }
        $id_count = $_POST['id_count'] ?? [];
        return $this->renderAjax('get-nom', [
            'nomenclatures' => $nomenclatures,
            'id_count' => $id_count ,
            'total' => $total,
            'search_name' => $search_name,
            'document_type' => $document_type,
        ]);
    }

    public function actionGetNomiclatureUpdate(){
        $page = $_GET['paging'] ?? 1;
        $urlId = intval($_POST['urlId']);
        $_GET['warehouse_id'] = $_GET['warehouse_id'] ?? $_POST['warehouse_id'];
        $_POST['warehouse_id'] = $_POST['warehouse_id'] ?? $_GET['warehouse_id'];
        $warehouse_id = $_POST['warehouse_id'] ?? $_GET['warehouse_id'];
        $_GET['documents_type'] = $_GET['documents_type'] ?? $_POST['documents_type'];
        $_POST['documents_type'] = $_POST['documents_type'] ?? $_GET['documents_type'];
        $document_type = $_POST['documents_type'];
        $search_name = $_GET['nomenclature'] ?? false;
        $pageSize = 10;
        $offset = ($page-1) * $pageSize;
        $document_items = DocumentItems::find()->select('document_items.*,nomenclature.name, nomenclature.id as nom_id')
            ->leftJoin('nomenclature','document_items.nomenclature_id = nomenclature.id')
            ->where(['document_items.document_id' => $urlId])
            ->andWhere(['document_items.status' => '1'])
            ->asArray()->all();
        $query = Nomenclature::find();
        $nomenclatures = $query->select('nomenclature.id as nomenclature_id,nomenclature.name,nomenclature.image,nomenclature.cost,nomenclature.price');
            if ($document_type == 'Մուտք'){
                $nomenclatures = $nomenclatures->where(['not in','id' , array_column($document_items,'nomenclature_id')]);
            }
        $nomenclatures = $nomenclatures->andWhere(['status' => '1'])
            ->groupBy('nomenclature.id');
        $total = $nomenclatures->count();
        if ($search_name){
            $nomenclatures->andWhere(['like', 'nomenclature.name', $search_name])
                ->offset(0);
        }else{
            $nomenclatures->offset($offset)
                ->limit($pageSize);
        }
        $nomenclatures = $nomenclatures
            ->asArray()
            ->all();
        if ($document_type != "Մուտք"){
            $query = Products::find();
            $nomenclatures = $query->select('SUM(count_balance) as count_balance, products.id,nomenclature.id as nomenclature_id,
                nomenclature.image,nomenclature.name,nomenclature.cost,products.count,products.price')
                ->leftJoin('nomenclature','nomenclature.id = products.nomenclature_id')
                ->where(['and',['products.status' => 1,'nomenclature.status' => 1,'products.type' => 1]])
                ->andWhere(['products.warehouse_id' => intval($warehouse_id)])
                ->groupBy('products.nomenclature_id')
                ->having(['!=', 'SUM(products.count_balance)', 0]);
            if ($search_name){
                $nomenclatures->andWhere(['like', 'nomenclature.name', $search_name])
                    ->offset(0);
            }else{
                $total = $nomenclatures->count();
                $nomenclatures->offset($offset)
                    ->limit($pageSize);
            }
//            $total = $nomenclatures->count();
            $nomenclatures = $nomenclatures
                ->asArray()
                ->all();
        }
        $id_count = $_POST['id_count'] ?? [];
        return $this->renderAjax('get-nom', [
            'nomenclatures' => $nomenclatures,
            'id_count' => $id_count ,
            'total' => $total,
            'search_name' => $search_name,
            'urlId' => $urlId,
            'document_type' => $document_type,
            'update' => 'update',
        ]);
    }
    public function actionUpdate($id)
    {
        $session = Yii::$app->session;
        $have_access = Users::checkPremission(38);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $model = $this->findModel($id);
        $sub_page = [];
        $date_tab = [];
        $model_new = [];
        $url = Url::to('', 'http');
        $oldattributes = Documents::find()
            ->select('*')
            ->where(['id' => $id])
            ->asArray()
            ->one();
        $premission = Premissions::find()
            ->select('name')
            ->where(['id' => 38])
            ->asArray()
            ->one();
        if ($this->request->isPost) {
            $post = $this->request->post();
            date_default_timezone_set('Asia/Yerevan');
            $model->user_id = $post['Documents']['user_id'];
            $model->warehouse_id = $post['Documents']['warehouse_id'];
            if ($post['Documents']['document_type'] == 'Տեղափոխություն'){
                $model->to_warehouse = $post['Documents']['to_warehouse'];
            }
            $model->rate_id = $post['Documents']['rate_id'];
            $model->rate_value = $post['Documents']['rate_value'];
            $model->comment = $post['Documents']['comment'];
            $model->date = $post['Documents']['date'];
            $model->updated_at = date('Y-m-d H:i:s');
            $model->save();
            Log::afterSaves('Update', $model, $oldattributes, $url, $premission);
            $items = $post['document_items'];
            foreach ($items as $j => $item){
                if ($post['count_'][$j]) {
                    if ($item != 'null') {
                        if ($post['Documents']['document_type'] === 'Մուտք') {
                            $products = Products::find()->select('products.*')
                                ->where(['and', ['document_id' => $model->id, 'type' => 1, 'nomenclature_id' => $post['items'][$j]]])->one();
                            $products->warehouse_id = $post['Documents']['warehouse_id'];
                            $products->nomenclature_id = $post['items'][$j];
                            $products->document_id = $model->id;
                            $products->type = 1;
                            $products->count = intval($post['count_'][$j]);
                            $products->count_balance = intval($post['count_'][$j]);
                            if ($post['aah'] == 'true') {
                                $products->price = floatval($post['pricewithaah'][$j]);
                                $products->AAH = 1;
                            } else {
                                $products->price = floatval($post['price'][$j]);
                                $products->AAH = 0;
                            }
                            $products->created_at = date('Y-m-d H:i:s');
                            $products->updated_at = date('Y-m-d H:i:s');
                            $products->save(false);

                            $document_items_update = DocumentItems::findOne(intval($item));
                            $document_items_update->document_id = $id;
                            $document_items_update->nomenclature_id = $post['items'][$j];
                            $document_items_update->count = intval($post['count_'][$j]);
                            $document_items_update->price = floatval($post['price'][$j]);
                            $document_items_update->refuse_product_id = $products->id;
                            $document_items_update->price_with_aah = floatval($post['pricewithaah'][$j]);
                            $document_items_update->AAH = $post['aah'];
                            $document_items_update->created_at = date('Y-m-d H:i:s');
                            $document_items_update->updated_at = date('Y-m-d H:i:s');
                            $document_items_update->save();
                        }
                    } else {


                        if ($post['Documents']['document_type'] === 'Մուտք') {
                            $products = new Products();
                            $products->warehouse_id = $post['Documents']['warehouse_id'];
                            $products->nomenclature_id = $post['items'][$j];
                            $products->document_id = $model->id;
                            $products->type = 1;
                            $products->count = intval($post['count_'][$j]);
                            $products->count_balance = intval($post['count_'][$j]);
                            if ($post['aah'] == 'true') {
                                $products->price = floatval($post['pricewithaah'][$j]);
                                $products->AAH = 1;
                            } else {
                                $products->price = floatval($post['price'][$j]);
                                $products->AAH = 0;
                            }
                            $products->created_at = date('Y-m-d H:i:s');
                            $products->updated_at = date('Y-m-d H:i:s');
                            $products->save(false);

                            $document_items_update = new DocumentItems();
                            $document_items_update->document_id = $id;
                            $document_items_update->nomenclature_id = $post['items'][$j];
                            $document_items_update->count = intval($post['count_'][$j]);
                            $document_items_update->price = floatval($post['price'][$j]);
                            $document_items_update->refuse_product_id = $products->id;
                            $document_items_update->price_with_aah = floatval($post['pricewithaah'][$j]);
                            $document_items_update->AAH = $post['aah'];
                            $document_items_update->created_at = date('Y-m-d H:i:s');
                            $document_items_update->updated_at = date('Y-m-d H:i:s');
                            $document_items_update->save();

                            foreach ($document_items_update as $name => $value) {
                                $model_new[$name] = $value;
                            }
                            foreach ($model as $name => $value) {
                                $model_new[$name] = $value;
                            }
                        }
                        if ($post['Documents']['document_type'] === 'Ելք') {
                            $first_product = Products::find()
                                ->where(['and',['nomenclature_id' => $post['items'][$j]],
                                    ['warehouse_id' => $post['Documents']['warehouse_id']]])
                                ->andWhere(['or',['type' => 1],['type' => 3],['type' => 8]])
                                ->andWhere(['!=', 'count_balance', 0])
                                ->andWhere(['status' => '1'])
                                ->all();
                            $bal = $post['count_'][$j];;
                            foreach ($first_product as $item) {
                                if ($item->count_balance - $bal >= 0) {
                                    $products = new Products();
                                    $item->count_balance -= $bal;
                                    $item->save(false);
                                    $products->count_balance = 0;
                                    $products->parent_id = $item->id;
                                    $products->count = $bal;
                                    $products->warehouse_id = $post['Documents']['warehouse_id'];
                                    $products->nomenclature_id = $post['items'][$j];
                                    $products->document_id = $model->id;
                                    $products->type = 5;
                                    if ($post['aah'] == 'true'){
                                        $products->price = floatval($post['pricewithaah'][$j]);
                                        $products->AAH = 1;
                                    }else{
                                        $products->price = floatval($post['price'][$j]);
                                        $products->AAH = 0;
                                    }
                                    $products->created_at = date('Y-m-d H:i:s');
                                    $products->updated_at = date('Y-m-d H:i:s');
                                    $products->save(false);

                                    $document_items_update = new DocumentItems();
                                    $document_items_update->document_id = $id;
                                    $document_items_update->nomenclature_id = $post['items'][$j];
                                    $document_items_update->count = $products->count;
                                    $document_items_update->price = floatval($post['price'][$j]);
                                    $document_items_update->refuse_product_id = $products->id;
                                    $document_items_update->price_with_aah = floatval($post['pricewithaah'][$j]);
                                    $document_items_update->AAH = $post['aah'];
                                    $document_items_update->created_at = date('Y-m-d H:i:s');
                                    $document_items_update->updated_at = date('Y-m-d H:i:s');
                                    $document_items_update->save();
                                    foreach ($document_items_update as $name => $value) {
                                        $model_new[$name] = $value;
                                    }
                                    foreach ($model as $name => $value) {
                                        $model_new[$name] = $value;
                                    }
                                    break;
                                }else{
                                    $products = new Products();
                                    $bal -= $item->count_balance;
                                    $products->count = $item->count_balance;
                                    $item->count_balance = 0;
                                    $item->save(false);
                                    $products->count_balance = 0;
                                    $products->parent_id = $item->id;
                                    $products->warehouse_id = $post['Documents']['warehouse_id'];
                                    $products->nomenclature_id = $post['items'][$j];
                                    $products->document_id = $model->id;
                                    $products->type = 5;
                                    if ($post['aah'] == 'true'){
                                        $products->price = floatval($post['pricewithaah'][$j]);
                                        $products->AAH = 1;
                                    }else{
                                        $products->price = floatval($post['price'][$j]);
                                        $products->AAH = 0;
                                    }
                                    $products->created_at = date('Y-m-d H:i:s');
                                    $products->updated_at = date('Y-m-d H:i:s');
                                    $products->save(false);

                                    $document_items_update = new DocumentItems();
                                    $document_items_update->document_id = $id;
                                    $document_items_update->nomenclature_id = $post['items'][$j];
                                    $document_items_update->count = $products->count;
                                    $document_items_update->price = floatval($post['price'][$j]);
                                    $document_items_update->refuse_product_id = $products->id;
                                    $document_items_update->price_with_aah = floatval($post['pricewithaah'][$j]);
                                    $document_items_update->AAH = $post['aah'];
                                    $document_items_update->created_at = date('Y-m-d H:i:s');
                                    $document_items_update->updated_at = date('Y-m-d H:i:s');
                                    $document_items_update->save();
                                    foreach ($document_items_update as $name => $value) {
                                        $model_new[$name] = $value;
                                    }
                                    foreach ($model as $name => $value) {
                                        $model_new[$name] = $value;
                                    }
                                }
                            }
                        }
                        if ($post['Documents']['document_type'] === 'Տեղափոխություն') {
                            $first_product = Products::find()
                                ->where(['and',['nomenclature_id' => $post['items'][$j]],
                                    ['warehouse_id' => $post['Documents']['warehouse_id']]])
                                ->andWhere(['or',['type' => 1],['type' => 3],['type' => 8]])
                                ->andWhere(['!=', 'count_balance', 0])
                                ->andWhere(['status' => '1'])
                                ->all();
                            $bal = $post['count_'][$j];;
                            foreach ($first_product as $item) {
                                if ($item->count_balance - $bal >= 0) {
                                    $products = new Products();
                                    $item->count_balance -= $bal;
                                    $item->save(false);
                                    $products->count_balance = 0;
                                    $products->parent_id = $item->id;
                                    $products->count = $bal;
                                    $products->warehouse_id = $post['Documents']['warehouse_id'];
                                    $products->nomenclature_id = $post['items'][$j];
                                    $products->document_id = $model->id;
                                    $products->type = 5;
                                    if ($post['aah'] == 'true'){
                                        $products->price = floatval($post['pricewithaah'][$j]);
                                        $products->AAH = 1;
                                    }else{
                                        $products->price = floatval($post['price'][$j]);
                                        $products->AAH = 0;
                                    }
                                    $products->created_at = date('Y-m-d H:i:s');
                                    $products->updated_at = date('Y-m-d H:i:s');
                                    $products->save(false);

                                    $to_warehouse_products = new Products();
                                    $to_warehouse_products->warehouse_id = $post['Documents']['to_warehouse']; // texapoxvac pahest
                                    $to_warehouse_products->nomenclature_id = $post['items'][$j];
                                    $to_warehouse_products->document_id = $model->id;
                                    $to_warehouse_products->type = 3;
                                    $to_warehouse_products->count = $bal;
                                    $to_warehouse_products->count_balance = $bal;
                                    $to_warehouse_products->parent_id = $products->id;
                                    if ($post['aah'] == 'true'){
                                        $to_warehouse_products->price = floatval($post['pricewithaah'][$j]);
                                        $to_warehouse_products->AAH = 1;
                                    }else{
                                        $to_warehouse_products->price = floatval($post['price'][$j]);
                                        $to_warehouse_products->AAH = 0;
                                    }
                                    $to_warehouse_products->created_at = date('Y-m-d H:i:s');
                                    $to_warehouse_products->updated_at = date('Y-m-d H:i:s');
                                    $to_warehouse_products->save(false);

                                    $document_items_update = new DocumentItems();
                                    $document_items_update->document_id = $id;
                                    $document_items_update->nomenclature_id = $post['items'][$j];
                                    $document_items_update->count = $products->count;
                                    $document_items_update->price = floatval($post['price'][$j]);
                                    $document_items_update->refuse_product_id = $products->id;
                                    $document_items_update->price_with_aah = floatval($post['pricewithaah'][$j]);
                                    $document_items_update->AAH = $post['aah'];
                                    $document_items_update->created_at = date('Y-m-d H:i:s');
                                    $document_items_update->updated_at = date('Y-m-d H:i:s');
                                    $document_items_update->save();
                                    foreach ($document_items_update as $name => $value) {
                                        $model_new[$name] = $value;
                                    }
                                    foreach ($model as $name => $value) {
                                        $model_new[$name] = $value;
                                    }
                                    break;
                                }else{
                                    $products = new Products();
                                    $bal -= $item->count_balance;
                                    $products->count = $item->count_balance;
                                    $item->count_balance = 0;
                                    $item->save(false);
                                    $products->count_balance = 0;
                                    $products->parent_id = $item->id;
                                    $products->warehouse_id = $post['Documents']['warehouse_id'];
                                    $products->nomenclature_id = $post['items'][$j];
                                    $products->document_id = $model->id;
                                    $products->type = 5;
                                    if ($post['aah'] == 'true'){
                                        $products->price = floatval($post['pricewithaah'][$j]);
                                        $products->AAH = 1;
                                    }else{
                                        $products->price = floatval($post['price'][$j]);
                                        $products->AAH = 0;
                                    }
                                    $products->created_at = date('Y-m-d H:i:s');
                                    $products->updated_at = date('Y-m-d H:i:s');
                                    $products->save(false);

                                    $to_warehouse_products = new Products();
                                    $to_warehouse_products->warehouse_id = $post['Documents']['to_warehouse']; // texapoxvac pahest
                                    $to_warehouse_products->nomenclature_id = $post['items'][$j];
                                    $to_warehouse_products->document_id = $model->id;
                                    $to_warehouse_products->type = 3;
                                    $to_warehouse_products->count = $products->count;
                                    $to_warehouse_products->count_balance = $products->count;
                                    $to_warehouse_products->parent_id = $products->id;
                                    if ($post['aah'] == 'true'){
                                        $to_warehouse_products->price = floatval($post['pricewithaah'][$j]);
                                        $to_warehouse_products->AAH = 1;
                                    }else{
                                        $to_warehouse_products->price = floatval($post['price'][$j]);
                                        $to_warehouse_products->AAH = 0;
                                    }
                                    $to_warehouse_products->created_at = date('Y-m-d H:i:s');
                                    $to_warehouse_products->updated_at = date('Y-m-d H:i:s');
                                    $to_warehouse_products->save(false);

                                    $document_items_update = new DocumentItems();
                                    $document_items_update->document_id = $id;
                                    $document_items_update->nomenclature_id = $post['items'][$j];
                                    $document_items_update->count = $products->count;
                                    $document_items_update->price = floatval($post['price'][$j]);
                                    $document_items_update->refuse_product_id = $products->id;
                                    $document_items_update->price_with_aah = floatval($post['pricewithaah'][$j]);
                                    $document_items_update->AAH = $post['aah'];
                                    $document_items_update->created_at = date('Y-m-d H:i:s');
                                    $document_items_update->updated_at = date('Y-m-d H:i:s');
                                    $document_items_update->save();
                                    foreach ($document_items_update as $name => $value) {
                                        $model_new[$name] = $value;
                                    }
                                    foreach ($model as $name => $value) {
                                        $model_new[$name] = $value;
                                    }
                                }
                            }
                        }
                        if ($post['Documents']['document_type'] === 'Խոտան') {
                            $first_product = Products::find()
                                ->where(['and',['nomenclature_id' => $post['items'][$j]],
                                    ['warehouse_id' => $post['Documents']['warehouse_id']]])
                                ->andWhere(['or',['type' => 1],['type' => 3],['type' => 8]])
                                ->andWhere(['!=', 'count_balance', 0])
                                ->andWhere(['status' => '1'])
                                ->all();
                            $bal = $post['count_'][$j];;
                            foreach ($first_product as $item) {
                                if ($item->count_balance - $bal >= 0) {
                                    $products = new Products();
                                    $item->count_balance -= $bal;
                                    $item->save(false);
                                    $products->count_balance = 0;
                                    $products->parent_id = $item->id;
                                    $products->count = $bal;
                                    $products->warehouse_id = $post['Documents']['warehouse_id'];
                                    $products->nomenclature_id = $post['items'][$j];
                                    $products->document_id = $model->id;
                                    $products->type = 4;
                                    if ($post['aah'] == 'true'){
                                        $products->price = floatval($post['pricewithaah'][$j]);
                                        $products->AAH = 1;
                                    }else{
                                        $products->price = floatval($post['price'][$j]);
                                        $products->AAH = 0;
                                    }
                                    $products->created_at = date('Y-m-d H:i:s');
                                    $products->updated_at = date('Y-m-d H:i:s');
                                    $products->save(false);

                                    $document_items_update = new DocumentItems();
                                    $document_items_update->document_id = $id;
                                    $document_items_update->nomenclature_id = $post['items'][$j];
                                    $document_items_update->count = $products->count;
                                    $document_items_update->price = floatval($post['price'][$j]);
                                    $document_items_update->refuse_product_id = $products->id;
                                    $document_items_update->price_with_aah = floatval($post['pricewithaah'][$j]);
                                    $document_items_update->AAH = $post['aah'];
                                    $document_items_update->created_at = date('Y-m-d H:i:s');
                                    $document_items_update->updated_at = date('Y-m-d H:i:s');
                                    $document_items_update->save();
                                    foreach ($document_items_update as $name => $value) {
                                        $model_new[$name] = $value;
                                    }
                                    foreach ($model as $name => $value) {
                                        $model_new[$name] = $value;
                                    }
                                    break;
                                }else{
                                    $products = new Products();
                                    $bal -= $item->count_balance;
                                    $products->count = $item->count_balance;
                                    $item->count_balance = 0;
                                    $item->save(false);
                                    $products->count_balance = 0;
                                    $products->parent_id = $item->id;
                                    $products->warehouse_id = $post['Documents']['warehouse_id'];
                                    $products->nomenclature_id = $post['items'][$j];
                                    $products->document_id = $model->id;
                                    $products->type = 4;
                                    if ($post['aah'] == 'true'){
                                        $products->price = floatval($post['pricewithaah'][$j]);
                                        $products->AAH = 1;
                                    }else{
                                        $products->price = floatval($post['price'][$j]);
                                        $products->AAH = 0;
                                    }
                                    $products->created_at = date('Y-m-d H:i:s');
                                    $products->updated_at = date('Y-m-d H:i:s');
                                    $products->save(false);

                                    $document_items_update = new DocumentItems();
                                    $document_items_update->document_id = $id;
                                    $document_items_update->nomenclature_id = $post['items'][$j];
                                    $document_items_update->count = $products->count;
                                    $document_items_update->price = floatval($post['price'][$j]);
                                    $document_items_update->refuse_product_id = $products->id;
                                    $document_items_update->price_with_aah = floatval($post['pricewithaah'][$j]);
                                    $document_items_update->AAH = $post['aah'];
                                    $document_items_update->created_at = date('Y-m-d H:i:s');
                                    $document_items_update->updated_at = date('Y-m-d H:i:s');
                                    $document_items_update->save();
                                    foreach ($document_items_update as $name => $value) {
                                        $model_new[$name] = $value;
                                    }
                                    foreach ($model as $name => $value) {
                                        $model_new[$name] = $value;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $_POST['item_id'] = $model->id;
            if($post['newblocks'] || $post['new_fild_name']){
                Yii::$app->runAction('custom-fields/create-title',$post);
            }
            Log::afterSaves('Update', $model, $oldattributes, $url, $premission);
            return $this->redirect(['index', 'id' => $model->id]);
        }
        $get_documents_id = Documents::findOne($id);
        if ($session['role_id'] == 1 || $session['role_id'] == 2 || $session['role_id'] == 3){
            $users = Users::find()->select('id,name')->where(['status' => '1'])->andWhere(['role_id' => '4'])->andWhere(['id' => $get_documents_id->user_id])->asArray()->all();
            $users = ArrayHelper::map($users,'id','name');
        }elseif($session['role_id'] == 4){
            $users = Users::find()->select('id,name')->where(['status' => '1'])->andWhere(['id' => $session['user_id']])->asArray()->all();
            $users = ArrayHelper::map($users,'id','name');
        }
        $warehouse = Warehouse::find()->select('id,name')->where(['id' => $get_documents_id->warehouse_id])->asArray()->all();
        if ($session['role_id'] != 4){
            $warehouse =  ArrayHelper::map($warehouse,'id','name');
        }else{
            $warehouse = $warehouse[0]['id'];
        }
        $to_warehouse =  Warehouse::find()->select('id,name')->where(['id' => $get_documents_id->to_warehouse])->andWhere(['status' => '1'])->asArray()->all();
        $to_warehouse = ArrayHelper::map($to_warehouse,'id','name');
        $rates = Rates::find()->select('id,name')->where(['status' => ['1','2']])->asArray()->all();
        $rates = ArrayHelper::map($rates,'id','name');

        $document_items = DocumentItems::find()->select('document_items.*,nomenclature.name, nomenclature.id as nom_id')
            ->leftJoin('nomenclature','document_items.nomenclature_id = nomenclature.id')
            ->where(['document_items.document_id' => $id])
            ->andWhere(['document_items.status' => '1'])
            ->asArray()->all();

        $delivered_documents = Orders::find()
            ->select('orders.id, orders.orders_date, clients.id as clients_id, clients.name, users.id as deliver_id, users.name as deliver_name')
            ->leftJoin('documents', 'documents.orders_id = orders.id')
            ->leftJoin('clients', 'clients.id = orders.clients_id')
            ->leftJoin('users', 'users.id = documents.deliver_id')
            ->where(['orders.status'=> ['4','5']])
            ->where(['documents.id' => $id])
            ->andWhere(['documents.orders_id' => new \yii\db\Expression('orders.id')])
            ->andWhere(['documents.document_type' => '10'])
            ->asArray()->one();

        $aah = DocumentItems::find()->select('AAH')->where(['document_id' => $id])->asArray()->one();
        return $this->render('update', [
            'model' => $model,
            'users' => $users,
            'warehouse' => $warehouse,
            'rates' => $rates,
            'document_items' => $document_items,
            'aah' => $aah,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,
            'to_warehouse' => $to_warehouse,
            'delivered_documents' => $delivered_documents,
        ]);
    }

    public function actionChangeStorekeeper(){
        if($this->request->isGet){
            $keeper = Users::findOne(['warehouse_id' => $this->request->get('warehouse_id')]);
            if ($keeper != null){
                $users = Users::find()->select('id,name')->where(['status' => '1'])->andWhere(['role_id' => '4'])->andWhere(['id' => $keeper->id])->asArray()->one();
                return $this->renderAjax('users-input',[
                    'users' => $users
                ]);
            }else{
                $users = ['id' => null, 'name' => 'Դատարկ'];
                return $this->renderAjax('users-input',[
                    'users' => $users
                ]);
            }
        }
    }
    public function actionDelivered($id){
        $have_access = Users::checkPremission(75);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $session = Yii::$app->session;
        $url = Url::to('', 'http');
        $url = str_replace('delivered', 'view', $url);
        $premission = Premissions::find()
            ->select('name')
            ->where(['id' => 75])
            ->asArray()
            ->one();
        $model = array();
        date_default_timezone_set('Asia/Yerevan');
        $document = Documents::findOne($id);
        $new_document = new Documents();
        $new_document->user_id = $document->user_id;
        $new_document->deliver_id = $document->deliver_id;
        $new_document->warehouse_id = $document->warehouse_id;
        $new_document->rate_id = 1;
        $new_document->rate_value = 1;
        $new_document->document_type = 8;
        $new_document->comment = 'Վերադարձի մուտքագրված փաստաթուղթ';
        $new_document->status = '1';
        $new_document->date = date('Y-m-d H:i:s');;
        $new_document->created_at = date('Y-m-d H:i:s');
        $new_document->updated_at = date('Y-m-d H:i:s');
        $new_document->save(false);
        foreach ($new_document as $index => $item) {
            $model[$index] = $item;
        }
        $document_items = DocumentItems::find()->where(['document_id' => $id])->asArray()->all();

        if (!empty($document_items)){
            for ($k = 0;$k < count($document_items); $k++){
                $new_product = new Products();
                $new_product->warehouse_id = $document->warehouse_id;
                $new_product->nomenclature_id = $document_items[$k]['nomenclature_id'];
                $new_product->document_id = $new_document->id;
                $new_product->type = 8;
                $new_product->count = $document_items[$k]['count'];
                $new_product->count_balance = $document_items[$k]['count'];
                if ($document_items[$k]['AAH'] == 'true'){
                    $new_product->AAH = 1;
                    $new_product->price = $document_items[$k]['price_with_aah'];
                }else{
                    $new_product->price = $document_items[$k]['price'];
                    $new_product->AAH = 0;
                }
                $new_product->status = '1';
                $new_product->created_at = date('Y-m-d H:i:s');
                $new_product->updated_at = date('Y-m-d H:i:s');
                $new_product->save(false);
                foreach ($new_product as $index => $item) {
                    $model[$index.$k] = $item;
                }
                $new_document_items = new DocumentItems();
                $new_document_items->document_id = $new_document->id;
                $new_document_items->nomenclature_id = $document_items[$k]['nomenclature_id'];
                $new_document_items->refuse_product_id = $new_product->id;
                $new_document_items->count = $document_items[$k]['count'];
                if ($document_items[$k]['wastrel'] != ''){
                    $new_document_items->wastrel = $document_items[$k]['wastrel'];
                }
                $new_document_items->price = $document_items[$k]['price'];
                $new_document_items->price_with_aah = $document_items[$k]['price_with_aah'];
                $new_document_items->AAH =  $document_items[$k]['AAH'];
                $new_document_items->status = '1';
                $new_document_items->created_at = date('Y-m-d H:i:s');
                $new_document_items->updated_at = date('Y-m-d H:i:s');
                $new_document_items->save(false);
                foreach ($new_document_items as $index => $item) {
                    $model[$index.$k.'-'] = $item;
                }
            }
        }

        $document_status = Documents::findOne($id);
        $document_status->status = '0';
        $document_status->save(false);
        Log::afterSaves('delivered', $model, '', $url, $premission);
        if($session['role_id'] == 4 || $session['role_id'] == 2 || $session['role_id'] == 3) {
            $user_name = Users::find()->select('*')->where(['id' => $session['user_id']])->asArray()->one();
            $text = $user_name['name'] . '(ն\ը) մուտք է արել պահեստ վերադարձված ապրանք(ներ)ը։';
            Notifications::createNotifications('Վերադարձված ապրանք(ներ)ի մուտքագրված փաստաթուղթ', $text, 'documentsdelivered');
        }

        $document_items_status = DocumentItems::find()->where(['document_id' => $id])->all();
        if (!empty($document_items_status)){
            foreach ($document_items_status as $value){
                $value->status = '0';
                $value->save(false);
            }
        }
        return $this->redirect(['index']);
    }

    public function actionRefuseModal(){
        if($this->request->isGet){
            $get = $this->request->get('documentId');
        }
        $url = Url::to('', 'http');
        $url = str_replace('refuse-modal?documentId', 'view?id', $url);
        $premission = Premissions::find()
            ->select('name')
            ->where(['id' => 81])
            ->asArray()
            ->one();
        $model = array();
        Log::afterSaves('refuse', $model, '', $url, $premission);
        return $this->renderAjax('refuse',[
            'id' => $get,
        ]);
    }
    public function actionMessage(){
        $sub_page = [
//            ['name' => 'Պահեստ','address' => '/warehouse'],
//            ['name' => 'Անվանակարգ','address' => '/nomenclature'],
//            ['name' => 'Ապրանք','address' => '/products'],
//            ['name' => 'Տեղեկամատյան','address' => '/log'],
        ];
        $date_tab = [];
        if ($this->request->isPost){
            $post = $this->request->post();
            $document = Documents::findOne($post['document_id']);
            $document->document_type = 7;
            $document->comment = 'Մերժման պատճառն է՝ «' . $post['message'] . '»';
            $document->save(false);
            $session = Yii::$app->session;
            $mes = $post['message'];
            $message_length = strlen($mes);
            $ch = '';
            for ($i = 0; $i < $message_length; $i++) {
                $ch .= $mes[$i];
                if (($i + 1) % 20 == 0 && $i != $message_length - 1) {
                    $ch .= "\n";
                }
            }
            if($session['role_id'] == 4 || $session['role_id'] == 2 || $session['role_id'] == 3){
                $user_name = Users::find()->select('*')->where(['id' => $session['user_id']])->asArray()->one();
                $text = $user_name['name'] . '(ն/ը) ' . 'մերժել է ապրանքի հետ վերդարձը, նշելով մերժման պատճառը ՝ «'
                    . $ch . '»: '
                    . "\n" .
                    '<a href="http://champagne/documents/update?id=' . $post['document_id'] . '">
                    <img width="15" height="15" src="/upload/view.png" alt="view"></a>';
                Notifications::createNotifications('Մերժել վերադարձը', $text,'refusalreturn');
            }
            return $this->redirect('message');
        }

        $searchModel = new DocumentsSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,

        ]);
    }
    /**
     * Deletes an existing Documents model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
//        echo "<pre>";
        $have_access = Users::checkPremission(39);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $premission = Premissions::find()
            ->select('name')
            ->where(['id' => 39])
            ->asArray()
            ->one();
        $oldattributes = Documents::find()
            ->select(['users.id', 'users.name', 'users.username'])
            ->leftJoin('users', 'users.id = documents.user_id')
            ->where(['documents.id' => $id])
            ->asArray()
            ->one();
        $documents = Documents::findOne($id);
        if ($documents->document_type == '1' || $documents->document_type == '10'){
            $documents->status = '0';
            $documents->save(false);
            $delete_document_items =DocumentItems::find()->where(['document_id' => $id])->andWhere(['status' => '1'])->all();
            foreach ($delete_document_items as $item){
                $item->status = '0';
                $item->save(false);
                $delete_product = Products::findOne($item->refuse_product_id);
                $delete_product->status = '0';
                $delete_product->save(false);
            }
        }elseif ($documents->document_type == '2'){
            $documents->status = '0';
            $documents->save(false);
            $delete_document_items =DocumentItems::find()->where(['document_id' => $id])->andWhere(['status' => '1'])->all();
            foreach ($delete_document_items as $item){
                $item->status = '0';
                $item->save(false);
                $delete_product = Products::findOne($item->refuse_product_id);
                $delete_product->status = '0';
                $delete_product->save(false);
                $refuse_product = Products::findOne($delete_product->parent_id);
                $refuse_product->count_balance += $delete_product->count;
                $refuse_product->save(false);
            }
        }elseif ($documents->document_type == '3'){
            $documents->status = '0';
            $documents->save(false);
            $delete_document_items =DocumentItems::find()->where(['document_id' => $id])->andWhere(['status' => '1'])->all();
            foreach ($delete_document_items as $item) {
                $item->status = '0';
                $item->save(false);
                $delete_product = Products::findOne($item->refuse_product_id);
                $delete_product->status = '0';
                $delete_product->save(false);
                $refuse_product = Products::findOne($delete_product->parent_id);
                $refuse_product->count_balance += $delete_product->count;
                $refuse_product->save(false);
                $exit_product = Products::findOne(['parent_id' => $delete_product->id,'document_id' => $delete_product->document_id, 'type' => 3]);
                $exit_product->status = '0';
                $exit_product->save(false);
            }
        }elseif ($documents->document_type == '4'){
            $documents->status = '0';
            $documents->save(false);
            $delete_document_items =DocumentItems::find()->where(['document_id' => $id])->andWhere(['status' => '1'])->all();
            foreach ($delete_document_items as $item) {
                $item->status = '0';
                $item->save(false);
                $delete_product = Products::findOne($item->refuse_product_id);
                $delete_product->status = '0';
                $delete_product->save(false);
                $refuse_product = Products::findOne($delete_product->parent_id);
                $refuse_product->count_balance += $delete_product->count;
                $refuse_product->save(false);
            }
        }elseif ($documents->document_type == '6'){
            $documents->status = '0';
            $documents->save(false);
            $delete_document_items =DocumentItems::find()->where(['document_id' => $id])->andWhere(['status' => '1'])->all();
            foreach ($delete_document_items as $item) {
                $item->status = '0';
                $item->save(false);
                $delete_product = Products::findOne($item->refuse_product_id);
                $delete_product->status = '0';
                $delete_product->save(false); // popoxvac apranqi kam yndhanur apranqi depqum stugel petqa es tox te che
            }
        }elseif ($documents->document_type == '7'){
            $documents->status = '0';
            $documents->save(false);
            $delete_document_items =DocumentItems::find()->where(['document_id' => $id])->andWhere(['status' => '1'])->all();
            foreach ($delete_document_items as $item) {
                $item->status = '0';
                $item->save(false);
                $delete_product = Products::findOne($item->refuse_product_id);
                $delete_product->status = '0';
                $delete_product->save(false);
            }
        }elseif ($documents->document_type == '8'){
            $documents->status = '0';
            $documents->save(false);
            $delete_document_items =DocumentItems::find()->where(['document_id' => $id])->andWhere(['status' => '1'])->all();
            foreach ($delete_document_items as $item) {
                $item->status = '0';
                $item->save(false);
                $delete_product = Products::findOne($item->refuse_product_id);
                $delete_product->status = '0';
                $delete_product->save(false);
            }
        }elseif ($documents->document_type == '9'){
            $documents->status = '0';
            $documents->save(false);
            $delete_document_items =DocumentItems::find()->where(['document_id' => $id])->andWhere(['status' => '1'])->all();
            foreach ($delete_document_items as $item) {
                $item->status = '0';
                $item->save(false);
                $delete_product = Products::findOne($item->refuse_product_id);
                $delete_product->status = '0';
                $delete_product->save(false);
                $refuse_product = Products::findOne($delete_product->parent_id);
                $refuse_product->count_balance += $delete_product->count;
                $refuse_product->save(false);
            }
        }
        Log::afterSaves('Delete', '', $oldattributes['name'] . ' ' . $oldattributes['username'], '#', $premission);
        return $this->redirect(['index']);
    }

    public function actionDeleteDocumentItems(){
        $have_access = Users::checkPremission(39);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        if ($this->request->isPost){
            $items_id = $this->request->post('docItemsId');
            $doc_type = $this->request->post('docType');
            $exist_document = DocumentItems::find()->where(['id' => $items_id])->asArray()->one();
            $exist_document_items = DocumentItems::find()->where(['status' => '1'])->andWhere(['document_id' => $exist_document['document_id']])->count();
            if ($exist_document_items == 1){
                return json_encode(false);

            }else{
                if ($doc_type == 'Մուտք'){
                    $delete_items = DocumentItems::findOne($items_id);
                    $delete_items->status = '0';
                    $delete_items->save(false);
                    $delete_product = Products::findOne($delete_items->refuse_product_id);
                    $delete_product->status = '0';
                    $delete_product->save(false);
                    return json_encode(true);
                }elseif ($doc_type == 'Ելք'){
                    $delete_items = DocumentItems::findOne($items_id);
                    $delete_items->status = '0';
                    $delete_items->save(false);
                    $delete_product = Products::findOne($delete_items->refuse_product_id);
                    $delete_product->status = '0';
                    $delete_product->save(false);
                    $refuse_product = Products::findOne($delete_product->parent_id);
                    $refuse_product->count_balance += $delete_product->count;
                    $refuse_product->save(false);
                    return json_encode(true);
                }elseif ($doc_type == 'Տեղափոխություն'){
                    $delete_items = DocumentItems::findOne($items_id);
                    $delete_items->status = '0';
                    $delete_items->save(false);
                    $delete_product = Products::findOne($delete_items->refuse_product_id);
                    $delete_product->status = '0';
                    $delete_product->save(false);
                    $refuse_product = Products::findOne($delete_product->parent_id);
                    $refuse_product->count_balance += $delete_product->count;
                    $refuse_product->save(false);
                    $exit_product = Products::findOne(['parent_id' => $delete_product->id,'document_id' => $delete_product->document_id, 'type' => 3]);
                    $exit_product->status = '0';
                    $exit_product->save(false);
                    return json_encode(true);

                }elseif($doc_type == 'Խոտան'){
                    $delete_items = DocumentItems::findOne($items_id);
                    $delete_items->status = '0';
                    $delete_items->save(false);
                    $delete_product = Products::findOne($delete_items->refuse_product_id);
                    $delete_product->status = '0';
                    $delete_product->save(false);
                    $refuse_product = Products::findOne($delete_product->parent_id);
                    $refuse_product->count_balance += $delete_product->count;
                    $refuse_product->save(false);
                    return json_encode(true);
                }
            }
        }
    }

    /**
     * Finds the Documents model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Documents the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Documents::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionChangeRates(){
        if ($this->request->isPost){
            $id = $this->request->post('id');
            if ($id != 1){
                return json_encode('others');
            }else{
                return json_encode('amd');
            }
        }
    }
    public  function actionDocumentFilterStatus(){
        if ($_GET){
            $searchModel = new DocumentsSearch();
            $dataProvider = $searchModel->search($this->request->queryParams);
            $sub_page = [];
            $date_tab = [];
            $page_value = null;
            $warehouse = Warehouse::find()->select('id,name')->asArray()->all();
            $res = Yii::$app->runAction('custom-fields/get-table-data',['page'=>'documents']);

            if(isset($_GET["dp-1-page"]))
                $page_value = intval($_GET["dp-1-page"]);
            $render_array = [
                'sub_page' => $sub_page,
                'date_tab' => $date_tab,
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'page_value' => $page_value,
                'warehouse' => $warehouse,
                'new_fields'=>$res,

            ];

            if(Yii::$app->request->isAjax){
                return $this->renderAjax('widget', $render_array);
            }else{
                return $this->render('widget', $render_array);
            }
        }
    }

    public function actionPrintDoc($id){
        $document_items = DocumentItems::find()->select('document_items.*,nomenclature.name, nomenclature.id as nom_id')
            ->leftJoin('nomenclature','document_items.nomenclature_id = nomenclature.id')
            ->where(['document_items.document_id' => $id])
            ->andWhere(['document_items.status' => '1'])
            ->asArray()->all();
        return $this->renderAjax('get-update-trs', [
            'document_items' => $document_items,
        ]);
    }
    public function actionPrintDocFild(){
        $res = Yii::$app->runAction('custom-fields/get-table-data',['page'=>'documents']);
        $attribute = [];
        for ($i = 0; $i < count($res); $i++) {
            array_push($attribute, $res[$i]['attribute']);
        }
        return json_encode(['attribute' => $attribute]);
    }
    public function actionChangeWastrel(){
        if ($this->request->isGet){
            $items = $this->request->get('documentItemsId');
            $document_items = DocumentItems::find()
                ->select('document_items.id,document_items.count,document_items.price,nomenclature.name')
                ->leftJoin('nomenclature', 'nomenclature.id = document_items.nomenclature_id')
                ->where(['document_items.id' => intval($items)])
                ->asArray()
                ->one();
            return $this->renderAjax('change-wastrel',[
                'document_items' => $document_items,
            ]);
        }
    }
    public function actionChangingCount(){
        if ($this->request->isPost) {
            $post = $this->request->post();
            if ($post['wastrel'] != ''){
                $document_items = DocumentItems::findOne($post['itemsId']);
                $document_items->count -= intval($post['wastrel']);
                if ($document_items->wastrel == null){
                    $document_items->wastrel = intval($post['wastrel']);
                }else{
                    $document_items->wastrel += intval($post['wastrel']);
                }
                $document_items->save(false);
                return json_encode('change');
            }

        }
    }
    public  function actionFilterStatus(){
        if ($_GET){
            $page_value = null;
            if(isset($_GET["page"]))
                $page_value = intval($_GET["page"]);
            $searchModel = new DocumentsSearch();
            $dataProvider = $searchModel->search($this->request->queryParams);
            $res = Yii::$app->runAction('custom-fields/get-table-data',['page'=>'documents']);

            $sub_page = [];
            $date_tab = [];
            $render_array = [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'sub_page' => $sub_page,
                'date_tab' => $date_tab,
                'page_value' => $page_value,
                'new_fields'=>$res,

            ];
            if(Yii::$app->request->isAjax){
                if (isset($_GET['clickXLSX']) && $_GET['clickXLSX'] === 'clickXLSX') {
                    $this->layout = 'index.php';
                    $render_array['data_size'] = 'max';
                    return $this->renderAjax('widget', $render_array);
                } else {
                    return $this->renderAjax('widget', $render_array);
                }

            }else{
                if (isset($_GET['clickXLSX']) && $_GET['clickXLSX'] === 'clickXLSX') {
                    $this->layout = 'index.php';
                    $render_array['data_size'] = 'max';
                    return $this->render('widget', $render_array);
                } else {
                    return $this->render('widget', $render_array);
                }
            }
        }
    }

    public function actionReports($id){
        $have_access = Users::checkPremission(22);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $model = $this->findModel($id);
        $sub_page = [];
        $date_tab = [];
        $users = Users::find()->select('id, name')->asArray()->all();
        $users = ArrayHelper::map($users,'id','name');
        $warehouse = Warehouse::find()->select('id,name')->asArray()->all();
        $warehouse = ArrayHelper::map($warehouse,'id','name');
        $rate = Rates::find()->select('id,name')->asArray()->all();
        $rate = ArrayHelper::map($rate,'id','name');
        $document_items = DocumentItems::find()->select('document_items.*,nomenclature.name, nomenclature.id as nom_id')
            ->leftJoin('nomenclature','document_items.nomenclature_id = nomenclature.id')
            ->where(['document_items.document_id' => $id])
            ->andWhere(['document_items.status' => '1'])
            ->asArray()
            ->all();
        $delivered_documents = Orders::find()
            ->select('orders.id, orders.orders_date, clients.id as clients_id, clients.name, users.id as deliver_id, users.name as deliver_name')
            ->leftJoin('documents', 'documents.orders_id = orders.id')
            ->leftJoin('clients', 'clients.id = orders.clients_id')
            ->leftJoin('users', 'users.id = documents.deliver_id')
            ->where(['orders.status'=> ['4','5']])
            ->where(['documents.id' => $id])
            ->andWhere(['documents.orders_id' => new \yii\db\Expression('orders.id')])
            ->andWhere(['documents.document_type' => '10'])
            ->asArray()->one();
        return $this->renderAjax('report', [
            'document_items' => $document_items,
            'model' => $model,
            'users' => $users,
            'warehouse' => $warehouse,
            'rate' => $rate,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,
            'delivered_documents' => $delivered_documents,
        ]);
    }


}
