<?php

namespace app\controllers;

use app\models\Log;
use app\models\Nomenclature;
use app\models\Orders;
use app\models\Payments;
use app\models\Premissions;
use Psy\Command\EditCommand;
use Yii;
use yii\helpers\Url;
use app\models\Clients;
use app\models\Route;
use app\models\ClientsSearch;
use app\models\Users;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * ClientsController implements the CRUD actions for Clients model.
 */
class ClientsController extends Controller
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
     * Lists all Clients models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $have_access = Users::checkPremission(8);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $sub_page = [
            ['name' => 'Խմբեր','address' => '/groups-name'],
        ];
        $date_tab = [];
        $searchModel = new ClientsSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,
        ]);
    }

    /**
     * Displays a single Clients model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView()
    {
        $id = Yii::$app->request->get('id');
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
    public function actionClientsDebt()
    {
        $have_access = Users::checkPremission(68);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $id = intval(Yii::$app->request->get('id'));
        $sub_page = [];
        $date_tab = [];

        $client_orders = Orders::find()
            ->select(['orders.id', 'orders.total_price as debt'])
            ->leftJoin('clients', 'orders.clients_id = clients.id')
            ->where(['orders.clients_id' => $id])
            ->andWhere(['or',['orders.status' => '2'],['orders.status' => '3']])
            ->groupBy('orders.id')
            ->asArray()
            ->all();

        $payments = Payments::find()->select('SUM(payment_sum) as payments_total')->where(['client_id'=> $id])->asArray()->one();

        return $this->render('clients_debt', [
            'model' => $this->findModel($id),
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,

            'client_orders' => $client_orders,
            'payments' => $payments['payments_total'],
        ]);
    }
    /**
     * Creates a new Clients model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $have_access = Users::checkPremission(5);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $sub_page = [];
        $date_tab = [];

        $model = new Clients();
        $url = Url::to('', 'http');
        $url = str_replace('create', 'view', $url);
        $premission = Premissions::find()
            ->select('name')
            ->where(['id' => 5])
            ->asArray()
            ->one();
        if ($this->request->isPost) {
            date_default_timezone_set('Asia/Yerevan');
            $post = $this->request->post();
            $model->name = $post['Clients']['name'];
            $model->location = $post['Clients']['location'];
            $model->route_id = $post['Clients']['route'];
            $model->phone = $post['Clients']['phone'];
            $model->created_at = date('Y-m-d H:i:s');
            $model->updated_at = date('Y-m-d H:i:s');
            $model = Clients::getDefVals($model);
            $model->save();
            Log::afterSaves('Create', $model, '', $url.'?'.'id'.'='.$model->id, $premission);
            $_POST['item_id'] = $model->id;
            if($post['newblocks'] || $post['new_fild_name']){
                Yii::$app->runAction('custom-fields/create-title',$post);
            }
                return $this->redirect(['index', 'id' => $model->id]);
        } else {
            $model->loadDefaultValues();
        }
        $route = Route::find()->select('id, route')->asArray()->all();
        return $this->render('create', [
            'model' => $model,
            'route' => $route,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,

        ]);
    }

    public function actionCoordsLocation()
    {
        if ($this->request->isPost) {
            $post = $this->request->post();
            $latlong = $post['coords'][0].','.$post['coords'][1];
            return json_encode($latlong);
        }
    }


    public function actionClientsLocation()
    {
        if ($this->request->isPost) {
            $allDataClients = Clients::find()->select('location')->asArray()->all();
            return json_encode($allDataClients);
        }
    }

    public function actionCreateFields()
    {
        $have_access = Users::checkPremission(70);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $sub_page = [];
        $date_tab = [];

        $model = new Clients();
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
     * Updates an existing Clients model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $have_access = Users::checkPremission(6);
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
        $oldattributes = Clients::find()
            ->select('*')
            ->where(['id' => $id])
            ->asArray()
            ->one();
        $premission = Premissions::find()
            ->select('name')
            ->where(['id' => 6])
            ->asArray()
            ->one();
        $route_value_update = Clients::find()->select('id, route_id')->where(['id' => $model->id])->one();
        if ($this->request->isPost) {
            date_default_timezone_set('Asia/Yerevan');
            $post = $this->request->post();
            $model->name = $post['Clients']['name'];
            $model->location = $post['Clients']['location'];
            $model->route_id = $post['Clients']['route'];
            $model->phone = $post['Clients']['phone'];
            $model->updated_at = date('Y-m-d H:i:s');
            $model->save();
            Log::afterSaves('Update', $model, $oldattributes, $url, $premission);
            $_POST['item_id'] = $model->id;
            if($post['newblocks'] || $post['new_fild_name']){
                Yii::$app->runAction('custom-fields/create-title',$post);
            }
            return $this->redirect(['index', 'id' => $model->id]);
        }
        $route = Route::find()->select('id, route')->asArray()->all();
        return $this->render('update', [
            'model' => $model,
            'route' => $route,
            'route_value_update' => $route_value_update,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,

        ]);
    }

    /**
     * Deletes an existing Clients model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $have_access = Users::checkPremission(7);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $oldattributes = Clients::find()
            ->select('name')
            ->where(['id' => $id])
            ->asArray()
            ->one();

        $premission = Premissions::find()
            ->select('name')
            ->where(['id' => 7])
            ->asArray()
            ->one();
        $clients = Clients::findOne($id);
        $clients->status = '0';
        $clients->save(false);
        Log::afterSaves('Delete', '', $oldattributes['name'], '#', $premission);
        return $this->redirect(['index']);
    }

    public function actionGetOrderId(){
        if ($this->request->isPost){
            $post = intval($this->request->post('id'));
            $orders = Orders::findOne($post);
            $orders->status = '3';
            $orders->save(false);

        }
    }
    /**
     * Finds the Clients model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Clients the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Clients::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
