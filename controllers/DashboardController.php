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
    public function actionIndex(){
        $sub_page = [];
        return $this->render('index',[
            'sub_page' => $sub_page
        ]);
    }

    public function actionMiniChart(){
        if ($this->request->isPost){
            $post = $this->request->post();
            var_dump($post);
        }
    }
}