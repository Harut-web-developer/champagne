<?php
namespace app\controllers;

use app\models\Clients;
use app\models\CoordinatesUser;
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

//        if(isset($_POST)){
//            $result = 'post ka';
//            return json_encode(['result' => $result]);
//        }
        $route = Route::find()->select('id, route')->asArray()->all();
        return $this->render('index', [
            'route' => $route,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,

        ]);
    }
    public function actionLocationValue()
    {
        if (isset($_GET)) {
            $session = Yii::$app->session;
            $userId = $session['user_id'];
            $get = $this->request->get();
            $value = $get['locationvalue'];
            $valuedate =$get['date'];
            date_default_timezone_set('UTC');
            $warehouse = Warehouse::find()->select('location')->where(['id' => 1])->asArray()->one();
            $formattedSelectedDate = Yii::$app->formatter->asDatetime($valuedate, 'yyyy-MM-dd');
            $locations = Orders::find()
                ->select(["clients.location", 'DATE_FORMAT(orders.orders_date, "%Y-%m-%d") as orders_date'])
                ->leftJoin('clients','clients.id = orders.clients_id')
                ->where(['route_id' => $value])
                ->andWhere(['and',['>=','orders.orders_date', $formattedSelectedDate.' 00:00:00'],
                    ['<','orders.orders_date', $formattedSelectedDate.' 23:59:59']])
                ->andWhere(['orders.status' => '1'])
//                ->andwhere(['=', 'orders.user_id', $userId])
                ->asArray()
                ->orderBy('clients.sort_',SORT_DESC)
                ->all();
            return json_encode(['location' => $locations, 'warehouse' => $warehouse]);
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
            $model->latitude = $post['myLatitude'];
            $model->longitude = $post['myLongitude'];
            $model->created_at = date('Y-m-d H:i:s');
            $model->save();
        }
    }
}
?>