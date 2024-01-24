<?php

namespace app\controllers;

use app\models\Clients;
use app\models\Discount;
use app\models\DiscountClients;
use app\models\DiscountProducts;
use app\models\Log;
use app\models\Nomenclature;
use app\models\OrderItems;
use app\models\Orders;
use app\models\OrdersSearch;
use app\models\Premissions;
use app\models\Products;
use app\models\Users;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\Pagination;
use function React\Promise\all;

/**
 * OrdersController implements the CRUD actions for Orders model.
 */
class OrdersController extends Controller
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
        $date_tab = [];

        $searchModel = new OrdersSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        if($_POST){
            return $this->renderAjax('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'data_size' => 'max',
                'sub_page' => $sub_page,
                'date_tab' => $date_tab,

            ]);
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,

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
     * Creates a new Orders model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionGetDiscount(){
        if ($this->request->isPost) {
            $client_id = intval($this->request->post('clientId'));
            $product_id = intval($this->request->post('product_id'));
            $nomenclature_id = intval($this->request->post('nomenclature_id'));
            $name = $this->request->post('name');
            $orders_date = $this->request->post('orders_date');
            $orders_count = intval($this->request->post('count'));
            $orders_price = intval($this->request->post('price'));
            $orders_cost = intval($this->request->post('cost'));
            $orders_total_sum = intval($this->request->post('totalSum'));
            $orders_total_count = intval($this->request->post('countSum'));
            $discount = Products::getDiscount([
                'client_id' => $client_id,
                'prod_id' => $product_id,
                'nom_id' => $nomenclature_id,
                'name' => $name,
                'orders_date' => $orders_date,
                'orders_count' => $orders_count,
                'orders_price' => $orders_price,
                'orders_cost' => $orders_cost,
                'orders_total_sum' => $orders_total_sum,
                'orders_total_count' => $orders_total_count
            ]);
            return $discount;
//            echo "<pre>";
//            var_dump($discount);
        }
    }
    public function actionCreate()
    {
        $have_access = Users::checkPremission(21);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $model = new Orders();
        $url = Url::to('', 'http');
        $url = str_replace('create', 'view', $url);
        $premission = Premissions::find()
            ->select('name')
            ->where(['id' => 21])
            ->asArray()
            ->one();
        if ($this->request->isPost) {
            date_default_timezone_set('Asia/Yerevan');
            $post = $this->request->post();
            $model->user_id = $post['Orders']['user_id'];
            $model->clients_id = $post['clients_id'];
            $model->total_price = $post['Orders']['total_price'];
            $model->total_price_before_discount = $post['Orders']['total_price_before_discount'];
            $model->total_discount = $post['Orders']['total_discount'];
            $model->total_count = $post['Orders']['total_count'];
            $model->comment = $post['Orders']['comment'];
            $model->orders_date = $post['Orders']['orders_date'];
            $model->created_at = date('Y-m-d H:i:s');
            $model->updated_at = date('Y-m-d H:i:s');
            $model->save();
            for ($i = 0; $i < count($post['order_items']); $i++){
                $order_items_create = new OrderItems();
                $order_items_create->order_id = $model->id;
                $order_items_create->product_id = intval($post['order_items'][$i]);
                $order_items_create->nom_id_for_name = intval($post['nom_id'][$i]);
                $order_items_create->price = intval($post['price'][$i]) * intval($post['count_'][$i]);
                $order_items_create->count = $post['count_'][$i];
                $order_items_create->cost = intval($post['cost'][$i]) * intval($post['count_'][$i]);
                $order_items_create->discount = intval($post['discount'][$i]) * intval($post['count_'][$i]);
                $order_items_create->price_before_discount = intval($post['beforePrice'][$i]) * intval($post['count_'][$i]);
                $order_items_create->count_discount_id = $post['count_discount_id'][$i];
                $order_items_create->created_at = date('Y-m-d H:i:s');
                $order_items_create->updated_at = date('Y-m-d H:i:s');
                $order_items_create->save(false);

                $product_write_out = new Products();
                $product_write_out->warehouse_id = 1;
                $product_write_out->nomenclature_id = intval($post['nom_id'][$i]);
                $product_write_out->document_id = $model->id;
                $product_write_out->type = 2;
                $product_write_out->count = -intval($post['count_'][$i]);
                $product_write_out->price = intval($post['price'][$i]);
                $product_write_out->created_at = date('Y-m-d H:i:s');
                $product_write_out->updated_at = date('Y-m-d H:i:s');
                $product_write_out->save(false);
            }


//            $orders_total_debt = Orders::findOne($model->id);
//            $orders_total_debt->total_price = $total_debt;
//            $orders_total_debt->save(false);
            $model = Orders::getDefVals($model);
            Log::afterSaves('Create', $model, '', $url.'?'.'id'.'='.$model->id, $premission);
                return $this->redirect(['index', 'id' => $model->id]);
        } else {
            $model->loadDefaultValues();
        }
        $sub_page = [];
        $date_tab = [];

        $query = Products::find();
        $countQuery = clone $query;
        $total = $countQuery->where(['and',['products.status' => 1,'products.type' => 1]])->groupBy('products.nomenclature_id')->count();
        $nomenclatures = $query->select('products.id,nomenclature.id as nomenclature_id,
        nomenclature.image,nomenclature.name,nomenclature.cost,products.count,products.price')
            ->leftJoin('nomenclature','nomenclature.id = products.nomenclature_id')
            ->where(['and',['products.status' => 1,'nomenclature.status' => 1,'products.type' => 1]])
            ->offset(0)
            ->groupBy('products.nomenclature_id')
            ->orderBy(['products.created_at' => SORT_DESC])
            ->limit(10)
            ->asArray()
            ->all();
        $clients = Clients::find()->select('id, name')->where(['=','status',1])->asArray()->all();
        $session = Yii::$app->session;
        if($session['role_id'] == 1){
            $users = Users::find()->select('id, name')->where(['=','role_id',2])->andWhere(['=','status',1])->asArray()->all();
            $users = ArrayHelper::map($users,'id','name');
        }elseif ($session['role_id'] == 2){
            $users = Users::find()->select('id, name')->where(['=','id',$session['user_id']])->andWhere(['=','status',1])->asArray()->all();
            $users = ArrayHelper::map($users,'id','name');
        }
        return $this->render('create', [
            'model' => $model,
            'users' => $users,
            'clients' => $clients,
            'nomenclatures' => $nomenclatures,
            'total' => $total,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,

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
        $urlId = intval($_POST['urlId']);
        $search_name = $_GET['nomenclature'] ?? false;
        $pageSize = 10;
        $offset = ($page-1) * $pageSize;
        $query = Products::find();
        $countQuery = clone $query;
        $orders_items_update = OrderItems::find()
            ->select('order_items.*,nomenclature.id,nomenclature.image,nomenclature.name,nomenclature.cost')
            ->leftJoin('nomenclature','order_items.nom_id_for_name = nomenclature.id')
            ->where(['order_items.order_id' => $urlId])
            ->asArray()
            ->all();
        $nomenclatures = $query->where(['not in', 'nomenclature.id', array_column($orders_items_update, 'nom_id_for_name')])
            ->select('products.id, products.count, products.price, nomenclature.id as nomenclature_id, nomenclature.image, nomenclature.name, nomenclature.cost')
            ->leftJoin('nomenclature', 'nomenclature.id = products.nomenclature_id')
            ->andWhere(['and', ['products.status' => 1, 'nomenclature.status' => 1, 'products.type' => 1]])
            ->groupBy('products.nomenclature_id')
            ->orderBy(['products.created_at' => SORT_DESC]);
            if ($search_name){
                $nomenclatures->andWhere(['like', 'nomenclature.name', $search_name])
                    ->offset(0);
            }else{
                $nomenclatures->offset($offset)
                    ->limit($pageSize);
            }
        $product_count =
            $countQuery
                ->where(['not in', 'nomenclature.id', array_column($orders_items_update, 'nom_id_for_name')])
                ->leftJoin('nomenclature', 'nomenclature.id = products.nomenclature_id')
                ->andWhere(['and', ['products.status' => 1, 'nomenclature.status' => 1, 'products.type' => 1]])
                ->groupBy('products.nomenclature_id')
                ->orderBy(['products.created_at' => SORT_DESC])
                ->asArray()
                ->all();
        $total = count($product_count);
        $nomenclatures = $nomenclatures
            ->asArray()
            ->all();
        $id_count = $_POST['id_count'] ?? [];
        return $this->renderAjax('get-nom', [
            'nomenclatures' => $nomenclatures,
            'id_count' => $id_count ,
            'total' => $total,
            'search_name' => $search_name,
            'urlId' => $urlId,
        ]);
    }
    public function actionUpdate($id)
    {
//        echo "<pre>";
        $have_access = Users::checkPremission(22);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        if ($this->findModel($id)->status == 0) {
            return $this->redirect(Yii::$app->request->referrer);
        }
        $model = $this->findModel($id);
        $url = Url::to('', 'http');
        $oldattributes = Orders::find()
            ->select('*')
            ->where(['id' => $id])
            ->asArray()
            ->one();
        $premission = Premissions::find()
            ->select('name')
            ->where(['id' => 22])
            ->asArray()
            ->one();
        if ($this->request->isPost) {
            date_default_timezone_set('Asia/Yerevan');
            $post = $this->request->post();
            $model->user_id = $post['Orders']['user_id'];
            $model->clients_id = $post['clients_id'];
            $model->total_price_before_discount = $post['Orders']['total_price_before_discount'];
            $model->total_price = $post['Orders']['total_price'];
            $model->total_discount = $post['Orders']['total_discount'];
            $model->total_count = $post['Orders']['total_count'];
            $model->comment = $post['Orders']['comment'];
            $model->orders_date = $post['Orders']['orders_date'];
            $model->updated_at = date('Y-m-d H:i:s');
            $model->save();
            $items = $post['order_items'];
            $quantity = 0;
            $total_price = 0;
            $total_price_before_discount = 0;
            $total_discount = 0;
//            echo "<pre>";
//            var_dump($post);
//            die;
            foreach ($items as $k => $item){
                $order_item = OrderItems::findOne(intval($item));
                if($order_item !== null){
                    $order_item->order_id = $id;
                    $order_item->product_id = $post['product_id'][$k];
                    $order_item->nom_id_for_name = intval($post['nom_id'][$k]);
                    $order_item->price = intval($post['price'][$k]) * intval($post['count_'][$k]);
                    $order_item->cost = intval($post['cost'][$k]) * intval($post['count_'][$k]);
                    $order_item->discount = intval($post['discount'][$k]) * intval($post['count_'][$k]);
                    $order_item->price_before_discount = intval($post['beforePrice'][$k]) * intval($post['count_'][$k]);
                    $order_item->count = intval($post['count_'][$k]);
                    $order_item->updated_at = date('Y-m-d H:i:s');
                    $order_item->save(false);

                    $quantity += intval($post['count_'][$k]);
                    $total_price += intval($post['price'][$k]) * intval($post['count_'][$k]);
                    $total_price_before_discount += intval($post['beforePrice'][$k]) * intval($post['count_'][$k]);
                    $total_discount += intval($post['discount'][$k]) * intval($post['count_'][$k]);

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
                    $order_items_create = new OrderItems();
                    $order_items_create->order_id = $model->id;
                    $order_items_create->product_id = intval($post['product_id'][$k]);
                    $order_items_create->nom_id_for_name = intval($post['nom_id'][$k]);
                    $order_items_create->price = intval($post['price'][$k]) * intval($post['count_'][$k]);
                    $order_items_create->count = $post['count_'][$k];
                    $order_items_create->cost = intval($post['cost'][$k]) * intval($post['count_'][$k]);
                    $order_items_create->discount = intval($post['discount'][$k]) * intval($post['count_'][$k]);
                    $order_items_create->price_before_discount = intval($post['beforePrice'][$k]) * intval($post['count_'][$k]);
                    $order_items_create->count_discount_id = $post['count_discount_id'][$k];
                    $order_items_create->created_at = date('Y-m-d H:i:s');
                    $order_items_create->updated_at = date('Y-m-d H:i:s');
                    $order_items_create->save(false);

                    $quantity += intval($post['count_'][$k]);
                    $total_price += intval($post['price'][$k]) * intval($post['count_'][$k]);
                    $total_price_before_discount += intval($post['beforePrice'][$k]) * intval($post['count_'][$k]);
                    $total_discount += intval($post['discount'][$k]) * intval($post['count_'][$k]);

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
            $order->total_price_before_discount = $total_price_before_discount;
            $order->total_discount = $total_discount;
            $order->total_price = $total_price;
            $order->total_count = $quantity;
            $order->save(false);
            Log::afterSaves('Update', $model, $oldattributes, $url, $premission);
            return $this->redirect(['index', 'id' => $model->id]);
        }
        $sub_page = [];
        $date_tab = [];

        $query = Products::find();
        $countQuery = clone $query;
        $orders_items_update = OrderItems::find()
            ->select('order_items.*,nomenclature.id,nomenclature.image,nomenclature.name,nomenclature.cost')
            ->leftJoin('nomenclature','order_items.nom_id_for_name = nomenclature.id')
            ->where(['order_items.order_id' => $id])
            ->asArray()
            ->all();
        $nomenclatures = $query->where(['not in', 'nomenclature.id', array_column($orders_items_update, 'nom_id_for_name')])
            ->select('products.id, products.count, products.price, nomenclature.id as nom_id, nomenclature.image, nomenclature.name, nomenclature.cost')
            ->leftJoin('nomenclature', 'nomenclature.id = products.nomenclature_id')
            ->andWhere(['and', ['products.status' => 1, 'nomenclature.status' => 1, 'products.type' => 1]])
            ->offset(0)
            ->limit(10)
            ->groupBy('products.nomenclature_id')
            ->orderBy(['products.created_at' => SORT_DESC])
            ->asArray()
            ->all();

        $product_count =
            $countQuery
                ->where(['not in', 'nomenclature.id', array_column($orders_items_update, 'nom_id_for_name')])
            ->leftJoin('nomenclature', 'nomenclature.id = products.nomenclature_id')
            ->andWhere(['and', ['products.status' => 1, 'nomenclature.status' => 1, 'products.type' => 1]])
            ->groupBy('products.nomenclature_id')
            ->orderBy(['products.created_at' => SORT_DESC])
            ->asArray()
            ->all();
        $total = count($product_count);
        $order_items = OrderItems::find()->select('order_items.id,order_items.product_id,order_items.count,(order_items.price_before_discount / order_items.count) as beforePrice,
        order_items.price_before_discount as totalBeforePrice,(order_items.cost / order_items.count) as cost,order_items.discount,
        order_items.price as total_price,(order_items.price / order_items.count) as price,nomenclature.name, (nomenclature.id) as nom_id,count_discount_id')
            ->leftJoin('products','products.id = order_items.product_id')
            ->leftJoin('nomenclature','nomenclature.id = products.nomenclature_id')
            ->where(['order_id' => $id])
            ->asArray()
            ->all();
        $order_items_discount = OrderItems::find()->select('count_discount_id')->where(['=','order_id', $id])->asArray()->all();

        $uniqueValues = [];
        foreach ($order_items_discount as $item) {
            $ids = explode(',', $item["count_discount_id"]);
            $uniqueValues = array_merge($uniqueValues, $ids);
        }
        $uniqueValues = array_unique($uniqueValues);

        $numericArray = array_map(function ($value) {
            return is_numeric($value) ? intval($value) : $value;
        }, $uniqueValues);

        $numericValuesOnly = array_filter($numericArray, 'is_numeric');

        $active_discount = Discount::find()->select('id,name,discount,type')->asArray()->all();

        $clients = Clients::find()->select('id, name')->Where(['=','status',1])->asArray()->all();
        $orders_clients = Orders::find()->select('clients_id')->where(['=','id',$id])->asArray()->all();
        $orders_clients = array_column($orders_clients,'clients_id');
        $session = Yii::$app->session;
        if($session['role_id'] == 1){
            $users = Users::find()->select('id, name')->where(['=','role_id',2])->asArray()->all();
            $users = ArrayHelper::map($users,'id','name');
        }elseif ($session['role_id'] == 2){
            $users = Users::find()->select('id, name')->where(['=','id',$session['user_id']])->asArray()->all();
            $users = ArrayHelper::map($users,'id','name');
        }
        return $this->render('update', [
            'model' => $model,
            'users' => $users,
            'numericValuesOnly' => $numericValuesOnly,
            'active_discount' => $active_discount,
            'clients' => $clients,
            'orders_clients' => $orders_clients,
            'nomenclatures' => $nomenclatures,
            'order_items' => $order_items,
            'total' => $total,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,

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
        $oldattributes = Orders::find()
            ->select(['clients.id', 'clients.name'])
            ->leftJoin('clients', 'clients.id = orders.user_id')
            ->where(['orders.id' => $id])
            ->asArray()
            ->one();
        $premission = Premissions::find()
            ->select('name')
            ->where(['id' => 23])
            ->asArray()
            ->one();
        $orders = Orders::findOne($id);
        $orders->status = '0';
        $orders->save();
        Log::afterSaves('Delete', '', $oldattributes['name'], '#', $premission);
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
            $date_tab = [];

            $approved = null;
            if ($_GET['numberVal'] == 2){
                $approved = 2;
            }elseif ($_GET['numberVal'] == 0){
                $approved = 0;
            }elseif ($_GET['numberVal'] == 3 || $_GET['numberVal'] == 4){
                $approved = 3;
            }elseif ($_GET['numberVal'] == 1){
                $approved = 1;
            }

            return $this->renderAjax('widget', [
                'sub_page' => $sub_page,
                'date_tab' => $date_tab,

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
            $total_price_before_discount = $this->request->post('totalPriceBeforeDiscount');
            $total_discount = $this->request->post('totalDiscount');
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
            $update_orders->total_price_before_discount = $total_price_before_discount;
            $update_orders->total_discount = $total_discount;
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
        $date_tab = [];

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


        $order_items = OrderItems::find()->select('order_items.id,order_items.product_id,
        order_items.count,(order_items.price / order_items.count) as price,
        (order_items.cost / order_items.count) as cost,order_items.discount,order_items.price_before_discount,
        nomenclature.name, (nomenclature.id) as nom_id')
            ->leftJoin('products','products.id = order_items.product_id')
            ->leftJoin('nomenclature','nomenclature.id = products.nomenclature_id')
            ->where(['order_id' => $id])
            ->asArray()
            ->all();
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
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,

        ]);
    }
}
