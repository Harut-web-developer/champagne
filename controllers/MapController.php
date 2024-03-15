<?php
namespace app\controllers;

use app\models\Clients;
use app\models\CoordinatesUser;
use app\models\DeliversGroup;
use app\models\ManagerDeliverCondition;
use app\models\Orders;
use app\models\Route;
use app\models\Users;
use app\models\Warehouse;
use Yii;
use yii\db\Expression;
use yii\web\Controller;
use yii\web\Session;
use function React\Promise\all;

class MapController extends Controller
{
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
    public function actionIndex()
    {
        $have_access = Users::checkPremission(53);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $sub_page = [];
        $date_tab = [];
        $users = Users::find()->where(['status' => 1])->asArray()->all();
        $session = Yii::$app->session;
        $userId = $session['user_id'];
        $deliver_ = '';
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
        $route_manager = Route::find()
            ->select('route.*');
            if ($session['role_id'] == 2 || $session['role_id'] == 3) {
            $route_manager->leftJoin('manager_deliver_condition', ['manager_deliver_condition.route_id' => new Expression('route.id')])
                ->andWhere(['manager_deliver_condition.manager_id' => $userId])
                ->andWhere(['manager_deliver_condition.status' => '1']);
            }
        $route = $route_manager->andWhere(['route.status' => '1'])
            ->asArray()
            ->all();
        $route_deliver = ManagerDeliverCondition::find()
            ->select('manager_deliver_condition.id, manager_deliver_condition.route_id, route.route')
            ->leftJoin('route','route.id = manager_deliver_condition.route_id')
            ->where(['manager_deliver_condition.deliver_id' => $userId])
            ->andWhere(['manager_deliver_condition.status'=> '1'])
            ->asArray()
            ->all();
        if ($session['role_id'] == '2') {
            $deliver_ = ManagerDeliverCondition::find()
                ->select('manager_deliver_condition.*, users.name')
                ->leftJoin('users', 'users.id = manager_deliver_condition.deliver_id')
                ->where(['manager_deliver_condition.manager_id' => $session['user_id']])
                ->asArray()
                ->all();
        }
        return $this->render('index', [
            'route' => $route,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,
            'users' => $users,
            'route_deliver' => $route_deliver,
            'managers_senders' => $managers_senders,
            'deliver_' => $deliver_,
        ]);
    }
    public function actionLocationValue()
    {
        if (isset($_GET) && $_GET['date'] != '') {
            $session = Yii::$app->session;
            $get = $this->request->get();
            $manager_id = 0;
            $warehouse = '';
;            $find_manager = null;
            $value = $get['locationvalue'];
            $valuedate =$get['date'];
            date_default_timezone_set('UTC');
            $formattedSelectedDate = Yii::$app->formatter->asDatetime($valuedate, 'yyyy-MM-dd');
            if (isset($get['manager'])){
                $manager_id = $get['manager'];
            }elseif (isset($get['araqich'])){
                $araqich_id = $get['araqich'];
                $find_manager = ManagerDeliverCondition::find()
                    ->select('manager_id')
                    ->where(['deliver_id' => $araqich_id])
                    ->andWhere(['status' => '1'])
                    ->asArray()
                    ->all();
                $find_manager = array_map(function($item) {
                    return $item['manager_id'];
                }, $find_manager);
            }
            $today_manager = Orders::find()
                ->select('user_id')
                ->andWhere(['and',['>=','orders_date', $formattedSelectedDate.' 00:00:00'],
                    ['<','orders_date', $formattedSelectedDate.' 23:59:59']])
                ->asArray()
                ->all();
            $today_manager = array_map(function($item) {
                return $item['user_id'];
            }, $today_manager);
            $countToday_manager = null;
            if (!is_null($find_manager)) {
                $countToday_manager = array_intersect($today_manager, $find_manager);
            }
            $locationsQuery = Orders::find()
                ->select([
                    'clients.id AS client_id',
                    'clients.name',
                    'clients.client_warehouse_id',
                    'clients.location',
                    'DATE_FORMAT(orders.orders_date, "%Y-%m-%d") AS orders_date'
                ])
                ->leftJoin('clients','clients.id = orders.clients_id');
            if (isset($_GET['deliver']) && $_GET['deliver'] != ''){
                $locationsQuery->leftJoin('documents', 'documents.orders_id = orders.id');
            }
            $locationsQuery->where(['route_id' => $value])
                ->andWhere(['and',['>=','orders.orders_date', $formattedSelectedDate.' 00:00:00'],
                    ['<','orders.orders_date', $formattedSelectedDate.' 23:59:59']])
                ->andWhere(['orders.status' => '1'])
                ->andWhere(['clients.status' => '1']);
            if (isset($_GET['deliver']) && $_GET['deliver'] != ''){
                $locationsQuery->andWhere(['documents.deliver_id' => $_GET['deliver']]);
            }
            if (!is_null($countToday_manager) && count($countToday_manager) == 1) {
                foreach ($countToday_manager as $key => $value)
                $manager_id = $value;
                $locationsQuery->andWhere(['=', 'orders.user_id', $manager_id]);
            }elseif(!is_null($countToday_manager) && count($countToday_manager) > 1) {
                $manager_id = $countToday_manager[0];
                $locationsQuery->andWhere(['=', 'orders.user_id', $manager_id]);
            }elseif(is_null($countToday_manager)) {
                $locationsQuery->andWhere(['=', 'orders.user_id', $manager_id]);
            }
            if (isset($get['araqich']) && $get['araqich'] != ''){
                $locationsQuery->leftJoin('documents', 'documents.orders_id = orders.id')
                    ->andWhere(['documents.deliver_id' => $get['araqich']]);
            }
            $locations = $locationsQuery
                ->asArray()
                ->orderBy('clients.sort_', SORT_DESC)
                ->all();

            $locationsQueryStatus_2 = Orders::find()
                ->select([
                    'clients.id AS client_id',
                    'clients.name',
                    'clients.client_warehouse_id',
                    'clients.location',
                    'DATE_FORMAT(orders.orders_date, "%Y-%m-%d") AS orders_date'
                ])
                ->leftJoin('clients','clients.id = orders.clients_id');
            if (isset($_GET['deliver']) && $_GET['deliver'] != ''){
                $locationsQueryStatus_2->leftJoin('documents', 'documents.orders_id = orders.id');
            }
            $locationsQueryStatus_2->where(['route_id' => $value])
                ->andWhere(['and',['>=','orders.orders_date', $formattedSelectedDate.' 00:00:00'],
                    ['<','orders.orders_date', $formattedSelectedDate.' 23:59:59']])
                ->andWhere(['orders.status' => '2'])
                ->andWhere(['clients.status' => '1']);
            if (isset($_GET['deliver']) && $_GET['deliver'] != ''){
                $locationsQueryStatus_2->andWhere(['documents.deliver_id' => $_GET['deliver']]);
            }
            if (!is_null($countToday_manager) && count($countToday_manager) == 1) {
                foreach ($countToday_manager as $key => $value)
                    $manager_id = $value;
                $locationsQueryStatus_2->andWhere(['=', 'orders.user_id', $manager_id]);
            }elseif(!is_null($countToday_manager) && count($countToday_manager) > 1) {
                $manager_id = $countToday_manager[0];
                $locationsQueryStatus_2->andWhere(['=', 'orders.user_id', $manager_id]);
            }elseif(is_null($countToday_manager)) {
                $locationsQuery->andWhere(['=', 'orders.user_id', $manager_id]);
            }
            if (isset($get['araqich']) && $get['araqich'] != ''){
                $locationsQueryStatus_2->leftJoin('documents', 'documents.orders_id = orders.id')
                    ->andWhere(['documents.deliver_id' => $get['araqich']]);
            }
            $locationsStatus_2 = $locationsQueryStatus_2
                ->asArray()
                ->orderBy('clients.sort_', SORT_DESC)
                ->all();
            if (count($locations) != 0) {
                if (count($locationsStatus_2) == '0') {
                    $warehouse = Warehouse::find()->select('location')
                        ->where(['id' => $locations[0]['client_warehouse_id']])
                        ->asArray()->one();
                }else{
                    $start_ = CoordinatesUser::find()
                        ->select('id, latitude, longitude');
                    if ($session['role_id'] == '2' || $session['role_id'] == '3') {
                        $start_->where(['user_id' => $session['user_id']]);
                    } elseif ($session['role_id'] == '1') {
                        $start_->where(['in', 'user_id', [$get['manager'], $get['deliver']]]);
                    }
                    $start_ = $start_->andWhere(['route_id' => $get['locationvalue']])
                        ->andWhere(['>=', 'created_at', $get['date'] . ' 00:00:00'])
                        ->andWhere(['<', 'created_at', $get['date'] . ' 23:59:59'])
                        ->orderBy(['created_at' => SORT_DESC])
                        ->groupBy('latitude')
                        ->asArray()
                        ->one();
                    $warehouse = ["location" => $start_['latitude'] . ',' . $start_['longitude']];
                }

                $locations = array_chunk($locations, 20);
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

                return ['location' => $locations, 'warehouse' => $warehouse, 'find_manager' => $find_manager, 'role_id' => $session['role_id']];
            }
        }
    }
    public function actionCoordinatesUser()
    {
        if ($this->request->isPost) {
            $session = Yii::$app->session;
            $post = $this->request->post();
            date_default_timezone_set('Asia/Yerevan');
            $model = new CoordinatesUser();
            if (isset($post['manager']) && $post['manager'] != '') {
                $model->user_id = $post['manager'];
            }elseif (isset($post['deliver']) && $post['deliver'] != '') {
                $model->user_id = $post['deliver'];
            }else{
                $model->user_id = $session['user_id'];
            }
            $model->route_id = $post['route_id'];
            $model->latitude = $post['myLatitude'];
            $model->longitude = $post['myLongitude'];
            if (isset($post['date']) && $post['date'] != '') {
                $model->created_at = $post['date'];
            }else{
                $model->created_at = date('Y-m-d H:i:s');
            }
            $model->save();
        }
    }

    public function actionWindowLoadData()
    {
        if ($this->request->get()) {
            $session = Yii::$app->session;
            date_default_timezone_set('Asia/Yerevan');
            if ($session['role_id'] == 2 || $session['role_id'] == 3) {
                if ($session['role_id'] == 2){
                    $manager_id = $session['user_id'];
                    $today = date('Y-m-d H:i:s');
                    $route = ManagerDeliverCondition::find()
                        ->select('route_id')
                        ->where(['manager_id' => $manager_id])
                        ->asArray()
                        ->one();
                    $route = $route['route_id'];
                    $response = Yii::$app->getResponse();
                    $response->format = yii\web\Response::FORMAT_JSON;
                    $response->data = ['manager_id' => $manager_id, 'today' => $today, 'route' => $route, 'role_id' => $session['role_id']];

                    return $response;
                }
                if ($session['role_id'] == 3){
                    $deliver_id = $session['user_id'];
                    $today = date('Y-m-d H:i:s');
                    $route = ManagerDeliverCondition::find()
                        ->select('route_id')
                        ->where(['deliver_id' => $deliver_id])
                        ->asArray()
                        ->one();
                    $route = $route['route_id'];
                    $response = Yii::$app->getResponse();
                    $response->format = yii\web\Response::FORMAT_JSON;
                    $response->data = ['deliver_id' => $deliver_id, 'today' => $today, 'route' => $route, 'role_id' => $session['role_id']];

                    return $response;
                }
            }
        }
    }
}
?>