<?php

namespace app\controllers;

use app\models\Clients;
use app\models\CoordinatesUser;
use app\models\Log;
use app\models\ManagerDeliverCondition;
use app\models\Orders;
use app\models\Premissions;
use app\models\Route;
use app\models\RouteSearch;
use app\models\Users;
use app\models\Warehouse;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use function PHPUnit\Framework\isFalse;

/**
 * RouteController implements the CRUD actions for Route model.
 */
class RouteController extends Controller
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
        $this->enableCsrfValidation = false;
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
     * Lists all Route models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $have_access = Users::checkPremission(52);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $sub_page = [];
        $date_tab = [];

        $searchModel = new RouteSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,

        ]);
    }

    /**
     * Displays a single Route model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView()
    {
        $id = Yii::$app->request->get('id');
        $have_access = Users::checkPremission(54);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $sub_page = [];
        $date_tab = [];
        if ($this->findModel($id)->status == 0) {
            return $this->redirect(Yii::$app->request->referrer);
        }
        return $this->render('route-view', [
            'model' => $this->findModel($id),
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,

        ]);
    }
    public function actionRouteSort()
    {
        $id = Yii::$app->request->get('id');
        $have_access = Users::checkPremission(54);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $sub_page = [];
        $date_tab = [];

        $result = Clients::find()
            ->select('id,name')
            ->where(['=', 'route_id', intval($id)])
            ->andwhere(['status' => 1])
            ->orderBy(['sort_'=> SORT_ASC])
            ->asArray()
            ->all();

        return $this->render('route-sort', [
            'model' => $this->findModel($id),
            'result' => $result,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,

//            'alldata' => $alldata,
        ]);
    }


    public function actionSave()
    {
        $db = Yii::$app->db;
        if ($this->request->isPost) {
            if (!empty($_POST['sort'])) {
                foreach ($_POST['sort'] as $i => $row) {
                    $client = Clients::findOne($row);
                    $client->sort_ = $i;
                    $client->save(false);
                }
            }
            return 'success';
        } else {
            return 'error';
        }
    }

    /**
     * Creates a new Route model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $have_access = Users::checkPremission(49);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $model = new Route();
        $sub_page = [];
        $date_tab = [];

        $url = Url::to('', 'http');
        $url = str_replace('create', 'view', $url);
        $premission = Premissions::find()
            ->select('name')
            ->where(['id' => 49])
            ->asArray()
            ->one();
        $model = Route::getDefVals($model);
        if ($this->request->isPost) {
            date_default_timezone_set('Asia/Yerevan');
            $model->created_at = date('Y-m-d H:i:s');
            $model->updated_at = date('Y-m-d H:i:s');
            if ($model->load($this->request->post()) && $model->save()) {
                Log::afterSaves('Create', $model, '', $url.'?'.'id'.'='.$model->id, $premission);
                return $this->redirect('index');
            }
        } else {
            $model->loadDefaultValues();
        }
//        $route = Route::find()->select('id, route')->asArray()->all();

        return $this->render('create', [
            'model' => $model,
//            'route' => $route,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,

        ]);
    }

    /**
     * Updates an existing Route model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $have_access = Users::checkPremission(50);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $model = $this->findModel($id);
        $sub_page = [];
        $date_tab = [];
        if ($this->findModel($id)->status == 0) {
            return $this->redirect(Yii::$app->request->referrer);
        }
        $url = Url::to('', 'http');
        $oldattributes = Route::find()
            ->select('*')
            ->where(['id' => $id])
            ->asArray()
            ->one();
        $premission = Premissions::find()
            ->select('name')
            ->where(['id' => 50])
            ->asArray()
            ->one();
        if ($this->request->isPost){
            date_default_timezone_set('Asia/Yerevan');
            $model->updated_at = date('Y-m-d H:i:s');
            if($model->load($this->request->post()) && $model->save()) {
                Log::afterSaves('Update', $model, $oldattributes, $url, $premission);
                return $this->redirect('index');
            }
        }


        return $this->render('update', [
            'model' => $model,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,

        ]);
    }

    /**
     * Deletes an existing Route model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $have_access = Users::checkPremission(51);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $oldattributes = Route::find()
            ->select('route')
            ->where(['id' => $id])
            ->asArray()
            ->one();

        $premission = Premissions::find()
            ->select('name')
            ->where(['id' => 51])
            ->asArray()
            ->one();
        $this->findModel($id)->delete();
        Log::afterSaves('Delete', '', $oldattributes['route'], '#', $premission);
        return $this->redirect(['index']);
    }

    /**
     * Finds the Route model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Route the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Route::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionShippingRoute(){
        $have_access = Users::checkPremission(69);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $id = Yii::$app->request->get('id');
        $sub_page = [];
        $date_tab = [];
        $route = Route::find()->select('route')
            ->where(['id' => $id])
            ->asArray()
            ->one();
        $users = Users::find()
            ->where(['status' => 1])
            ->andWhere(['in', 'role_id', [2,3]])
            ->asArray()->all();
//        $users = Users::find()->select('users.id, users.name')
//            ->leftJoin('orders', 'orders.user_id = users.id')
//            ->leftJoin('clients', 'clients.id = orders.clients_id')
//            ->where(['clients.route_id' => $id])
//            ->asArray()
//            ->all();
        $all_managers = Users::find()
            ->select('id')
            ->where(['and', ['role_id' => '2'], ['status' => 1]])
            ->asArray()
            ->all();
        $managers_senders = [];
        foreach ($all_managers as $manager) {
            $managerId = $manager['id'];
            $senders = ManagerDeliverCondition::find()
                ->select('deliver_id')
                ->where(['manager_id' => $managerId, 'status' => 1])
                ->asArray()
                ->all();
            $senderIds = array_column($senders, 'deliver_id');
            $managers_senders[$managerId] = $senderIds;
        }
        return $this->render('shipping-route', [
            'id' => $id,
            'route' => $route,
            'users' => $users,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,
            'managers_senders' => $managers_senders,

        ]);
    }

    public function actionDeleteAllVisit()
    {
        if(isset($_GET)){
            $coordinatesUsers = CoordinatesUser::find()->where(['not', ['visit' => null]])->all();
            foreach ($coordinatesUsers as $coordinatesUser) {
                $coordinatesUser->visit = 0;
                $coordinatesUser->save(false);
            }
        }
    }
    public function actionUpdateVisit()
    {
        if(isset($_GET)){
            $visit_get = CoordinatesUser::findOne(['id' => $_GET['coord_id']]);
            $visit_get->visit = $_GET['visit'];
            $visit_get->save(false);
        }
    }
    public function actionLocationValue()
    {
        if (isset($_GET)) {
            $get = $this->request->get();
            $route_id = intval($get['urlId']);
            $valuedate = $get['date'];
            $formattedSelectedDate = Yii::$app->formatter->asDatetime($valuedate, 'yyyy-MM-dd');
            $userId =  intval($get['user']);
            $coordinatesUser = CoordinatesUser::find()
                ->select('id, latitude, longitude')
                ->where(['or',['user_id' => $userId]])
                ->andWhere(['route_id' => $route_id])
                ->andWhere(['and',['>=','created_at', $valuedate.' 00:00:00'],
                    ['<','created_at', $valuedate.' 23:59:59']])
                ->orderBy(['created_at'=>SORT_ASC])
                ->groupBy('latitude')
                ->asArray()
                ->all();
            $coordinatesUserCopy = $coordinatesUser;
            if (count($coordinatesUser) != 0) {
                $warhouse_id_clients = Clients::find()
                    ->select('clients.client_warehouse_id, warehouse.location')
                    ->leftJoin('warehouse', 'warehouse.id = clients.client_warehouse_id')
                    ->where(['clients.route_id' => $route_id])
                    ->asArray()
                    ->one();
                $locations = Orders::find()
                    ->select(["clients.location", 'clients.name', 'DATE_FORMAT(orders.orders_date, "%Y-%m-%d") as orders_date'])
                    ->leftJoin('clients', 'clients.id = orders.clients_id')
                    ->where(['route_id' => $route_id])
                    ->andWhere(['and', ['>=', 'orders.orders_date', $formattedSelectedDate . ' 00:00:00'],
                        ['<', 'orders.orders_date', $formattedSelectedDate . ' 23:59:59']])
                    ->andWhere(['!=', 'orders.status', '0'])
//                ->andwhere(['=', 'orders.user_id', $userId])
                    ->asArray()
                    ->orderBy('clients.sort_', SORT_DESC)
                    ->all();
                $clients_locations = $locations;
                $visit = CoordinatesUser::find()
                    ->select('visit, id')
                    ->where(['or', ['user_id' => $userId]])
                    ->asArray()
                    ->all();
                $locations = array_chunk($locations, 20);
                $coordinatesUserCopy = array_chunk($coordinatesUserCopy, 20);
                return json_encode([
                    'location' => $locations,
                    'clients_locations' => $clients_locations,
                    'warehouse' => $warhouse_id_clients,
                    'coordinatesUser' => $coordinatesUser,
                    'coordinatesUserCopy' => $coordinatesUserCopy,
                    'visit' => $visit,
                ]);
            }
        }
    }
}
