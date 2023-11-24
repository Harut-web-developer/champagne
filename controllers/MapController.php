<?php
namespace app\controllers;

use app\models\MapForm;
use Yii;
use yii\web\Controller;

class MapController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }
}
?>