<?php

namespace app\controllers;

use app\models\Clients;
use app\models\Discount;
use app\models\DiscountClients;
use app\models\DiscountProducts;
use app\models\Nomenclature;
use app\models\OrderItems;
use app\models\Orders;
use app\models\OrdersSearch;
use app\models\Products;
use app\models\Users;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\Pagination;

/**
 * OrdersController implements the CRUD actions for Orders model.
 */
class OrdersController extends Controller
{
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
     * Lists all Orders models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $have_access = Users::checkPremission(24);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $sub_page = [];
        $searchModel = new OrdersSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        if($_POST){
            return $this->renderAjax('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'data_size' => 'max',
                'sub_page' => $sub_page,
            ]);
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'sub_page' => $sub_page
        ]);
    }

    /**
     * Displays a single Orders model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $sub_page = [];
        return $this->render('view', [
            'model' => $this->findModel($id),
            'sub_page' => $sub_page,

        ]);
    }

    /**
     * Creates a new Orders model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
//        echo "<pre>";

        $have_access = Users::checkPremission(21);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $model = new Orders();

        if ($this->request->isPost) {
            date_default_timezone_set('Asia/Yerevan');
            $post = $this->request->post();
            $model->user_id = $post['Orders']['user_id'];
            $model->clients_id = $post['Orders']['clients_id'];
            $model->total_price = $post['Orders']['total_price'];
            $model->total_count = $post['Orders']['total_count'];
            $model->comment = $post['Orders']['comment'];
            $model->orders_date = $post['Orders']['orders_date'];
            $model->created_at = date('Y-m-d H:i:s');
            $model->updated_at = date('Y-m-d H:i:s');
//            $model->save();
            for ($i = 0; $i < count($post['order_items']); $i++){
                $product_write_out = new Products();
                $product_write_out->warehouse_id = 1;
                $product_write_out->nomenclature_id = $post['order_items'][$i];
                $product_write_out->document_id = $model->id;
                $product_write_out->type = 2;
                $product_write_out->count = -intval($post['count_'][$i]);
                $product_write_out->price = $post['price'][$i];
                $product_write_out->created_at = date('Y-m-d H:i:s');
                $product_write_out->updated_at = date('Y-m-d H:i:s');
//                $product_write_out->save();
            }
            echo "<pre>";
            $total_debt = 0;
            for ($i = 0; $i < count($post['order_items']); $i++){
                $total_exist = Discount::find()->select('discount.*,discount_products.*,discount_clients.*')
                    ->leftJoin('discount_products','discount.id = discount_products.discount_id')
                    ->leftJoin('discount_clients','discount.id = discount_clients.discount_id')
                    ->where(['and',['discount_products.status' => 1,'discount.status' => 1,'discount_clients.status' => 1]])
                    ->andWhere(['or',
                        ['<=', 'discount.start_date', $post['Orders']['orders_date']],
                        ['discount.start_date' => null]
                    ])
                    ->andWhere(['or',
                        ['>=', 'discount.end_date', $post['Orders']['orders_date']],
                        ['discount.end_date' => null]
                    ])
                    ->andWhere(['discount_products.product_id' => $post['order_items'][$i]])
                    ->andWhere(['discount_clients.client_id' => $post['Orders']['clients_id']])
                    ->exists();
                $nomenclatures_exist = Discount::find()->select('discount.*,discount_products.*')
                    ->leftJoin('discount_products','discount.id = discount_products.discount_id')
                    ->where(['and',['discount_products.status' => 1,'discount.status' => 1]])
                    ->andWhere(['or',
                        ['<=', 'discount.start_date', $post['Orders']['orders_date']],
                        ['discount.start_date' => null]
                    ])
                    ->andWhere(['or',
                        ['>=', 'discount.end_date', $post['Orders']['orders_date']],
                        ['discount.end_date' => null]
                    ])
                    ->andWhere(['discount_products.product_id' => $post['order_items'][$i]])
                    ->exists();

                $clients_exist = Discount::find()->select('discount.*,discount_clients.*')
                    ->leftJoin('discount_clients','discount.id = discount_clients.discount_id')
                    ->where(['and',['discount_clients.status' => 1,'discount.status' => 1]])
                    ->andWhere(['or',
                        ['<=', 'discount.start_date', $post['Orders']['orders_date']],
                        ['discount.start_date' => null]
                    ])
                    ->andWhere(['or',
                        ['>=', 'discount.end_date', $post['Orders']['orders_date']],
                        ['discount.end_date' => null]
                    ])
                    ->andWhere(['discount_clients.client_id' => $post['Orders']['clients_id']])
                    ->exists();
                if ($total_exist){
                    $discount = Discount::find()->select('discount.*,discount_products.*,discount_clients.*')
                        ->leftJoin('discount_products','discount.id = discount_products.discount_id')
                        ->leftJoin('discount_clients','discount.id = discount_clients.discount_id')
                        ->where(['and',['discount_products.status' => 1,'discount.status' => 1,'discount_clients.status' => 1]])
                        ->andWhere(['or',
                            ['<=', 'discount.start_date', $post['Orders']['orders_date']],
                            ['discount.start_date' => null]
                        ])
                        ->andWhere(['or',
                            ['>=', 'discount.end_date', $post['Orders']['orders_date']],
                            ['discount.end_date' => null]
                        ])
                        ->andWhere(['discount_products.product_id' => $post['order_items'][$i]])
                        ->andWhere(['discount_clients.client_id' => $post['Orders']['clients_id']])
                        ->orderBy(['discount.discount_sortable' => SORT_ASC])
                        ->asArray()
                        ->all();
                    var_dump($discount);
                    $count = 0;
                    $count_discount_id = '';
                    $price = intval($post['price'][$i]);
                        for ($j = 0; $j < count($discount); $j++){
                            if ($discount[$j]['discount_filter_type'] === 'count' && $discount[$j]['min'] < $post['Orders']['total_count'] &&  $discount[$j]['max'] > $post['Orders']['total_count']){
                            if ($discount[$j]['discount_check'] == 0 && $count == 0){
                                $count++;
                                if ($discount[$j]['type'] == 'percent'){
                                    $count_discount_id .= $discount[$j]['discount_id'].',';
                                    $price =  $price - ($price * $discount[$j]['discount'])/100;
                                }else{
                                    $count_discount_id .= $discount[$j]['discount_id'].',';
                                    $price = $price - $discount[$j]['discount'];
                                }
                            }elseif ($discount[$j]['discount_check'] == 1){
                                if ($discount[$j]['type'] == 'percent'){
                                    $count_discount_id .= $discount[$j]['discount_id'].',';
                                    $price =  $price - ($price * $discount[$j]['discount'])/100;
                                }else{
                                    $count_discount_id .= $discount[$j]['discount_id'].',';
                                    $price = $price - $discount[$j]['discount'];
                                }
                            }
                            }elseif ($discount[$j]['discount_filter_type'] === 'price' && $discount[$j]['min'] < $post['Orders']['total_price'] &&  $discount[$j]['max'] > $post['Orders']['total_price']){
                            if ($discount[$j]['discount_check'] == 0 && $count == 0){
                                $count++;
                                if ($discount[$j]['type'] == 'percent'){
                                    $count_discount_id .= $discount[$j]['discount_id'].',';
                                    $price =  $price - ($price * $discount[$j]['discount'])/100;
                                }else{
                                    $count_discount_id .= $discount[$j]['discount_id'].',';
                                    $price = $price - $discount[$j]['discount'];
                                }
                            }elseif ($discount[$j]['discount_check'] == 1){
                                if ($discount[$j]['type'] == 'percent'){
                                    $count_discount_id .= $discount[$j]['discount_id'].',';
                                    $price =  $price - ($price * $discount[$j]['discount'])/100;
                                }else{
                                    $count_discount_id .= $discount[$j]['discount_id'].',';
                                    $price = $price - $discount[$j]['discount'];
                                }
                            }
                            }elseif(empty($discount[$j]['discount_filter_type'])){
                                if ($discount[$j]['discount_check'] == 0 && $count == 0){
                                    $count++;
                                    if ($discount[$j]['type'] == 'percent'){
                                        $count_discount_id .= $discount[$j]['discount_id'].',';
                                        $price =  $price - ($price * $discount[$j]['discount'])/100;
                                    }else{
                                        $count_discount_id .= $discount[$j]['discount_id'].',';
                                        $price = $price - $discount[$j]['discount'];
                                    }
                                }elseif ($discount[$j]['discount_check'] == 1){
                                    if ($discount[$j]['type'] == 'percent'){
                                        $count_discount_id .= $discount[$j]['discount_id'].',';
                                        $price =  $price - ($price * $discount[$j]['discount'])/100;
                                    }else{
                                        $count_discount_id .= $discount[$j]['discount_id'].',';
                                        $price = $price - $discount[$j]['discount'];
                                    }
                                }
                            }
                        }
//                $row_price = $price * $post['count_'][$i];
//                $total_debt += $row_price;
//                $row_before_price = intval($post['price'][$i]) * $post['count_'][$i];
//                $format_number = round($row_price,2);
//                $format_before_price = round($row_before_price,2);
//                $order_items_create = new OrderItems();
//                $order_items_create->order_id = $model->id;
//                $order_items_create->product_id = $post['product_id'][$i];
//                $order_items_create->price = $format_number;
//                $order_items_create->count = $post['count_'][$i];
//                $order_items_create->cost = $post['cost'][$i] * $post['count_'][$i];
//                $order_items_create->discount = (intval($post['price'][$i]) - $price) * $post['count_'][$i];
//                $order_items_create->price_before_discount = $format_before_price;
//                $order_items_create->count_discount_id = substr($count_discount_id,0,-1);
//                $order_items_create->created_at = date('Y-m-d H:i:s');
//                $order_items_create->updated_at = date('Y-m-d H:i:s');
//                $order_items_create->save(false);
                }elseif ($nomenclatures_exist){
                    $discount = Discount::find()->select('discount.*,discount_products.*')
                        ->leftJoin('discount_products','discount.id = discount_products.discount_id')
                        ->where(['and',['discount_products.status' => 1,'discount.status' => 1]])
                        ->andWhere(['or',
                            ['<=', 'discount.start_date', $post['Orders']['orders_date']],
                            ['discount.start_date' => null]
                        ])
                        ->andWhere(['or',
                            ['>=', 'discount.end_date', $post['Orders']['orders_date']],
                            ['discount.end_date' => null]
                        ])
                        ->andWhere(['discount_products.product_id' => $post['order_items'][$i]])
                        ->orderBy(['discount.discount_sortable' => SORT_ASC])
                        ->asArray()
                        ->all();
                    $count = 0;
                    $count_discount_id = '';
                    $price = intval($post['price'][$i]);
                    for ($j = 0; $j < count($discount); $j++){
                        if ($discount[$j]['discount_filter_type'] === 'count' && $discount[$j]['min'] < $post['Orders']['total_count'] &&  $discount[$j]['max'] > $post['Orders']['total_count']){
                            if ($discount[$j]['discount_check'] == 0 && $count == 0){
                                $count++;
                                if ($discount[$j]['type'] == 'percent'){
                                    $count_discount_id .= $discount[$j]['discount_id'].',';
                                    $price =  $price - ($price * $discount[$j]['discount'])/100;
                                }else{
                                    $count_discount_id .= $discount[$j]['discount_id'].',';
                                    $price = $price - $discount[$j]['discount'];
                                }
                            }elseif ($discount[$j]['discount_check'] == 1){
                                if ($discount[$j]['type'] == 'percent'){
                                    $count_discount_id .= $discount[$j]['discount_id'].',';
                                    $price =  $price - ($price * $discount[$j]['discount'])/100;
                                }else{
                                    $count_discount_id .= $discount[$j]['discount_id'].',';
                                    $price = $price - $discount[$j]['discount'];
                                }
                            }
                        }elseif ($discount[$j]['discount_filter_type'] === 'price' && $discount[$j]['min'] < $post['Orders']['total_price'] &&  $discount[$j]['max'] > $post['Orders']['total_price']){
                            if ($discount[$j]['discount_check'] == 0 && $count == 0){
                                $count++;
                                if ($discount[$j]['type'] == 'percent'){
                                    $count_discount_id .= $discount[$j]['discount_id'].',';
                                    $price =  $price - ($price * $discount[$j]['discount'])/100;
                                }else{
                                    $count_discount_id .= $discount[$j]['discount_id'].',';
                                    $price = $price - $discount[$j]['discount'];
                                }
                            }elseif ($discount[$j]['discount_check'] == 1){
                                if ($discount[$j]['type'] == 'percent'){
                                    $count_discount_id .= $discount[$j]['discount_id'].',';
                                    $price =  $price - ($price * $discount[$j]['discount'])/100;
                                }else{
                                    $count_discount_id .= $discount[$j]['discount_id'].',';
                                    $price = $price - $discount[$j]['discount'];
                                }
                            }
                        }elseif(empty($discount[$j]['discount_filter_type'])){
                            if ($discount[$j]['discount_check'] == 0 && $count == 0){
                                $count++;
                                if ($discount[$j]['type'] == 'percent'){
                                    $count_discount_id .= $discount[$j]['discount_id'].',';
                                    $price =  $price - ($price * $discount[$j]['discount'])/100;
                                }else{
                                    $count_discount_id .= $discount[$j]['discount_id'].',';
                                    $price = $price - $discount[$j]['discount'];
                                }
                            }elseif ($discount[$j]['discount_check'] == 1){
                                if ($discount[$j]['type'] == 'percent'){
                                    $count_discount_id .= $discount[$j]['discount_id'].',';
                                    $price =  $price - ($price * $discount[$j]['discount'])/100;
                                }else{
                                    $count_discount_id .= $discount[$j]['discount_id'].',';
                                    $price = $price - $discount[$j]['discount'];
                                }
                            }
                        }
                    }
                $row_price = $price * $post['count_'][$i];
                $total_debt += $row_price;
                $row_before_price = intval($post['price'][$i]) * $post['count_'][$i];
                $format_number = round($row_price,2);
                $format_before_price = round($row_before_price,2);
                $order_items_create = new OrderItems();
                $order_items_create->order_id = $model->id;
                $order_items_create->product_id = $post['product_id'][$i];
                $order_items_create->price = $format_number;
                $order_items_create->count = $post['count_'][$i];
                $order_items_create->cost = $post['cost'][$i] * $post['count_'][$i];
                $order_items_create->discount = (intval($post['price'][$i]) - $price) * $post['count_'][$i];
                $order_items_create->price_before_discount = $format_before_price;
                $order_items_create->count_discount_id = substr($count_discount_id,0,-1);
                $order_items_create->created_at = date('Y-m-d H:i:s');
                $order_items_create->updated_at = date('Y-m-d H:i:s');
//                $order_items_create->save(false);
                }elseif ($clients_exist){
                    $discount = Discount::find()->select('discount.*,discount_clients.*')
                        ->leftJoin('discount_clients','discount.id = discount_clients.discount_id')
                        ->where(['and',['discount_clients.status' => 1,'discount.status' => 1]])
                        ->andWhere(['or',
                            ['<=', 'discount.start_date', $post['Orders']['orders_date']],
                            ['discount.start_date' => null]
                        ])
                        ->andWhere(['or',
                            ['>=', 'discount.end_date', $post['Orders']['orders_date']],
                            ['discount.end_date' => null]
                        ])
                        ->andWhere(['discount_clients.client_id' => $post['Orders']['clients_id']])
                        ->orderBy(['discount.discount_sortable' => SORT_ASC])
                        ->asArray()
                        ->all();
                    $count = 0;
                    $count_discount_id = '';
                    $price = intval($post['price'][$i]);
                    for ($j = 0; $j < count($discount); $j++){
                        if ($discount[$j]['discount_filter_type'] === 'count' && $discount[$j]['min'] < $post['Orders']['total_count'] &&  $discount[$j]['max'] > $post['Orders']['total_count']){
                            if ($discount[$j]['discount_check'] == 0 && $count == 0){
                                $count++;
                                if ($discount[$j]['type'] == 'percent'){
                                    $count_discount_id .= $discount[$j]['discount_id'].',';
                                    $price =  $price - ($price * $discount[$j]['discount'])/100;
                                }else{
                                    $count_discount_id .= $discount[$j]['discount_id'].',';
                                    $price = $price - $discount[$j]['discount'];
                                }
                            }elseif ($discount[$j]['discount_check'] == 1){
                                if ($discount[$j]['type'] == 'percent'){
                                    $count_discount_id .= $discount[$j]['discount_id'].',';
                                    $price =  $price - ($price * $discount[$j]['discount'])/100;
                                }else{
                                    $count_discount_id .= $discount[$j]['discount_id'].',';
                                    $price = $price - $discount[$j]['discount'];
                                }
                            }
                        }elseif ($discount[$j]['discount_filter_type'] === 'price' && $discount[$j]['min'] < $post['Orders']['total_price'] &&  $discount[$j]['max'] > $post['Orders']['total_price']){
                            if ($discount[$j]['discount_check'] == 0 && $count == 0){
                                $count++;
                                if ($discount[$j]['type'] == 'percent'){
                                    $count_discount_id .= $discount[$j]['discount_id'].',';
                                    $price =  $price - ($price * $discount[$j]['discount'])/100;
                                }else{
                                    $count_discount_id .= $discount[$j]['discount_id'].',';
                                    $price = $price - $discount[$j]['discount'];
                                }
                            }elseif ($discount[$j]['discount_check'] == 1){
                                if ($discount[$j]['type'] == 'percent'){
                                    $count_discount_id .= $discount[$j]['discount_id'].',';
                                    $price =  $price - ($price * $discount[$j]['discount'])/100;
                                }else{
                                    $count_discount_id .= $discount[$j]['discount_id'].',';
                                    $price = $price - $discount[$j]['discount'];
                                }
                            }
                        }elseif(empty($discount[$j]['discount_filter_type'])){
                            if ($discount[$j]['discount_check'] == 0 && $count == 0){
                                $count++;
                                if ($discount[$j]['type'] == 'percent'){
                                    $count_discount_id .= $discount[$j]['discount_id'].',';
                                    $price =  $price - ($price * $discount[$j]['discount'])/100;
                                }else{
                                    $count_discount_id .= $discount[$j]['discount_id'].',';
                                    $price = $price - $discount[$j]['discount'];
                                }
                            }elseif ($discount[$j]['discount_check'] == 1){
                                if ($discount[$j]['type'] == 'percent'){
                                    $count_discount_id .= $discount[$j]['discount_id'].',';
                                    $price =  $price - ($price * $discount[$j]['discount'])/100;
                                }else{
                                    $count_discount_id .= $discount[$j]['discount_id'].',';
                                    $price = $price - $discount[$j]['discount'];
                                }
                            }
                        }

                    }
                $row_price = $price * $post['count_'][$i];
                $total_debt += $row_price;
                $row_before_price = intval($post['price'][$i]) * $post['count_'][$i];
                $format_number = round($row_price,2);
                $format_before_price = round($row_before_price,2);
                $order_items_create = new OrderItems();
                $order_items_create->order_id = $model->id;
                $order_items_create->product_id = $post['product_id'][$i];
                $order_items_create->price = $format_number;
                $order_items_create->count = $post['count_'][$i];
                $order_items_create->cost = $post['cost'][$i] * $post['count_'][$i];
                $order_items_create->discount = (intval($post['price'][$i]) - $price) * $post['count_'][$i];
                $order_items_create->price_before_discount = $format_before_price;
                $order_items_create->count_discount_id = substr($count_discount_id,0,-1);
                $order_items_create->created_at = date('Y-m-d H:i:s');
                $order_items_create->updated_at = date('Y-m-d H:i:s');
//                $order_items_create->save(false);
                }else{
                $order_items_create = new OrderItems();
                $order_items_create->order_id = $model->id;
                $order_items_create->product_id = $post['product_id'][$i];
                $order_items_create->price = $post['price'][$i] * $post['count_'][$i];
                $order_items_create->count = $post['count_'][$i];
                $order_items_create->cost = $post['cost'][$i] * $post['count_'][$i];
                $order_items_create->discount = 0;
                $order_items_create->price_before_discount = $post['price'][$i] * $post['count_'][$i];
                $order_items_create->created_at = date('Y-m-d H:i:s');
                $order_items_create->updated_at = date('Y-m-d H:i:s');
//                $order_items_create->save(false);
                $total_debt += $post['price'][$i] * $post['count_'][$i];
                }
            }
            exit();

            $orders_total_debt = Orders::findOne($model->id);
            $orders_total_debt->total_price = $total_debt;
//            $orders_total_debt->save(false);
                return $this->redirect(['index', 'id' => $model->id]);
        } else {
            $model->loadDefaultValues();
        }
        $sub_page = [];
        $query = Nomenclature::find();
        $countQuery = clone $query;
        $total = $countQuery->count();
        $nomenclatures = $query->select('nomenclature.id,nomenclature.image,nomenclature.name,nomenclature.price,
        nomenclature.cost,products.id as products_id,products.count')
            ->leftJoin('products','nomenclature.id = products.nomenclature_id')
            ->offset(0)
            ->groupBy('nomenclature.id')
            ->limit(10)
            ->asArray()
            ->all();


        $clients = Clients::find()->select('id, name')->asArray()->all();
        $clients = ArrayHelper::map($clients,'id','name');
        $users = Users::find()->select('id, name')->asArray()->all();
        $users = ArrayHelper::map($users,'id','name');
        return $this->render('create', [
            'model' => $model,
            'users' => $users,
            'clients' => $clients,
            'nomenclatures' => $nomenclatures,
            'total' => $total,
            'sub_page' => $sub_page,
//            'pagination' => $pagination,
//            'count' => $count,
        ]);
    }

    /**
     * Updates an existing Orders model.
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
        $query = Nomenclature::find();
        $countQuery = clone $query;
        $nomenclatures = $query->select('nomenclature.id,nomenclature.image,nomenclature.name,nomenclature.price,
        nomenclature.cost,products.id as products_id,products.count')
            ->leftJoin('products','nomenclature.id = products.nomenclature_id')
            ->groupBy('nomenclature.id');
        if ($search_name){
            $nomenclatures->andWhere(['like', 'nomenclature.name', $search_name])
                ->offset(0)
                ->limit(10);
            $total = $nomenclatures->count();
        }else{
            $total = $countQuery->count();
            $nomenclatures->offset($offset)
                ->limit($pageSize);
        }
        $nomenclatures = $nomenclatures
            ->asArray()
            ->all();
        return $this->renderAjax('get-nom', [
            'nomenclatures' => $nomenclatures,
            'total' => $total,
            'search_name' => $search_name
        ]);
    }
    public function actionUpdate($id)
    {
//        echo "<pre>";
        $have_access = Users::checkPremission(22);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $model = $this->findModel($id);
        if ($this->request->isPost) {
            date_default_timezone_set('Asia/Yerevan');
            $post = $this->request->post();
            $model->user_id = $post['Orders']['user_id'];
            $model->clients_id = $post['Orders']['clients_id'];
            $model->total_price = $post['Orders']['total_price'];
            $model->total_count = $post['Orders']['total_count'];
            $model->comment = $post['Orders']['comment'];
            $model->orders_date = $post['Orders']['orders_date'];
            $model->updated_at = date('Y-m-d H:i:s');
            $model->save();
            $items = $post['order_items'];
            $quantity = 0;
            $total_debt = 0;
            foreach ($items as $k => $item){
                if($item != 'null'){
                    $order_item = OrderItems::findOne($item);
                    $order_item->order_id = $id;
                    $order_item->product_id = $post['product_id'][$k];
                    $order_item->price = ($order_item->price / $order_item->count) * $post['count_'][$k];
                    $order_item->cost = $post['cost'][$k] * $post['count_'][$k];
                    $order_item->discount = ($order_item->discount / $order_item->count) * $post['count_'][$k];
                    $order_item->price_before_discount = ($order_item->price_before_discount / $order_item->count) * $post['count_'][$k];
                    $order_item->count = $post['count_'][$k];
                    $order_item->updated_at = date('Y-m-d H:i:s');
                    $quantity += $order_item->count;
                    $total_debt += $order_item->price;
                    $order_item->save(false);

                    $product_write_out = Products::find()->select('products.*')
                        ->where(['and',['document_id' => $model->id,'type' => 2,'nomenclature_id' => $post['nom_id'][$k]]])->one();
                    $product_write_out->warehouse_id = 1;
                    $product_write_out->nomenclature_id = $post['nom_id'][$k];
                    $product_write_out->price = $post['price'][$k];
                    $product_write_out->count = -intval($post['count_'][$k]);
                    $product_write_out->type = 2;
                    $product_write_out->updated_at = date('Y-m-d H:i:s');
                    $product_write_out->save();
                } else {
                    $total_exist = Discount::find()->select('discount.*,discount_products.*,discount_clients.*')
                        ->leftJoin('discount_products','discount.id = discount_products.discount_id')
                        ->leftJoin('discount_clients','discount.id = discount_clients.discount_id')
                        ->where(['and',['discount_products.status' => 1,'discount.status' => 1,'discount_clients.status' => 1]])
                        ->andWhere(['or',
                            ['<=', 'discount.start_date', $post['Orders']['orders_date']],
                            ['discount.start_date' => null]
                        ])
                        ->andWhere(['or',
                            ['>=', 'discount.end_date', $post['Orders']['orders_date']],
                            ['discount.end_date' => null]
                        ])
                        ->andWhere(['discount_products.product_id' => $post['nom_id'][$k]])
                        ->andWhere(['discount_clients.client_id' => $post['Orders']['clients_id']])
                        ->exists();
                    $nomenclatures_exist = Discount::find()->select('discount.*,discount_products.*')
                        ->leftJoin('discount_products','discount.id = discount_products.discount_id')
                        ->where(['and',['discount_products.status' => 1,'discount.status' => 1]])
                        ->andWhere(['or',
                            ['<=', 'discount.start_date', $post['Orders']['orders_date']],
                            ['discount.start_date' => null]
                        ])
                        ->andWhere(['or',
                            ['>=', 'discount.end_date', $post['Orders']['orders_date']],
                            ['discount.end_date' => null]
                        ])
                        ->andWhere(['discount_products.product_id' => $post['nom_id'][$k]])
                        ->exists();
                    $clients_exist = Discount::find()->select('discount.*,discount_clients.*')
                        ->leftJoin('discount_clients','discount.id = discount_clients.discount_id')
                        ->where(['and',['discount_clients.status' => 1,'discount.status' => 1]])
                        ->andWhere(['or',
                            ['<=', 'discount.start_date', $post['Orders']['orders_date']],
                            ['discount.start_date' => null]
                        ])
                        ->andWhere(['or',
                            ['>=', 'discount.end_date', $post['Orders']['orders_date']],
                            ['discount.end_date' => null]
                        ])
                        ->andWhere(['discount_clients.client_id' => $post['Orders']['clients_id']])
                        ->exists();
                    if ($total_exist){
                        $discount = Discount::find()->select('discount.*,discount_products.*,discount_clients.*')
                            ->leftJoin('discount_products','discount.id = discount_products.discount_id')
                            ->leftJoin('discount_clients','discount.id = discount_clients.discount_id')
                            ->where(['and',['discount_products.status' => 1,'discount.status' => 1,'discount_clients.status' => 1]])
                            ->andWhere(['or',
                                ['<=', 'discount.start_date', $post['Orders']['orders_date']],
                                ['discount.start_date' => null]
                            ])
                            ->andWhere(['or',
                                ['>=', 'discount.end_date', $post['Orders']['orders_date']],
                                ['discount.end_date' => null]
                            ])
                            ->andWhere(['discount_products.product_id' => $post['nom_id'][$k]])
                            ->andWhere(['discount_clients.client_id' => $post['Orders']['clients_id']])
                            ->orderBy(['discount.discount_sortable' => SORT_ASC])
                            ->asArray()
                            ->all();
                        $count = 0;
                        $count_discount_id = '';
                        $price = intval($post['price'][$k]);
                        for ($j = 0; $j < count($discount); $j++){
                            if ($discount[$j]['discount_check'] == 0 && $count == 0){
                                $count++;
                                if ($discount[$j]['type'] == 'percent'){
                                    $count_discount_id .= $discount[$j]['discount_id'].',';
                                    $price =  $price - ($price * $discount[$j]['discount'])/100;
                                }else{
                                    $count_discount_id .= $discount[$j]['discount_id'].',';
                                    $price = $price - $discount[$j]['discount'];
                                }
                            }elseif ($discount[$j]['discount_check'] == 1){
                                if ($discount[$j]['type'] == 'percent'){
                                    $count_discount_id .= $discount[$j]['discount_id'].',';
                                    $price =  $price - ($price * $discount[$j]['discount'])/100;
                                }else{
                                    $count_discount_id .= $discount[$j]['discount_id'].',';
                                    $price = $price - $discount[$j]['discount'];
                                }
                            }
                        }
                        $row_price = $price * $post['count_'][$k];
                        $total_debt += $row_price;
                        $quantity += $post['count_'][$k];
                        $row_before_price = intval($post['price'][$k]) * $post['count_'][$k];
                        $format_number = round($row_price,2);
                        $format_before_price = round($row_before_price,2);
                        $order_items_create = new OrderItems();
                        $order_items_create->order_id = $model->id;
                        $order_items_create->product_id = $post['product_id'][$k];
                        $order_items_create->price = $format_number;
                        $order_items_create->count = $post['count_'][$k];
                        $order_items_create->cost = $post['cost'][$k] * $post['count_'][$k];
                        $order_items_create->discount = (intval($post['price'][$k]) - $price) * $post['count_'][$k];
                        $order_items_create->price_before_discount = $format_before_price;
                        $order_items_create->count_discount_id = substr($count_discount_id,0,-1);
                        $order_items_create->created_at = date('Y-m-d H:i:s');
                        $order_items_create->updated_at = date('Y-m-d H:i:s');
                        $order_items_create->save(false);
                    }elseif ($nomenclatures_exist){
                        $discount = Discount::find()->select('discount.*,discount_products.*')
                            ->leftJoin('discount_products','discount.id = discount_products.discount_id')
                            ->where(['and',['discount_products.status' => 1,'discount.status' => 1]])
                            ->andWhere(['or',
                                ['<=', 'discount.start_date', $post['Orders']['orders_date']],
                                ['discount.start_date' => null]
                            ])
                            ->andWhere(['or',
                                ['>=', 'discount.end_date', $post['Orders']['orders_date']],
                                ['discount.end_date' => null]
                            ])
                            ->andWhere(['discount_products.product_id' => $post['nom_id'][$k]])
                            ->orderBy(['discount.discount_sortable' => SORT_ASC])
                            ->asArray()
                            ->all();
                        $count = 0;
                        $count_discount_id = '';
                        $price = intval($post['price'][$k]);
                        for ($j = 0; $j < count($discount); $j++){
                            if ($discount[$j]['discount_check'] == 0 && $count == 0){
                                $count++;
                                if ($discount[$j]['type'] == 'percent'){
                                    $count_discount_id .= $discount[$j]['discount_id'].',';
                                    $price =  $price - ($price * $discount[$j]['discount'])/100;
                                }else{
                                    $count_discount_id .= $discount[$j]['discount_id'].',';
                                    $price = $price - $discount[$j]['discount'];
                                }
                            }elseif ($discount[$j]['discount_check'] == 1){
                                if ($discount[$j]['type'] == 'percent'){
                                    $count_discount_id .= $discount[$j]['discount_id'].',';
                                    $price =  $price - ($price * $discount[$j]['discount'])/100;
                                }else{
                                    $count_discount_id .= $discount[$j]['discount_id'].',';
                                    $price = $price - $discount[$j]['discount'];
                                }
                            }
                        }
                        $row_price = $price * $post['count_'][$k];
                        $total_debt += $row_price;
                        $quantity += $post['count_'][$k];
                        $row_before_price = intval($post['price'][$k]) * $post['count_'][$k];
                        $format_number = round($row_price,2);
                        $format_before_price = round($row_before_price,2);
                        $order_items_create = new OrderItems();
                        $order_items_create->order_id = $model->id;
                        $order_items_create->product_id = $post['product_id'][$k];
                        $order_items_create->price = $format_number;
                        $order_items_create->count = $post['count_'][$k];
                        $order_items_create->cost = $post['cost'][$k] * $post['count_'][$k];
                        $order_items_create->discount = (intval($post['price'][$k]) - $price) * $post['count_'][$k];
                        $order_items_create->price_before_discount = $format_before_price;
                        $order_items_create->count_discount_id = substr($count_discount_id,0,-1);
                        $order_items_create->created_at = date('Y-m-d H:i:s');
                        $order_items_create->updated_at = date('Y-m-d H:i:s');
                        $order_items_create->save(false);
                    }elseif ($clients_exist){
                        $discount = Discount::find()->select('discount.*,discount_clients.*')
                            ->leftJoin('discount_clients','discount.id = discount_clients.discount_id')
                            ->where(['and',['discount_clients.status' => 1,'discount.status' => 1]])
                            ->andWhere(['or',
                                ['<=', 'discount.start_date', $post['Orders']['orders_date']],
                                ['discount.start_date' => null]
                            ])
                            ->andWhere(['or',
                                ['>=', 'discount.end_date', $post['Orders']['orders_date']],
                                ['discount.end_date' => null]
                            ])
                            ->andWhere(['discount_clients.client_id' => $post['Orders']['clients_id']])
                            ->orderBy(['discount.discount_sortable' => SORT_ASC])
                            ->asArray()
                            ->all();
                        $count = 0;
                        $count_discount_id = '';
                        $price = intval($post['price'][$k]);
                        for ($j = 0; $j < count($discount); $j++){
                            if ($discount[$j]['discount_check'] == 0 && $count == 0){
                                $count++;
                                if ($discount[$j]['type'] == 'percent'){
                                    $count_discount_id .= $discount[$j]['discount_id'].',';
                                    $price =  $price - ($price * $discount[$j]['discount'])/100;
                                }else{
                                    $count_discount_id .= $discount[$j]['discount_id'].',';
                                    $price = $price - $discount[$j]['discount'];
                                }
                            }elseif ($discount[$j]['discount_check'] == 1){
                                if ($discount[$j]['type'] == 'percent'){
                                    $count_discount_id .= $discount[$j]['discount_id'].',';
                                    $price =  $price - ($price * $discount[$j]['discount'])/100;
                                }else{
                                    $count_discount_id .= $discount[$j]['discount_id'].',';  // else chmoranal
                                    $price = $price - $discount[$j]['discount'];
                                }
                            }
                        }
                        $row_price = $price * $post['count_'][$k];
                        $total_debt += $row_price;
                        $quantity += $post['count_'][$k];
                        $row_before_price = intval($post['price'][$k]) * $post['count_'][$k];
                        $format_number = round($row_price,2);
                        $format_before_price = round($row_before_price,2);
                        $order_items_create = new OrderItems();
                        $order_items_create->order_id = $model->id;
                        $order_items_create->product_id = $post['product_id'][$k];
                        $order_items_create->price = $format_number;
                        $order_items_create->count = $post['count_'][$k];
                        $order_items_create->cost = $post['cost'][$k] * $post['count_'][$k];
                        $order_items_create->discount = (intval($post['price'][$k]) - $price) * $post['count_'][$k];
                        $order_items_create->price_before_discount = $format_before_price;
                        $order_items_create->count_discount_id = substr($count_discount_id,0,-1);
                        $order_items_create->created_at = date('Y-m-d H:i:s');
                        $order_items_create->updated_at = date('Y-m-d H:i:s');
                        $order_items_create->save(false);
                    }else{

                        $order_items_create = new OrderItems();
                        $order_items_create->order_id = $model->id;
                        $order_items_create->product_id = $post['product_id'][$k];
                        $order_items_create->price = $post['price'][$k] * $post['count_'][$k];
                        $order_items_create->count = $post['count_'][$k];
                        $order_items_create->cost = $post['cost'][$k] * $post['count_'][$k];
                        $order_items_create->discount = 0;
                        $order_items_create->price_before_discount = $post['price'][$k] * $post['count_'][$k];
                        $order_items_create->count_discount_id = 'չկա';
                        $order_items_create->created_at = date('Y-m-d H:i:s');
                        $order_items_create->updated_at = date('Y-m-d H:i:s');
                        $order_items_create->save(false);
                        $total_debt += $post['price'][$k] * $post['count_'][$k];
                        $quantity += $post['count_'][$k];
                    }


                    $product_write_out = new Products();
                    $product_write_out->warehouse_id = 1;
                    $product_write_out->nomenclature_id = $post['nom_id'][$k];
                    $product_write_out->document_id = $model->id;
                    $product_write_out->count = -intval($post['count_'][$k]);
                    $product_write_out->price = $post['price'][$k];
                    $product_write_out->type = 2;
                    $product_write_out->created_at = date('Y-m-d H:i:s');
                    $product_write_out->updated_at = date('Y-m-d H:i:s');
                    $product_write_out->save(false);
                }
            }
            $order = Orders::findOne($id);
            $order->total_price = $total_debt;
            $order->total_count = $quantity;
            $order->save(false);
            return $this->redirect(['index', 'id' => $model->id]);
        }
        $sub_page = [];
        $query = Nomenclature::find();
        $countQuery = clone $query;
        $total = $countQuery->count();
        $nomenclatures = $query->select('nomenclature.id,nomenclature.image,nomenclature.name,nomenclature.price,
        nomenclature.cost,products.id as products_id,products.count')
            ->leftJoin('products','nomenclature.id = products.nomenclature_id')
            ->offset(0)
            ->groupBy('nomenclature.id')
            ->limit(10)
            ->asArray()
            ->all();
        $order_items = OrderItems::find()->select('order_items.id,order_items.product_id,order_items.count,(order_items.price_before_discount / order_items.count) as price,
        (order_items.cost / order_items.count) as cost,order_items.discount,order_items.price_before_discount,nomenclature.name, (nomenclature.id) as nom_id')
            ->leftJoin('products','products.id = order_items.product_id')
            ->leftJoin('nomenclature','nomenclature.id = products.nomenclature_id')
            ->where(['order_id' => $id])->asArray()->all();
        $clients = Clients::find()->select('id, name')->asArray()->all();
        $clients = ArrayHelper::map($clients,'id','name');
        $users = Users::find()->select('id, name')->asArray()->all();
        $users = ArrayHelper::map($users,'id','name');
        return $this->render('update', [
            'model' => $model,
            'users' => $users,
            'clients' => $clients,
            'nomenclatures' => $nomenclatures,
            'order_items' => $order_items,
            'total' => $total,
            'sub_page' => $sub_page
        ]);
    }

    /**
     * Deletes an existing Orders model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $have_access = Users::checkPremission(23);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $orders = Orders::findOne($id);
        $orders->status = '0';
        $orders->save();
        return $this->redirect(['index']);
    }
    public function actionDelivered($id)
    {
//        $have_access = Users::checkPremission(55);
//        if(!$have_access){
//            $this->redirect('/site/403');
//        }
        $orders = Orders::findOne($id);
        $orders->status = '2';
        $orders->save();
        return $this->redirect(['index']);
    }
    public  function actionFilterStatus(){
        if ($_GET){
            $searchModel = new OrdersSearch();
            $dataProvider = $searchModel->search($this->request->queryParams);
            $sub_page = [];
            $approved = null;
            if ($_GET['numberVal'] == 2){
                $approved = 2;
            }elseif ($_GET['numberVal'] == 0){
                $approved = 0;
            }elseif ($_GET['numberVal'] == 3){
                $approved = 3;
            }elseif ($_GET['numberVal'] == 1){
                $approved = 1;
            }elseif ($_GET['numberVal'] == 4){
                $approved = 4;
            }
            var_dump($approved);

            return $this->renderAjax('widget', [
                'sub_page' => $sub_page,
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'approved' => $approved
            ]);
        }
    }

    public function actionDeleteItems(){
        if ($this->request->isPost){
            $total_count = $this->request->post('totalCount');
            $total_price = $this->request->post('totalPrice');
            $item_id = intval($this->request->post('itemId'));
            $nom_id = intval($this->request->post('nomId'));
            $orders_id = OrderItems::find()->select('order_id')->where(['id' => $item_id])->one();
            $delete_items = OrderItems::findOne($item_id)->delete();
            $delete_products = Products::findOne([
                'document_id' => $orders_id->order_id,
                'nomenclature_id' => $nom_id,
                'type' => 2
            ])->delete();
            $update_orders = Orders::findOne($orders_id->order_id);
            $update_orders->total_count = $total_count;
            $update_orders->total_price = $total_price;
            $update_orders->save(false);
            if(isset($delete_items) && isset($delete_products)){
                return json_encode(true);
            }else{
                return json_encode(false);
            }
        }
    }


    /**
     * Finds the Orders model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Orders the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Orders::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    public function actionReports($id){
        $have_access = Users::checkPremission(22);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $model = $this->findModel($id);
        $sub_page = [];
        $query = Nomenclature::find();
        $countQuery = clone $query;
        $total = $countQuery->count();
        $nomenclatures = $query->select('nomenclature.id,nomenclature.image,nomenclature.name,nomenclature.price,
        nomenclature.cost,products.id as products_id,products.count')
            ->leftJoin('products','nomenclature.id = products.nomenclature_id')
            ->offset(0)
            ->groupBy('nomenclature.id')
            ->limit(10)
            ->asArray()
            ->all();
        $order_items = OrderItems::find()->select('order_items.id,order_items.product_id,order_items.count,(order_items.price / order_items.count) as price,
        (order_items.cost / order_items.count) as cost,order_items.discount,order_items.price_before_discount,nomenclature.name, (nomenclature.id) as nom_id')
            ->leftJoin('products','products.id = order_items.product_id')
            ->leftJoin('nomenclature','nomenclature.id = products.nomenclature_id')
            ->where(['order_id' => $id])->asArray()->all();
        $clients = Clients::find()->select('id, name')->asArray()->all();
        $clients = ArrayHelper::map($clients,'id','name');
        $users = Users::find()->select('id, name')->asArray()->all();
        $users = ArrayHelper::map($users,'id','name');
        return $this->renderAjax('report', [
            'model' => $model,
            'users' => $users,
            'clients' => $clients,
            'nomenclatures' => $nomenclatures,
            'order_items' => $order_items,
            'total' => $total,
            'sub_page' => $sub_page
        ]);
    }
}
