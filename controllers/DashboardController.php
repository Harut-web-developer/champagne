<?php

namespace app\controllers;

use app\models\Clients;
use app\models\Nomenclature;
use app\models\OrderItems;
use app\models\Orders;
use app\models\OrdersSearch;
use app\models\Products;
use app\models\Users;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OrdersController implements the CRUD actions for Orders model.
 */
class DashboardController extends Controller
{
    public function actionIndex(){
        $sub_page = [];
        return $this->render('index',[
            'sub_page' => $sub_page
        ]);
    }
}