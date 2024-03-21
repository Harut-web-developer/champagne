<?php

namespace app\controllers;

use app\models\Clients;
use app\models\Log;
use app\models\ManagerDeliverCondition;
use app\models\Orders;
use app\models\Payments;
use app\models\PaymentsSearch;
use app\models\Premissions;
use app\models\Rates;
use app\models\Route;
use app\models\Users;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PaymentsController implements the CRUD actions for Payments model.
 */
class PaymentsController extends Controller
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
     * Lists all Payments models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $have_access = Users::checkPremission(65);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $sub_page = [];
        if (Users::checkPremission(66)){
            $statistics = ['name' => 'Վիճակագրություն','address' => '/payments/statistics'];
            array_push($sub_page,$statistics);
        }
        if (Users::checkPremission(48)){
            $rates = ['name' => 'Փոխարժեք','address' => '/rates'];
            array_push($sub_page,$rates);
        }

        $date_tab = [];

        $searchModel = new PaymentsSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,

        ]);
    }

    /**
     * Displays a single Payments model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public  function actionStatistics(){
        $have_access = Users::checkPremission(66);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $session = Yii::$app->session;
        if ($session['role_id'] == '1' || $session['role_id'] == '4'){
            $routeIds = Route::find()->select('id')->asArray()->all();
            $route_id = ArrayHelper::getColumn($routeIds, 'id');
        }else{
            if ($session['role_id'] == '2'){
                $routeIds = ManagerDeliverCondition::find()->select('route_id')->where(['manager_id' => $session['user_id']])->asArray()->all();
                $route_id = ArrayHelper::getColumn($routeIds, 'route_id');
            } elseif ($session['role_id'] == '3'){
                $routeIds = ManagerDeliverCondition::find()->select('route_id')->where(['deliver_id' => $session['user_id']])->asArray()->all();
                $route_id = ArrayHelper::getColumn($routeIds, 'route_id');
            }
        }
        $statistics = Clients::find()
            ->innerJoinWith(['orders' , 'payments'])
            ->where(['in','route_id', $route_id])
            ->asArray()
            ->all();
        $sub_page = [];
        if (Users::checkPremission(65)){
            $rates = ['name' => 'Վճարումներ','address' => '/payments'];
            array_push($sub_page,$rates);
        }
        if (Users::checkPremission(48)){
            $rates = ['name' => 'Փոխարժեք','address' => '/rates'];
            array_push($sub_page,$rates);
        }
        $date_tab = [];

        return $this->render('statistics',[
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,

            'statistics' => $statistics
        ]);
    }
    public function actionView($id)
    {
        $sub_page = [
            ['name' => 'Վիճակագրություն','address' => '/payments/statistics']
        ];
        $date_tab = [];

        return $this->render('view', [
            'model' => $this->findModel($id),
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,

        ]);
    }

    /**
     * Creates a new Payments model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $have_access = Users::checkPremission(62);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $model = new Payments();
        if ($this->request->isPost) {
            date_default_timezone_set('Asia/Yerevan');
            $url = Url::to('', 'http');
            $url = str_replace('create', 'view', $url);
            $premission = Premissions::find()
                ->select('name')
                ->where(['id' => 62])
                ->asArray()
                ->one();
            $model_l = array();
            $post = $this->request->post();
            $model->client_id = $post['client_id'];
            $model->payment_sum = floatval($post['Payments']['payment_sum']);
            $model->pay_date = $post['Payments']['pay_date'];
            $model->rate_id = $post['Payments']['rate_id'];
            $model->rate_value = $post['Payments']['rate_value'];
            $model->comment = $post['Payments']['comment'];
            $model->created_at = date('Y-m-d H:i:s');
            $model->updated_at = date('Y-m-d H:i:s');
            if ($model->save()) {
                foreach ($model as $index => $item) {
                    $model_l[$index] = $item;
                }
                $client_orders = Orders::find()
                    ->select(['orders.id', 'orders.total_price as debt'])
                    ->leftJoin('clients', 'orders.clients_id = clients.id')
                    ->where(['orders.clients_id' => $post['client_id']])
                    ->andwhere(['or',['orders.status' => '2'],['orders.status' => '3'],['orders.status' => '4'],['orders.status' => '5']])
                    ->groupBy('orders.id')
                    ->asArray()
                    ->all();

                $payments = Payments::find()
                    ->select('SUM(payment_sum) as payments_total')
                    ->where(['client_id'=> $post['client_id']])
                    ->andWhere(['status' => '1'])
                    ->asArray()->one();
                $payments = $payments['payments_total'];
                $debt_total = 0;
                foreach ($client_orders as $keys => $client_order) {
                    if ($payments) {
                        if ($payments >= intval($client_order['debt'])) {
                            $payments -= intval($client_order['debt']);
                            $orders = Orders::findOne($client_order['id']);
                            $orders->status = '3';
                            $orders->save(false);
                            foreach ($orders as $index => $item) {
                                $model_l[$index.$keys] = $item;
                            }
                        } else {
                            $debt_total += intval($client_order['debt']) - $payments;
                            $payments = 0;
                        }
                    } else {
                        $debt_total += intval($client_order['debt']) - $payments;
                    }
                }
                Log::afterSaves('Create', $model_l, '', $url.'?'.'id'.'='.$model->id, $premission);
                return $this->redirect(['index', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }
        $sub_page = [
            ['name' => 'Վիճակագրություն','address' => '/payments/statistics']
        ];
        $date_tab = [];
        $client = Clients::find()->select('id,name')->asArray()->all();
        $rates = Rates::find()->select('id,name')->where(['status' => ['1','2']])->asArray()->all();
        $rates = ArrayHelper::map($rates,'id','name');
        return $this->render('create', [
            'model' => $model,
            'client' => $client,
            'rates' => $rates,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,

        ]);
    }

    /**
     * Updates an existing Payments model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $have_access = Users::checkPremission(63);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $model = $this->findModel($id);
        $url = Url::to('', 'http');
        $url = str_replace('update', 'view', $url);
        $premission = Premissions::find()
            ->select('name')
            ->where(['id' => 63])
            ->asArray()
            ->one();
        $model_l = array();
        if ($this->request->isPost) {
            date_default_timezone_set('Asia/Yerevan');
            $post = $this->request->post();
            $model->client_id = $post['client_id'];
            $model->payment_sum = floatval($post['Payments']['payment_sum']);
            $model->pay_date = $post['Payments']['pay_date'];
            $model->rate_id = $post['Payments']['rate_id'];
            $model->rate_value = $post['Payments']['rate_value'];
            $model->comment = $post['Payments']['comment'];
            $model->updated_at = date('Y-m-d H:i:s');
            $model->save();
            $model_l = $model;
            Log::afterSaves('Update', $model_l, '', $url, $premission);
            return $this->redirect(['index', 'id' => $model->id]);
        }
        $sub_page = [
            ['name' => 'Վիճակագրություն','address' => '/payments/statistics']
        ];
        $date_tab = [];
        $payment_clients = Payments::find()->select('client_id')->where(['=','id',$id])->asArray()->all();
        $payment_clients = array_column($payment_clients,'client_id');
        $clients = Clients::find()->select('id, name')->Where(['=','status',1])->asArray()->all();
        $rates = Rates::find()->select('id,name')->where(['status' => ['1','2']])->asArray()->all();
        $rates = ArrayHelper::map($rates,'id','name');
        $client = Clients::find()->select('id,name')->asArray()->all();
        $client = ArrayHelper::map($client,'id','name');
        return $this->render('update', [
            'model' => $model,
            'client' => $client,
            'rates' => $rates,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,
            'payment_clients' => $payment_clients,
            'clients' => $clients,
        ]);
    }

    /**
     * Deletes an existing Payments model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $have_access = Users::checkPremission(64);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $premission = Premissions::find()
            ->select('name')
            ->where(['id' => 64])
            ->asArray()
            ->one();
        $oldattributes = Payments::find()
            ->select(['clients.name'])
            ->leftJoin('clients', 'clients.id = payments.client_id')
            ->where(['payments.id' => $id])
            ->asArray()
            ->one();
        $payments = Payments::findOne($id);
        $payments->status = '0';
        $payments->save();
        Log::afterSaves('Delete', '', $oldattributes['name'], '#', $premission);
        return $this->redirect(['index']);
    }

    /**
     * Finds the Payments model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Payments the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Payments::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
