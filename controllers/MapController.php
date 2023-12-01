<?php
namespace app\controllers;

use app\models\Clients;
use app\models\Orders;
use app\models\Route;
use app\models\Users;
use Yii;
use yii\web\Controller;

class MapController extends Controller
{
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
        $route = Route::find()->select('id, route')->asArray()->all();
        return $this->render('index', [
            'route' => $route,
        ]);
    }
    public function actionLocationValue()
    {

        if (isset($_GET)) {
            $get = $this->request->get();
            $value = $get['locationvalue'];
            $locations = Orders::find()->select("clients.location")
                ->leftJoin('clients','clients.id = orders.clients_id')
                ->where(['route_id' => $value])
                ->asArray()
                ->all();
            return json_encode($locations);
        }
    }
}
?>