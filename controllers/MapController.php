<?php
namespace app\controllers;

use app\models\Clients;
use app\models\Orders;
use app\models\Route;
use app\models\Warehouse;
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
            $valuedate =$get['date'];
            date_default_timezone_set('UTC');
            $warehouse = Warehouse::find()->select('location')->where(['id' => 1])->asArray()->one();
            $formattedSelectedDate = Yii::$app->formatter->asDatetime($valuedate, 'yyyy-MM-dd');
            $locations = Orders::find()
                ->select(["clients.location", 'DATE_FORMAT(orders.updated_at, "%Y-%m-%d") as updated_at'])
                ->leftJoin('clients','clients.id = orders.clients_id')
                ->where(['route_id' => $value])
                ->andWhere(['and',['>=','orders.updated_at', $formattedSelectedDate.' 00:00:00'],
                    ['<','orders.updated_at', $formattedSelectedDate.' 23:59:59']])
                ->asArray()
                ->orderBy('clients.sort_',SORT_DESC)
                ->all();
            return json_encode(['location' => $locations, 'warehouse' => $warehouse]);
        }
    }
}
?>