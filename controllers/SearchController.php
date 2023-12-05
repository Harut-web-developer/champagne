<?php
namespace app\controllers;

use app\models\Clients;
use app\models\Nomenclature;
use yii;
use yii\web\Controller;
use app\models\Users;

class SearchController extends Controller{
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
        $res = [];
        $have_access = Users::checkPremission(53);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        if (isset($_GET)) {
            $searchval = $_GET['searchQuery'];
            $query_nomenclature = Nomenclature::find()
                ->select('id, name')
                ->Where(['like', 'name', $searchval])
                ->asArray()->all();
            $query_users = Users::find()
                ->select('id, name')
                ->Where(['like', 'name', $searchval])
                ->asArray()->all();
            $query_clients = Clients::find()
                ->select('id, name')
                ->Where(['like', 'name', $searchval])
                ->asArray()->all();
            $res['query_nomenclature'] = $query_nomenclature;
            $res['query_users'] = $query_users;
            $res['query_clients'] = $query_clients;
            return $this->render('index',['res'=> $res]);
        }
        return $this->render('index');
    }
}