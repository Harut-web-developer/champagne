<?php

namespace app\controllers;

use app\models\Clients;
use app\models\Nomenclature;
use app\models\OrderItems;
use app\models\Orders;
use app\models\OrdersSearch;
use app\models\Users;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OrdersController implements the CRUD actions for Orders model.
 */
class OrdersController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

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
        $searchModel = new OrdersSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
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
        return $this->render('view', [
            'model' => $this->findModel($id),
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
            $model->created_at = date('Y-m-d H:i:s');
            $model->updated_at = date('Y-m-d H:i:s');
            $model->save();
                for ($i = 0; $i < count($post['order_items']); $i++){
                    $order_items_create = new OrderItems();
                    $order_items_create->order_id = $model->id;
                    $order_items_create->product_id = $post['product_id'][$i];
                    $order_items_create->price = $post['price'][$i] * $post['count_'][$i];
                    $order_items_create->count = $post['count_'][$i];
                    $order_items_create->cost = $post['cost'][$i] * $post['count_'][$i];
                    $order_items_create->discount = $post['discount'][$i];
                    $order_items_create->price_before_discount = $post['product_id'][$i];
                    $order_items_create->created_at = date('Y-m-d H:i:s');
                    $order_items_create->updated_at = date('Y-m-d H:i:s');
                    $order_items_create->save(false);
                }
                return $this->redirect(['index', 'id' => $model->id]);
        } else {
            $model->loadDefaultValues();
        }
        $nomenclatures = Nomenclature::find()->select('nomenclature.id,nomenclature.name,nomenclature.price,
        nomenclature.cost,nomenclature.discount_id,nomenclature.price_before_discount,products.id as products_id,products.count,')
            ->leftJoin('products','nomenclature.id = products.nomenclature_id')
            ->asArray()->all();
        $clients = Clients::find()->select('id, name')->asArray()->all();
        $clients = ArrayHelper::map($clients,'id','name');
        $users = Users::find()->select('id, name')->asArray()->all();
        $users = ArrayHelper::map($users,'id','name');
        return $this->render('create', [
            'model' => $model,
            'users' => $users,
            'clients' => $clients,
            'nomenclatures' => $nomenclatures
        ]);
    }

    /**
     * Updates an existing Orders model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
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
            $model->updated_at = date('Y-m-d H:i:s');
            $model->save();
            $items = $post['order_items'];
            $quantity = 0;
            $tot_price = 0;
            foreach ($items as $k => $item){
                if($item != 'null'){
                    $order_item = OrderItems::findOne($item);
                    $order_item->order_id = $id;
                    $order_item->product_id = $post['product_id'][$k];
                    $order_item->price = $post['price'][$k] *$post['count_'][$k];
                    $order_item->count = $post['count_'][$k];
                    $order_item->cost = $post['cost'][$k] * $post['count_'][$k];
                    $order_item->discount = $post['discount'][$k];
                    $order_item->price_before_discount = $post['priceBeforeDiscount'][$k];
                    $order_item->updated_at = date('Y-m-d H:i:s');
                    $quantity += $order_item->count;
                    $tot_price += $order_item->price;
                    $order_item->save(false);
                } else {
                    $order_item = new OrderItems();
                    $order_item->order_id = $id;
                    $order_item->product_id = $post['product_id'][$k];
                    $order_item->price = $post['price'][$k] *$post['count_'][$k];
                    $order_item->count = $post['count_'][$k];
                    $order_item->cost = $post['cost'][$k] * $post['count_'][$k];
                    $order_item->discount = $post['discount'][$k];
                    $order_item->price_before_discount = $post['priceBeforeDiscount'][$k];
                    $order_item->created_at = date('Y-m-d H:i:s');
                    $order_item->updated_at = date('Y-m-d H:i:s');
                    $quantity += $order_item->count;
                    $tot_price += $order_item->price;
                    $order_item->save(false);
                }
            }
            $order = Orders::findOne($id);
            $order->total_price = $tot_price;
            $order->total_count = $quantity;
            $order->save(false);
            return $this->redirect(['index', 'id' => $model->id]);
        }
        $nomenclatures = Nomenclature::find()->select('nomenclature.id,nomenclature.name,nomenclature.price,
        nomenclature.cost,nomenclature.discount_id,nomenclature.price_before_discount,products.id as products_id,products.count,')
            ->leftJoin('products','nomenclature.id = products.nomenclature_id')
            ->asArray()->all();
        $order_items = OrderItems::find()->select('order_items.id,order_items.product_id,order_items.count,(order_items.price / order_items.count) as price,
        (order_items.cost / order_items.count) as cost,order_items.discount,order_items.price_before_discount,nomenclature.name ')
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

    public function actionDeleteItems(){
        if ($this->request->isPost){
            $id = intval($this->request->post('itemId'));
            $delete_items = OrderItems::findOne($id)->delete();
            if(isset($delete_items)){
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
}
