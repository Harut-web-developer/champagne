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
        $all_manager = Users::find()->where(['and',['role_id' => '2'],['status' => 1]])->asArray()->all();
        $all_managers = Users::find()
            ->select('id')
            ->where(['and',['role_id' => '2'],['status' => 1]])
            ->asArray()
            ->all();
        $all_manager_ids = array_map(function ($all_managers) {
            return $all_managers['id'];
        }, $all_managers);
        $route = Route::find()->select('id, route')->asArray()->all();
        $session = Yii::$app->session;
        $userId = $session['user_id'];
        $route_deliver = ManagerDeliverCondition::find()
            ->select('manager_deliver_condition.id, manager_deliver_condition.route_id, route.route')
            ->leftJoin('route','route.id = manager_deliver_condition.route_id')
            ->where(['manager_deliver_condition.deliver_id' => $userId])
            ->andWhere(['manager_deliver_condition.status'=> '1'])
            ->asArray()
            ->all();
//        $route_deliver = array_map(function($item) {
//            return $item['route_id'];
//        }, $route_deliver);
//        echo "<pre>";
//        var_dump($route_deliver);
//        var_dump($userId);
//        die;
        return $this->render('index', [
            'route' => $route,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,
            'all_manager' => $all_manager,
            'all_manager_ids' => $all_manager_ids,
            'route_deliver' => $route_deliver,
        ]);
    }
    public function actionLocationValue()
    {
        if (isset($_GET)) {
//            $session = Yii::$app->session;
//            $userId = $session['user_id'];
            $get = $this->request->get();
            $manager_id = 0;
            $find_manager = null;
            $value = $get['locationvalue'];
            $valuedate =$get['date'];
            date_default_timezone_set('UTC');
            $warehouse = Warehouse::find()->select('location')->where(['id' => 1])->asArray()->one();
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
            $locationsQuery  = Orders::find()
                ->select(["clients.location", 'DATE_FORMAT(orders.orders_date, "%Y-%m-%d") as orders_date'])
                ->leftJoin('clients','clients.id = orders.clients_id')
                ->where(['route_id' => $value])
                ->andWhere(['and',['>=','orders.orders_date', $formattedSelectedDate.' 00:00:00'],
                    ['<','orders.orders_date', $formattedSelectedDate.' 23:59:59']])
                ->andWhere(['orders.status' => '1']);
            if (!is_null($countToday_manager) && count($countToday_manager) == 1) {
                $manager_id = $countToday_manager[0];
                $locationsQuery->andWhere(['=', 'orders.user_id', $manager_id]);
            }elseif(!is_null($countToday_manager) && count($countToday_manager) > 1) {
                $manager_id = $countToday_manager[0];
                $locationsQuery->andWhere(['=', 'orders.user_id', $manager_id]);
            }elseif(is_null($countToday_manager)) {
                $locationsQuery->andWhere(['=', 'orders.user_id', $manager_id]);
            }
//            die;
            $locations = $locationsQuery
                ->asArray()
                ->orderBy('clients.sort_', SORT_DESC)
                ->all();
            $locations = array_chunk($locations,20);
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            return ['location' => $locations, 'warehouse' => $warehouse, 'find_manager' => $find_manager];
        }
    }
    public function actionCoordinatesUser()
    {
        if ($this->request->isPost) {
            $session = Yii::$app->session;
            $post = $this->request->post();
            date_default_timezone_set('Asia/Yerevan');
            $model = new CoordinatesUser();
            $model->user_id = $session['user_id'];
            $model->route_id = $post['route_id'];
            $model->latitude = $post['myLatitude'];
            $model->longitude = $post['myLongitude'];
            $model->created_at = date('Y-m-d H:i:s');
            $model->save();
        }
    }
}
?>