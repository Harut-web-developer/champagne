<?php
namespace app\controllers;

use app\models\Clients;
use app\models\Orders;
use app\models\Route;
use Yii;
use yii\web\Controller;

class MapController extends Controller
{
    public function actionIndex()
    {
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