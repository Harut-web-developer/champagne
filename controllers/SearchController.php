<?php
namespace app\controllers;

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
        $have_access = Users::checkPremission(53);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        return $this->render('index');
    }

//    public function actionSearching(){
//        if (Yii::$app->request->isAjax && Yii::$app->request->post('option')) {
//            $option = Yii::$app->request->post('option');
//            $query_product = Product::find()
//                ->select('id , name, description, keyword')
//                ->orWhere(['like', 'name', $option])
//                ->orWhere(['like', 'description' , $option])
//                ->orWhere(['like', 'keyword', $option])
//                ->asArray()->all();
//            $query_category = Yii::$app->db->createCommand('SELECT id , name FROM category WHERE name LIKE :option')
//                ->bindValue(':option', '%' . $option . '%')
//                ->queryAll();
//            $res = [];
//            $res['query_product'] = $query_product;
//            $res['query_category'] = $query_category;
//            return json_encode($res);
//        }
//    }
}