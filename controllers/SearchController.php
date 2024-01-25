<?php
namespace app\controllers;

use app\models\Clients;
use app\models\Nomenclature;
use yii;
use yii\web\Controller;
use app\models\Users;

class SearchController extends Controller{
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
        $have_access = Users::checkPremission(67);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $res = [];
        $sub_page = [];
        $date_tab = [];

        if (isset($_GET)) {
            $searchval = $_GET['searchQuery'];
            $query_nomenclature = Nomenclature::find()
                ->select('id, name')
                ->where(['like', 'name', $searchval])
                ->andWhere(['status' => '1'])
                ->asArray()
                ->all();
            $query_users = Users::find()
                ->select('id, name')
                ->where(['like', 'name', $searchval])
                ->andWhere(['status' => '1'])
                ->asArray()->all();
            $query_clients = Clients::find()
                ->select('id, name')
                ->where(['like', 'name', $searchval])
                ->andWhere(['status' => '1'])
                ->asArray()->all();

            $res['query_nomenclature'] = $query_nomenclature;
            $res['query_users'] = $query_users;
            $res['query_clients'] = $query_clients;
            return $this->render('index',[
                'res'=> $res,
                'sub_page' => $sub_page,
                'date_tab' => $date_tab,

            ]);
        }
        return $this->render('index');
    }
}