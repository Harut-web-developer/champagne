<?php

namespace app\controllers;

use app\models\Clients;
use app\models\Discount;
use app\models\DiscountClients;
use app\models\DiscountProducts;
use app\models\DocumentItems;
use app\models\Documents;
use app\models\Log;
use app\models\ManagerDeliverCondition;
use app\models\Nomenclature;
use app\models\Notifications;
use app\models\OrderItems;
use app\models\Orders;
use app\models\OrdersSearch;
use app\models\Premissions;
use app\models\Products;
use app\models\Users;
use app\models\Warehouse;
use Couchbase\Document;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\Pagination;
use yii\web\View;
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
        $session = Yii::$app->session;
        $user_id = $session['user_id'];
        $manager_route_id = ManagerDeliverCondition::find()
            ->select('route_id, deliver_id')
            ->where(['manager_id' => $user_id])
            ->andWhere(['status' => '1'])
            ->asArray()
            ->all();
        $clients = Clients::find()
            ->select('id, name')
            ->where(['=','status',1]);
            if ($session['role_id'] == 2) {
                $clients->andWhere(['in', 'route_id', $manager_route_id]);
            }
        $clients =  $clients->asArray()->all();
        $searchModel = new OrdersSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,
            'clients' => $clients,
        ]);
    }

    /**
     * Displays a single Orders model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionPrintDoc($id){
        $order_items = OrderItems::find()->select('products.AAH,products.count_balance,order_items.id,order_items.product_id,order_items.count,(order_items.price_before_discount / order_items.count) as beforePrice,
        order_items.price_before_discount as totalBeforePrice,(order_items.cost / order_items.count) as cost,order_items.discount,
        order_items.price as total_price,(order_items.price / order_items.count) as price,nomenclature.name, (nomenclature.id) as nom_id,count_discount_id')
            ->leftJoin('products','products.id = order_items.product_id')
            ->leftJoin('nomenclature','nomenclature.id = products.nomenclature_id')
            ->where(['order_id' => $id])
            ->asArray()
            ->all();
        return $this->renderAjax('get-update-trs', [
            'order_items' => $order_items,
        ]);

    }
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
            $warehouse_id = $this->request->post('warehouse_id');
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
                'warehouse_id' => $warehouse_id,
                'orders_date' => $orders_date,
                'orders_count' => $orders_count,
                'orders_price' => $orders_price,
                'orders_cost' => $orders_cost,
                'orders_total_sum' => $orders_total_sum,
                'orders_total_count' => $orders_total_count
            ]);
            return $discount;
//            var_dump($discount);

        }
    }
//    public function actionChangeManager(){
//        if ($this->request->isGet){
//            $searchModel = new OrdersSearch();
//            $dataProvider = $searchModel->search($this->request->queryParams);
////            var_dump($dataProvider);
////            if ($this->request->get('managerId') == 'null'){
////                $manager_id = $this->request->get('managerId');
////            }else{
////                $manager_id = intval($this->request->get('managerId'));
////            }
////            var_dump($manager_id);
////            OrdersSearch::getManagerForOrders($manager_id);
//        }
//    }
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
        $session = Yii::$app->session;
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
            for ($i = 0; $i < count($post['order_items']); $i++) {
//                $count_balance = Products::findOne($post['order_items'][$i]);

                $product_write_out = new Products();
                $product_write_out->warehouse_id = $post['warehouse'];;
                $product_write_out->nomenclature_id = intval($post['nom_id'][$i]);
                $product_write_out->document_id = $model->id;
                $product_write_out->type = 2;
                $product_write_out->count = intval($post['count_'][$i]);
                $product_write_out->count_balance = 0;
//                    $count_balance->count_balance - intval($post['count_'][$i]);
                $product_write_out->price = floatval($post['beforePrice'][$i]);
                $product_write_out->AAH = $post['aah'][$i];
                $product_write_out->parent_id = $post['order_items'][$i];
                $product_write_out->created_at = date('Y-m-d H:i:s');
                $product_write_out->updated_at = date('Y-m-d H:i:s');
                $product_write_out->save(false);

                $order_items_create = new OrderItems();
                $order_items_create->order_id = $model->id;
                $order_items_create->warehouse_id = $post['warehouse'];
                $order_items_create->product_id = $product_write_out->id;
                $order_items_create->nom_id_for_name = intval($post['nom_id'][$i]);
                $order_items_create->price = floatval($post['price'][$i]) * intval($post['count_'][$i]);
                $order_items_create->price_by = floatval($post['price'][$i]) * intval($post['count_'][$i]);
                $order_items_create->count = $post['count_'][$i];
                $order_items_create->count_by = $post['count_'][$i];
                $order_items_create->cost = floatval($post['cost'][$i]) * intval($post['count_'][$i]);
                $order_items_create->cost_by = floatval($post['cost'][$i]) * intval($post['count_'][$i]);
                $order_items_create->discount = floatval($post['discount'][$i]) * intval($post['count_'][$i]);
                $order_items_create->discount_by = floatval($post['discount'][$i]) * intval($post['count_'][$i]);
                $order_items_create->price_before_discount = floatval($post['beforePrice'][$i]) * intval($post['count_'][$i]);
                $order_items_create->price_before_discount_by = floatval($post['beforePrice'][$i]) * intval($post['count_'][$i]);
                $order_items_create->count_discount_id = $post['count_discount_id'][$i];
                $order_items_create->created_at = date('Y-m-d H:i:s');
                $order_items_create->updated_at = date('Y-m-d H:i:s');
                $order_items_create->save(false);
            }
            for ($s = 0; $s < count($post['order_items']);$s++){
                $count_balance = Products::findOne($post['order_items'][$s]);
                $count_balance->count_balance = $post['count_balance'][$s];
                $count_balance->save(false);
            }

            if(isset($post['discount_client_id_check'])) {
                foreach ($post['discount_client_id_check'] as $key => $value) {
                    if ($key != 'empty') {
                        $discount_client_check_id = Discount::findOne($key);
                        $discount_client_check_id->discount_option_check_client_id = $value;
                        $discount_client_check_id->save(false);
                    }
                }
            }
            $model = Orders::getDefVals($model);
            $user_name = Users::find()->select('*')->where(['id' => $post['Orders']['user_id']])->asArray()->one();
            $client_name = Clients::find()->select('name')->where(['id' => $post['clients_id']])->one();
            $text = $user_name['name'] . '(ն/ը) ' . 'ստեղծել է ' . $client_name['name'] . 'ի համար պատվեր։';
            Notifications::createNotifications($premission['name'], $text,'orderscreate');
            Log::afterSaves('Create', $model, '', $url.'?'.'id'.'='.$model->id, $premission);
            return $this->redirect(['index', 'id' => $model->id]);
        } else {
            $model->loadDefaultValues();
        }
        $sub_page = [];
        $date_tab = [];

        if($session['role_id'] == 1){
            $users = Users::find()->select('id, name')->where(['=','role_id',2])->andWhere(['=','status',1])->asArray()->all();
            $users = ArrayHelper::map($users,'id','name');
        }elseif ($session['role_id'] == 2){
            $users = Users::find()->select('id, name')->where(['=','id',$session['user_id']])->andWhere(['=','status',1])->asArray()->all();
            $users = ArrayHelper::map($users,'id','name');
        }

        $user_id = $session['user_id'];
        $manager_route_id = ManagerDeliverCondition::find()
            ->select('route_id, deliver_id');
            if ($session['role_id'] == 2) {
                $manager_route_id->where(['manager_id' => $user_id]);
            }elseif ($session['role_id'] == 1){
                $user_id = key($users); // arajin tarri keyn
                $manager_route_id->where(['manager_id' => $user_id]);
            }
        $manager_route_id = $manager_route_id->andWhere(['status' => '1'])
            ->asArray()
            ->all();

        $clients = Clients::find()
            ->select('id, name')
            ->where(['=','status',1]);
        if ($session['role_id'] == 2) {
            $clients->andWhere(['in', 'route_id', $manager_route_id]);
        }elseif ($session['role_id'] == 1){
            $clients->andWhere(['in', 'route_id', $manager_route_id]);
        }
        $clients =  $clients->asArray()->all();

        $warehouse = Warehouse::find()
            ->select('id, name')
            ->asArray()
            ->all();
        return $this->render('create', [
            'model' => $model,
            'users' => $users,
            'clients' => $clients,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,
            'warehouse' => $warehouse,
        ]);
    }

    public function actionGetManager(){
        $session = Yii::$app->session;
        if($session['role_id'] == 1){
            $users = Users::find()->select('id, name')->where(['=','role_id',2])->andWhere(['=','status',1])->asArray()->all();
            $users = ArrayHelper::map($users,'id','name');
        }elseif ($session['role_id'] == 2){
            $users = Users::find()->select('id, name')->where(['=','id',$session['user_id']])->andWhere(['=','status',1])->asArray()->all();
            $users = ArrayHelper::map($users,'id','name');
        }

        $user_id = $session['user_id'];
        $manager_route_id = ManagerDeliverCondition::find()
            ->select('route_id, deliver_id');
        if ($session['role_id'] == 2) {
            $manager_route_id->where(['manager_id' => $user_id]);
        }elseif ($session['role_id'] == 1){
            $user_id = $_GET['user_id'] ? $_GET['user_id'] : key($users); // arajin tarri keyn
            $manager_route_id->where(['manager_id' => $user_id]);
        }
        $manager_route_id = $manager_route_id->andWhere(['status' => '1'])
            ->asArray()
            ->all();

        $clients = Clients::find()
            ->select('id, name')
            ->where(['=','status',1]);
        if ($session['role_id'] == 2) {
            $clients->andWhere(['in', 'route_id', $manager_route_id]);
        }elseif ($session['role_id'] == 1){
            $clients->andWhere(['in', 'route_id', $manager_route_id]);
        }
        $clients =  $clients->asArray()->all();
        return $this->renderAjax('clients_form', [
            'clients' => $clients,
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
        $_GET['warehouse_id'] = $_GET['warehouse_id'] ?? $_POST['warehouse_id'];
        $_POST['warehouse_id'] = $_POST['warehouse_id'] ?? $_GET['warehouse_id'];
        $warehouse_id = $_POST['warehouse_id'] ?? $_GET['warehouse_id'];
        $search_name = $_GET['nomenclature'] ?? false;
        $pageSize = 10;
        $offset = ($page-1) * $pageSize;
        $query = Products::find();
        $nomenclatures = $query->select('products.id,nomenclature.id as nomenclature_id,
                nomenclature.image,nomenclature.name,nomenclature.cost,products.count,products.price, SUM(count_balance) as all_count_balance')
            ->leftJoin('nomenclature','nomenclature.id = products.nomenclature_id')
            ->where(['and',['products.status' => 1,'nomenclature.status' => 1]])
            ->andWhere(['products.warehouse_id' => intval($warehouse_id)])
            ->andWhere(['or',
                ['products.type' => '1'],
                ['products.type' => '3'],
                ['products.type' => '8']
            ])
            ->groupBy('products.nomenclature_id')
            ->having(['!=', 'SUM(count_balance)', 0]);
        if ($search_name){
            $nomenclatures->andWhere(['like', 'nomenclature.name', $search_name]);
            $offset = 0;
            $pageSize = false;
        }
        $countQuery = clone $query;
        $total = $countQuery->count();
        $nomenclatures = $nomenclatures->offset($offset)
            ->limit($pageSize)
            ->asArray()
            ->all();
        $id_count = $_POST['id_count'] ?? [];
        return $this->renderAjax('get-nom', [
            'nomenclatures' => $nomenclatures,
            'id_count' => $id_count ,
            'total' => $total,
            'search_name' => $search_name,
        ]);
    }
    public function actionGetNomiclatureUpdate(){
        $page = $_GET['paging'] ?? 1;
        $urlId = intval($_POST['urlId']);
        $_GET['warehouse_id'] = $_GET['warehouse_id'] ?? $_POST['warehouse_id'];
        $_POST['warehouse_id'] = $_POST['warehouse_id'] ?? $_GET['warehouse_id'];
        $warehouse_id = $_POST['warehouse_id'] ?? $_GET['warehouse_id'];
        $search_name = $_GET['nomenclature'] ?? false;
        $pageSize = 10;
        $offset = ($page-1) * $pageSize;
        $query = Products::find();
        $countQuery = clone $query;
        $orders_items_update = OrderItems::find()
            ->select('products.AAH, products.count_balance,order_items.*,nomenclature.id,nomenclature.image,nomenclature.name,nomenclature.cost')
            ->leftJoin('nomenclature','order_items.nom_id_for_name = nomenclature.id')
            ->leftJoin('products','products.id = order_items.product_id')
            ->andWhere(['order_items.warehouse_id' => intval($warehouse_id)])
            ->where(['order_items.order_id' => $urlId])
            ->asArray()
            ->all();
        $nomenclatures = $query
//            ->where(['not in', 'nomenclature.id', array_column($orders_items_update, 'nom_id_for_name')])
            ->select('products.id, products.count, products.price,products.warehouse_id, nomenclature.id as nomenclature_id, nomenclature.image, nomenclature.name, nomenclature.cost')
            ->leftJoin('nomenclature', 'nomenclature.id = products.nomenclature_id')
            ->andWhere(['and', ['products.status' => 1, 'nomenclature.status' => 1, 'products.type' => 1]])
            ->andWhere(['products.warehouse_id' => intval($warehouse_id)])
            ->groupBy('products.nomenclature_id');
        if ($search_name){
            $nomenclatures->andWhere(['like', 'nomenclature.name', $search_name]);
            $offset = 0;
            $pageSize = false;
        }
        $product_count =
            $countQuery
                ->where(['not in', 'nomenclature.id', array_column($orders_items_update, 'nom_id_for_name')])
                ->leftJoin('nomenclature', 'nomenclature.id = products.nomenclature_id')
                ->andWhere(['and', ['products.status' => 1, 'nomenclature.status' => 1, 'products.type' => 1]])
                ->andWhere(['products.warehouse_id' => intval($warehouse_id)])
                ->groupBy('products.nomenclature_id')
                ->asArray()
                ->all();
        $total = count($product_count);
        $nomenclatures = $nomenclatures->offset($offset)
            ->limit($pageSize)
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
        $warehouse_value_update = OrderItems::find()->select('warehouse_id')->where(['order_id' => $id])->one();
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
            foreach ($items as $k => $item){
                $order_item = OrderItems::findOne(intval($item));
                if($order_item !== null){


//                    $order_item->order_id = $id;
//                    $order_item->warehouse_id = intval($post['warehouse']);
//                    $order_item->product_id = $post['product_id'][$k];
//                    $order_item->nom_id_for_name = intval($post['nom_id'][$k]);
//                    if ($model->is_exit == 1){
//                        $order_item->price = floatval($post['price'][$k]) * intval($post['count_'][$k]);
//                        $order_item->price_by = floatval($post['price'][$k]) * intval($post['count_'][$k]);
//                        $order_item->cost = floatval($post['cost'][$k]) * intval($post['count_'][$k]);
//                        $order_item->cost_by = floatval($post['cost'][$k]) * intval($post['count_'][$k]);
//                        $order_item->discount = floatval($post['discount'][$k]) * intval($post['count_'][$k]);
//                        $order_item->discount_by = floatval($post['discount'][$k]) * intval($post['count_'][$k]);
//                        $order_item->price_before_discount = floatval($post['beforePrice'][$k]) * intval($post['count_'][$k]);
//                        $order_item->price_before_discount_by = floatval($post['beforePrice'][$k]) * intval($post['count_'][$k]);
//                        $order_item->count = intval($post['count_'][$k]);
//                        $order_item->count_by = intval($post['count_'][$k]);
//                    }else{
//                        $order_item->price_by = floatval($post['price'][$k]) * intval($post['count_'][$k]);
//                        $order_item->cost_by = floatval($post['cost'][$k]) * intval($post['count_'][$k]);
//                        $order_item->discount_by = floatval($post['discount'][$k]) * intval($post['count_'][$k]);
//                        $order_item->price_before_discount_by = floatval($post['beforePrice'][$k]) * intval($post['count_'][$k]);
//                        $order_item->count_by = intval($post['count_'][$k]);
//                    }
//                    $order_item->updated_at = date('Y-m-d H:i:s');
//                    $order_item->save(false);

                    $quantity += intval($post['count_'][$k]);
                    $total_price += floatval($post['price'][$k]) * intval($post['count_'][$k]);
                    $total_price_before_discount += floatval($post['beforePrice'][$k]) * intval($post['count_'][$k]);
                    $total_discount += floatval($post['discount'][$k]) * intval($post['count_'][$k]);

//                    $product_write_out = Products::find()->select('products.*')
//                        ->where(['and',['document_id' => $model->id,'type' => 2,'nomenclature_id' => $post['nom_id'][$k]]])->one();
//                    $product_write_out->warehouse_id = intval($post['warehouse']);
//                    $product_write_out->nomenclature_id = $post['nom_id'][$k];
//                    $product_write_out->price = floatval($post['price'][$k]);
//                    $product_write_out->count = -intval($post['count_'][$k]);
//                    $product_write_out->type = 2;
//                    $product_write_out->updated_at = date('Y-m-d H:i:s');
//                    $product_write_out->save();
                } else {
                    $product_write_out = new Products();
                    $product_write_out->warehouse_id = $post['warehouse'];;
                    $product_write_out->nomenclature_id = intval($post['nom_id'][$k]);
                    $product_write_out->document_id = $model->id;
                    $product_write_out->type = 2;
                    $product_write_out->count = intval($post['count_'][$k]);
                    $product_write_out->count_balance = 0;
//                    $count_balance->count_balance - intval($post['count_'][$k]);
                    $product_write_out->price = floatval($post['beforePrice'][$k]);
                    $product_write_out->AAH = $post['aah'][$k];
                    $product_write_out->parent_id = $post['product_id'][$k];
                    $product_write_out->created_at = date('Y-m-d H:i:s');
                    $product_write_out->updated_at = date('Y-m-d H:i:s');
                    $product_write_out->save(false);



                    $order_items_create = new OrderItems();
                    $order_items_create->order_id = $model->id;
                    $order_items_create->warehouse_id = intval($post['warehouse']);
                    $order_items_create->product_id = $product_write_out->id;
                    $order_items_create->nom_id_for_name = intval($post['nom_id'][$k]);
                    $order_items_create->price = floatval($post['price'][$k]) * intval($post['count_'][$k]);
                    $order_items_create->price_by = floatval($post['price'][$k]) * intval($post['count_'][$k]);
                    $order_items_create->count = intval($post['count_'][$k]);
                    $order_items_create->count_by = intval($post['count_'][$k]);
                    $order_items_create->cost = floatval($post['cost'][$k]) * intval($post['count_'][$k]);
                    $order_items_create->cost_by = floatval($post['cost'][$k]) * intval($post['count_'][$k]);
                    $order_items_create->discount = floatval($post['discount'][$k]) * intval($post['count_'][$k]);
                    $order_items_create->discount_by = floatval($post['discount'][$k]) * intval($post['count_'][$k]);
                    $order_items_create->price_before_discount = floatval($post['beforePrice'][$k]) * intval($post['count_'][$k]);
                    $order_items_create->price_before_discount_by = floatval($post['beforePrice'][$k]) * intval($post['count_'][$k]);
                    $order_items_create->count_discount_id = $post['count_discount_id'][$k];
                    $order_items_create->created_at = date('Y-m-d H:i:s');
                    $order_items_create->updated_at = date('Y-m-d H:i:s');
                    $order_items_create->save(false);

                    $quantity += intval($post['count_'][$k]);
                    $total_price += floatval($post['price'][$k]) * intval($post['count_'][$k]);
                    $total_price_before_discount += floatval($post['beforePrice'][$k]) * intval($post['count_'][$k]);
                    $total_discount += floatval($post['discount'][$k]) * intval($post['count_'][$k]);

                    $count_balance = Products::findOne($post['product_id'][$k]);
                    $count_balance->count_balance = $post['count_balance'][$k];
                    $count_balance->save(false);

                }
            }
            $order = Orders::findOne($id);
            $order->total_price_before_discount = $total_price_before_discount;
            $order->total_discount = $total_discount;
            $order->total_price = $total_price;
            $order->total_count = $quantity;
            $order->save(false);
            if(isset($post['discount_client_id_check'])){
                foreach ($post['discount_client_id_check'] as $key => $value){
                    if ($key != 'empty'){
                        $discount_client_check_id = Discount::findOne($key);
                        $discount_client_check_id->discount_option_check_client_id = $value;
                        $discount_client_check_id->save(false);
                    }
                }
            }
            $user_name = Users::find()->select('*')->where(['id' => $post['Orders']['user_id']])->asArray()->one();
            $client_name = Clients::find()->select('name')->where(['id' => $post['clients_id']])->one();
            $text = $user_name['name'] . '(ն/ը) ' . 'թարմացրել է ' . $client_name['name'] . 'ի համար պատվերը։';
            Notifications::createNotifications($premission['name'], $text,'ordersupdate');
            Log::afterSaves('Update', $model, $oldattributes, $url, $premission);
            return $this->redirect(['index', 'id' => $model->id]);
        }
        $sub_page = [];
        $date_tab = [];

        $order_items = OrderItems::find()->select('products.AAH,products.count_balance,order_items.count_by,order_items.id,order_items.product_id,order_items.count,(order_items.price_before_discount / order_items.count) as beforePrice,
        ((order_items.price_before_discount / order_items.count) * order_items.count_by) as totalBeforePrice,(order_items.cost / order_items.count) as cost,order_items.discount,
        ((order_items.price / order_items.count) * order_items.count_by)  as total_price,(order_items.price / order_items.count) as price,nomenclature.name, (nomenclature.id) as nom_id,count_discount_id')
            ->leftJoin('products','products.id = order_items.product_id')
//            ->leftJoin('products as prod_parent','prod_parent.id = products.parent_id')
            ->leftJoin('nomenclature','nomenclature.id = products.nomenclature_id')
            ->where(['order_id' => $id])
            ->andWhere(['order_items.status' => '1'])
            ->asArray()
            ->all();
//        echo "<pre>";
//        var_dump($order_items);
//        exit();
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
        $session = Yii::$app->session;
        $user_id = $session['user_id'];
        $manager_route_id = ManagerDeliverCondition::find()
            ->select('route_id, deliver_id')
            ->where(['manager_id' => $user_id])
            ->andWhere(['status' => '1'])
            ->asArray()
            ->all();
        $clients = Clients::find()
            ->select('id, name')
            ->where(['=','status',1]);
        if ($session['role_id'] == 2) {
            $clients->andWhere(['in', 'route_id', $manager_route_id]);
        }
        $clients =  $clients->asArray()->all();
        $orders_clients = Orders::find()->select('clients_id')->where(['=','id',$id])->asArray()->all();
        $orders_clients = array_column($orders_clients,'clients_id');
        $session = Yii::$app->session;
        if($session['role_id'] == 1){
            $users = Users::find()->select('id, name')->where(['=','role_id',2])->asArray()->all();
            $users = ArrayHelper::map($users,'id','name');
        }elseif ($session['role_id'] == 2){
            $users = Users::find()->select('id, name')->where(['=','id',$session['user_id']])->asArray()->all();
            $users = ArrayHelper::map($users,'id','name');
        }elseif($session['role_id'] == 3 || $session['role_id'] == 4){
            $users = Users::find()->select('id, name')->where(['id' => $session['user_id']])->asArray()->all();
            $users = ArrayHelper::map($users,'id','name');

        }
        $warehouse = Warehouse::find()
            ->select('id, name')
            ->asArray()
            ->all();
        return $this->render('update', [
            'model' => $model,
            'users' => $users,
            'numericValuesOnly' => $numericValuesOnly,
            'active_discount' => $active_discount,
            'clients' => $clients,
            'orders_clients' => $orders_clients,
            'oldattributes' => $oldattributes,
            'order_items' => $order_items,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,
            'warehouse' => $warehouse,
            'warehouse_value_update' => $warehouse_value_update,
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
//        echo "<pre>";
        $orders = Orders::findOne($id);
        $orders_items_refuse = OrderItems::findOne(['order_id' => $orders->id]);
        $keeper = Users::findOne(['warehouse_id' => $orders_items_refuse->warehouse_id]);
        $order_items = OrderItems::find()->where(['order_id' => $orders->id])->andWhere(['status' => '1'])->all();

        if ($orders->is_exit == 1){
            $orders->status = '0';
            $orders->save(false);
            foreach ($order_items as $item){
                $item->status = '0';
                $item->save(false);
                $product = Products::findOne($item->product_id);
                $product->status = '0';
                $product->save(false);
            }
        }else{
//            var_dump($order_items);
//            exit();
                $orders->status = '0';
                $orders->save(false);

                $document = new Documents();
                $document->user_id = $keeper->id;
                $document->orders_id = $orders_items_refuse->order_id;
                $document->warehouse_id = $orders_items_refuse->warehouse_id;
                $document->rate_id = 1;
                $document->rate_value = 1;
                $document->document_type = 6;
                $document->comment = 'Վերադարձրած ապրանք(ներ)';
                $document->date = date('Y-m-d H:i:s');
                $document->status = '1';
                $document->created_at = date('Y-m-d H:i:s');
                $document->updated_at = date('Y-m-d H:i:s');
                $document->save(false);

                foreach ($order_items as $item){
                    if ($item->count - $item->count_by == 0){

                        $product = Products::findOne($item->product_id);
                        $product->status = '0';
                        $product->save(false);

                        $new_document_items = new DocumentItems();
                        $new_document_items->document_id = $document->id;
                        $new_document_items->nomenclature_id = $item->nom_id_for_name;
                        $new_document_items->count = $item->count_by;
                        $new_document_items->refuse_product_id = $product->id;
                        if ($product->AAH == 1){
                            $new_document_items->AAH = 'true';
                            $new_document_items->price_with_aah = $product->price;
                            $new_document_items->price = number_format((($product->price * 5)/6),2,'.','');
                        }else{
                            $new_document_items->AAH = 'false';
                            $new_document_items->price = $product->price;
                            $new_document_items->price_with_aah = $product->price + ($product->price * 20) / 100;
                        }
                        $new_document_items->status = '1';
                        $new_document_items->created_at = date('Y-m-d H:i:s');
                        $new_document_items->updated_at = date('Y-m-d H:i:s');
                        $new_document_items->save(false);
                        $item->status = '0';
                        $item->save(false);
                    }else{

                        $product = Products::findOne($item->product_id);
                        $product->count = $item->count;
                        $product->status = '0';
                        $product->save(false);

                        $new_document_items = new DocumentItems();
                        $new_document_items->document_id = $document->id;
                        $new_document_items->nomenclature_id = $item->nom_id_for_name;
                        $new_document_items->count = $item->count;
                        $new_document_items->refuse_product_id = $product->id;
                        if ($product->AAH == 1){
                            $new_document_items->AAH = 'true';
                            $new_document_items->price_with_aah = $product->price;
                            $new_document_items->price = number_format((($product->price * 5)/6),2,'.','');
                        }else{
                            $new_document_items->AAH = 'false';
                            $new_document_items->price = $product->price;
                            $new_document_items->price_with_aah = $product->price + ($product->price * 20) / 100;
                        }
                        $new_document_items->status = '1';
                        $new_document_items->created_at = date('Y-m-d H:i:s');
                        $new_document_items->updated_at = date('Y-m-d H:i:s');
                        $new_document_items->save(false);
                        $item->status = '0';
                        $item->save(false);
                    }


                }
        }
        Log::afterSaves('Delete', '', $oldattributes['name'], '#', $premission);
        return $this->redirect(['index']);
    }
    public function actionDelivered($id)
    {
        $have_access = Users::checkPremission(55);
        if(!$have_access){
            $this->redirect('/site/403');
        }
//        echo "<pre>";
        $session = Yii::$app->session;
        date_default_timezone_set('Asia/Yerevan');
        $changed_items = [];
        $order_items = OrderItems::find()
            ->select('order_items.order_id,order_items.warehouse_id,order_items.product_id,order_items.nom_id_for_name,
            order_items.count_by,order_items.count,products.AAH,products.price')
            ->leftJoin('products', 'order_items.product_id = products.id')
            ->where(['order_items.order_id' => $id])
            ->andWhere(['order_items.status' => '1'])
            ->asArray()->all();

        $size = 1;
        $num = 0;
        for ($i = 0; $i < count($order_items); $i++){
            if ($order_items[$i]['count'] - $order_items[$i]['count_by'] != 0){
                $changed_items[$num] = [
                    $order_items[$i]['order_id'],
                    $order_items[$i]['warehouse_id'],
                    $order_items[$i]['product_id'],
                    $order_items[$i]['nom_id_for_name'],
                    $order_items[$i]['price'],
                    $order_items[$i]['count']-$order_items[$i]['count_by'],
                    $order_items[$i]['AAH'],
                ];
                if ($size == 1) {
                    $size++;
                    $user_name = Users::find()->select('*')->where(['id' => $session['user_id']])->asArray()->one();
                    $client_name = Orders::find()
                        ->select('clients.name')
                        ->leftJoin('clients', 'clients.id = orders.clients_id')
                        ->where(['orders.id' => $id])
                        ->asArray()
                        ->one();
                    $text = $user_name['name'] . '(ի) առաքումից ' . $client_name['name'] . '(ն\ը) ապրանք է հետ վերադարձ արել։';
                    Notifications::createNotifications('Ապրանքի վերադարձ', $text,'changeorderscount');
                }
                $num++;
            }
        }
//        var_dump($order_items);
        if(!empty($changed_items)){
            $keeper = Users::findOne(['warehouse_id' => $changed_items[0][1]]);
            $document = new Documents();
            $document->user_id = $keeper->id;
            $document->orders_id = $changed_items[0][0];
            $document->warehouse_id = $changed_items[0][1];
            $document->rate_id = 1;
            $document->rate_value = 1;
            $document->document_type = 6;
            $document->comment = 'Վերադարձրած ապրանք(ներ)';
            $document->date = date('Y-m-d H:i:s');
            $document->status = '1';
            $document->created_at = date('Y-m-d H:i:s');
            $document->updated_at = date('Y-m-d H:i:s');
            $document->save(false);
            for ($k = 0; $k < count($changed_items); $k++){
                    $new_document_items = new DocumentItems();
                    $new_document_items->document_id = $document->id;
                    $new_document_items->nomenclature_id = $changed_items[$k][3];
                    $new_document_items->refuse_product_id = $changed_items[$k][2];
                    $new_document_items->count = $changed_items[$k][5];
                    if ($changed_items[$k][6] == 1){
                        $new_document_items->AAH = 'true';
                        $new_document_items->price_with_aah = $changed_items[$k][4];
                        $new_document_items->price = number_format((($changed_items[$k][4] * 5)/6),2,'.','');
                    }else{
                        $new_document_items->AAH = 'false';
                        $new_document_items->price = $changed_items[$k][4];
                        $new_document_items->price_with_aah = $changed_items[$k][4] + ($changed_items[$k][4] * 20) / 100;
                    }
                    $new_document_items->status = '1';
                    $new_document_items->created_at = date('Y-m-d H:i:s');
                    $new_document_items->updated_at = date('Y-m-d H:i:s');
                    $new_document_items->save(false);
            }
        }
//        exit();
        $orders = Orders::findOne($id);
        $orders->status = '2';
        $orders->save();
        $user_name = Users::find()->select('*')->where(['id' => $session['user_id']])->asArray()->one();
        $client_name = Orders::find()
            ->select('clients.name')
            ->leftJoin('clients', 'clients.id = orders.clients_id')
            ->where(['orders.id' => $id])
            ->asArray()
            ->one();
        $text = $user_name['name'] . '(ը/ն) հաստատել է ' . $client_name['name'] . '(ի) պատվեի առաքումը։';
        Notifications::createNotifications('Հաստատել պատվեր', $text,'ordersdelivered');
        return $this->redirect(['index']);
    }
    public function actionExit($id){
        $have_access = Users::checkPremission(76);
        if(!$have_access){
            $this->redirect('/site/403');
        }
//        echo "<pre>";
        $session = Yii::$app->session;
        date_default_timezone_set('Asia/Yerevan');
        $exit_documents = [];
        $order_items = OrderItems::find()->select('order_items.id,order_items.order_id,order_items.warehouse_id,order_items.product_id,
        order_items.nom_id_for_name,order_items.count_by,products.AAH,products.price')
            ->leftJoin('products','products.id = order_items.product_id')
            ->where(['order_items.order_id' => $id])
            ->andWhere(['order_items.status' => '1'])
            ->asArray()->all();
//        echo "<pre>";
//        var_dump($order_items);
//        exit();
        for ($i = 0;$i < count($order_items);$i++){
            $exit_documents[$i] = [
              $order_items[$i]['order_id'],
              $order_items[$i]['product_id'],
              $order_items[$i]['warehouse_id'],
              $order_items[$i]['nom_id_for_name'],
              $order_items[$i]['count_by'],
              $order_items[$i]['AAH'],
              $order_items[$i]['price'],
            ];
        }
        if (!empty($exit_documents)){
            $new_exit_document = new Documents();
            $keeper = Users::findOne(['warehouse_id' => $exit_documents[0][2]]);
            $new_exit_document->user_id = $keeper->id;
            $new_exit_document->orders_id = $exit_documents[0][0];
            $new_exit_document->warehouse_id = $exit_documents[0][2];
            $new_exit_document->rate_id = 1;
            $new_exit_document->rate_value = 1;
            $new_exit_document->document_type = 9;
            $new_exit_document->comment = 'Ելքագրված փաստաթուղթ';
            $new_exit_document->date = date('Y-m-d H:i:s');
            $new_exit_document->status = '1';
            $new_exit_document->created_at = date('Y-m-d H:i:s');
            $new_exit_document->updated_at = date('Y-m-d H:i:s');
            $new_exit_document->save(false);
            for ($j = 0; $j< count($exit_documents); $j++){
                $new_exit_document_items = new DocumentItems();
                $new_exit_document_items->document_id = $new_exit_document->id;
                $new_exit_document_items->nomenclature_id = $exit_documents[$j][3];
                $new_exit_document_items->count = $exit_documents[$j][4];
                $new_exit_document_items->refuse_product_id = $exit_documents[$j][1];
                if ($exit_documents[$j][5] == 1){
                    $new_exit_document_items->price_with_aah = $exit_documents[$j][6];
                    $new_exit_document_items->AAH = 'true';
                    $new_exit_document_items->price = number_format((($exit_documents[$j][6] * 5)/6),2,'.','');
                }else{
                    $new_exit_document_items->price = $exit_documents[$j][6];
                    $new_exit_document_items->AAH = 'false';
                    $new_exit_document_items->price_with_aah = $exit_documents[$j][6] + ($exit_documents[$j][6] * 20) / 100;
                }
                $new_exit_document_items->status = '1';
                $new_exit_document_items->created_at = date('Y-m-d H:i:s');
                $new_exit_document_items->updated_at = date('Y-m-d H:i:s');
                $new_exit_document_items->save(false);
            }
            if ($session['role_id'] == 4){
                $user_name = Users::find()->select('*')->where(['id' => $session['user_id']])->asArray()->one();
                $text = $user_name['name'] . '(ն\ը) ելքագրել է ապրանք։';
                Notifications::createNotifications('Ելքագրել փաստաթուղթ', $text,'exitdocument');
            }
        }
        $is_exit_orders = Orders::findOne($id);
        $is_exit_orders->is_exit = '0';
        $is_exit_orders->save(false);
        return $this->redirect(['index']);
    }
    public  function actionFilterStatus(){
        if ($_GET){
            $page_value = null;
            if(isset($_GET["page"]))
                $page_value = intval($_GET["page"]);
            $searchModel = new OrdersSearch();
            $dataProvider = $searchModel->search($this->request->queryParams);
            $sub_page = [];
            $date_tab = [];
            $is_filter = false;
            if ($_GET['numberVal'] || $_GET['managerId'] || $_GET['clientsVal'] || $_GET['ordersDate'] || $_GET['type']){
                $is_filter = true;
            }
            $session = Yii::$app->session;
            $user_id = $session['user_id'];
            $manager_route_id = ManagerDeliverCondition::find()
                ->select('route_id, deliver_id')
                ->where(['manager_id' => $user_id])
                ->andWhere(['status' => '1'])
                ->asArray()
                ->all();
            $clients = Clients::find()
                ->select('id, name')
                ->where(['=','status',1]);
            if ($session['role_id'] == 2) {
                $clients->andWhere(['in', 'route_id', $manager_route_id]);
            }
            $clients =  $clients->asArray()->all();
            $render_array = [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'sub_page' => $sub_page,
                'date_tab' => $date_tab,
                'clients' => $clients,
                'page_value' => $page_value,
                'is_filter' => $is_filter,
            ];
            if(Yii::$app->request->isAjax){
                if (isset($_GET['type']) && $_GET['type'] == 'order'){
                    if (isset($_GET['clickXLSX']) && $_GET['clickXLSX'] === 'clickXLSX') {
                        $this->layout = 'index.php';
                        $render_array['data_size'] = 'max';
                        return $this->renderAjax('widget', $render_array);
                    } else {
                        return $this->renderAjax('widget', $render_array);
                    }
                }elseif (isset($_GET['type']) && $_GET['type'] == 'product'){
                    return $this->renderAjax('products', $render_array);
                }elseif (!isset($_GET['type'])){
                    $this->layout = 'index.php';
                    $render_array['data_size'] = 'max';
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

    public function actionChangeCount(){
        if ($this->request->isGet){
            $items = $this->request->get('orderItemsId');
            $order_items = OrderItems::find()
                ->select('order_items.discount,order_items.id,order_items.count,order_items.count_by,order_items.price_by,order_items.price,order_items.price_before_discount,
                order_items.price_before_discount_by,order_items.discount_by,order_items.cost,order_items.cost_by,nomenclature.name,')
                ->leftJoin('nomenclature', 'nomenclature.id = order_items.nom_id_for_name')
                ->where(['order_items.id' => intval($items)])
                ->asArray()
                ->one();
            return $this->renderAjax('change-count',[
                'order_items' => $order_items,
            ]);
        }
    }
    public function actionChangingItems(){
        if ($this->request->isPost){
            $post = $this->request->post();

            $order_items = OrderItems::findOne($post['itemsId']);
            $order_items->count_by = $post['countBy'];
            $order_items->cost_by = $post['costBy'];
            $order_items->discount_by = $post['discountBy'];
            $order_items->price_by = $post['priceBy'];
            $order_items->price_before_discount_by = $post['priceBeforeDiscountBy'];
            $order_items->save(false);
            $for_orders = OrderItems::find()->select('SUM(count_by) as count,SUM(price_by) as total_price,
            SUM(discount_by) as discount,SUM(price_before_discount_by) as total_price_before_discount')
                ->where(['status' => '1'])
                ->andWhere(['order_id' => $order_items->order_id])
                ->asArray()
                ->all();
            $orders = Orders::findOne($order_items->order_id);
//            var_dump($orders);
//            var_dump($for_orders);
//            exit();
            $orders->total_price = $for_orders[0]['total_price'];
            $orders->total_price_before_discount = $for_orders[0]['total_price_before_discount'];
            $orders->total_discount = $for_orders[0]['discount'];
            $orders->total_count = $for_orders[0]['count'];
            $orders->save(false);

            $products = Products::findOne($order_items->product_id);
//            $products_batch = Products::findOne($products->parent_id);
//            $products_batch->count_balance += $products->count - $order_items->count_by;
//            $products_batch->save(false);
            $products->count = $order_items->count_by;
            $products->save(false);
            return json_encode('change');
        }
    }

    public function actionDeleteItems(){
//        echo "<pre>";
        if ($this->request->isPost){
            $total_count = $this->request->post('totalCount');
            $total_price = $this->request->post('totalPrice');
            $total_price_before_discount = $this->request->post('totalPriceBeforeDiscount');
            $total_discount = $this->request->post('totalDiscount');
            $item_id = intval($this->request->post('itemId'));
            $nom_id = intval($this->request->post('nomId'));

            $orders_id = OrderItems::find()->select('order_id,count,count_by,warehouse_id, nom_id_for_name,product_id')->where(['id' => $item_id])->one();
            $keeper = Users::findOne(['warehouse_id' => $orders_id->warehouse_id]);
            $is_exit = Orders::findOne($orders_id->order_id);
            $exist_orders_items = OrderItems::find()->where(['status' => '1'])->andWhere(['order_id' => $orders_id->order_id])->count();
            if ($exist_orders_items == 1){
                return json_encode(false);
            }else{
                if ($is_exit->is_exit == 1){
                    $delete_items = OrderItems::findOne($item_id);
                    $delete_items->status = '0';
                    $delete_items->save(false);

                    $delete_products = Products::findOne($orders_id->product_id);
                    $delete_products->status = '0';
                    $delete_products->save(false);

                    $enter_products = Products::findOne($delete_products->parent_id);
                    $enter_products->count_balance += $delete_products->count;
                    $enter_products->save(false);

                    $update_orders = Orders::findOne($orders_id->order_id);
                    $update_orders->total_count = $total_count;
                    $update_orders->total_price = $total_price;
                    $update_orders->total_price_before_discount = $total_price_before_discount;
                    $update_orders->total_discount = $total_discount;
                    $update_orders->save(false);
                    return json_encode(true);

                }else{
                    $product_id = Products::findOne($orders_id->product_id);
                    if ($orders_id->count - $orders_id->count_by == 0){
//                    var_dump(1111);
                        $delete_items = OrderItems::findOne($item_id);
                        $delete_items->status = '0';
                        $delete_items->save(false);

                        $delete_products = Products::findOne($delete_items->product_id);
                        $delete_products->status = '0';
                        $delete_products->save(false);

                        $document = new Documents();
                        $document->user_id = $keeper->id;
                        $document->orders_id = $orders_id->order_id;
                        $document->warehouse_id = $orders_id->warehouse_id;
                        $document->rate_id = 1;
                        $document->rate_value = 1;
                        $document->document_type = 6;
                        $document->comment = 'Վերադարձրած ապրանք';
                        $document->date = date('Y-m-d H:i:s');
                        $document->status = '1';
                        $document->created_at = date('Y-m-d H:i:s');
                        $document->updated_at = date('Y-m-d H:i:s');
                        $document->save(false);

                        $new_document_items = new DocumentItems();
                        $new_document_items->document_id = $document->id;
                        $new_document_items->nomenclature_id = $orders_id->nom_id_for_name;
                        $new_document_items->count = $orders_id->count_by;
                        $new_document_items->refuse_product_id = $delete_products->id;
                        if ($product_id->AAH == 1){
                            $new_document_items->AAH = 'true';
                            $new_document_items->price_with_aah = $product_id->price;
                            $new_document_items->price = number_format((($product_id->price * 5)/6),2,'.','');
                        }else{
                            $new_document_items->AAH = 'false';
                            $new_document_items->price = $product_id->price;
                            $new_document_items->price_with_aah = $product_id->price + ($product_id->price * 20) / 100;
                        }
                        $new_document_items->status = '1';
                        $new_document_items->created_at = date('Y-m-d H:i:s');
                        $new_document_items->updated_at = date('Y-m-d H:i:s');
                        $new_document_items->save(false);
                        $update_orders = Orders::findOne($orders_id->order_id);
                        $update_orders->total_count = $total_count;
                        $update_orders->total_price = $total_price;
                        $update_orders->total_price_before_discount = $total_price_before_discount;
                        $update_orders->total_discount = $total_discount;
                        $update_orders->save(false);
                        return json_encode(true);


                    }else{
                        $delete_items = OrderItems::findOne($item_id);
                        $delete_items->status = '0';
                        $delete_items->save(false);

                        $product_id->count = $delete_items->count;
                        $product_id->status = '0';
                        $product_id->save(false);

                        $document = new Documents();
                        $document->user_id = $keeper->id;
                        $document->orders_id = $orders_id->order_id;
                        $document->warehouse_id = $orders_id->warehouse_id;
                        $document->rate_id = 1;
                        $document->rate_value = 1;
                        $document->document_type = 6;
                        $document->comment = 'Վերադարձրած ապրանք';
                        $document->date = date('Y-m-d H:i:s');
                        $document->status = '1';
                        $document->created_at = date('Y-m-d H:i:s');
                        $document->updated_at = date('Y-m-d H:i:s');
                        $document->save(false);

                        $new_document_items = new DocumentItems();
                        $new_document_items->document_id = $document->id;
                        $new_document_items->nomenclature_id = $orders_id->nom_id_for_name;
                        $new_document_items->count = $orders_id->count;
                        $new_document_items->refuse_product_id = $product_id->id;
                        if ($product_id->AAH == 1){
                            $new_document_items->AAH = 'true';
                            $new_document_items->price_with_aah = $product_id->price;
                            $new_document_items->price = number_format((($product_id->price * 5)/6),2,'.','');
                        }else{
                            $new_document_items->AAH = 'false';
                            $new_document_items->price = $product_id->price;
                            $new_document_items->price_with_aah = $product_id->price + ($product_id->price * 20) / 100;
                        }
                        $new_document_items->status = '1';
                        $new_document_items->created_at = date('Y-m-d H:i:s');
                        $new_document_items->updated_at = date('Y-m-d H:i:s');
                        $new_document_items->save(false);

                        $update_orders = Orders::findOne($orders_id->order_id);
                        $update_orders->total_count = $total_count;
                        $update_orders->total_price = $total_price;
                        $update_orders->total_price_before_discount = $total_price_before_discount;
                        $update_orders->total_discount = $total_discount;
                        $update_orders->save(false);
                        return json_encode(true);
                    }

                }
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
