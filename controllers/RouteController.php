<?php

namespace app\controllers;

use app\models\Clients;
use app\models\Log;
use app\models\Orders;
use app\models\Premissions;
use app\models\Route;
use app\models\RouteSearch;
use app\models\Users;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use function PHPUnit\Framework\isFalse;

/**
 * RouteController implements the CRUD actions for Route model.
 */
class RouteController extends Controller
{
    /**
     * @inheritDoc
     */
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
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }
//    public function behaviors()
//    {
//        return array_merge(
//            parent::behaviors(),
//            [
//                'verbs' => [
//                    'class' => VerbFilter::className(),
//                    'actions' => [
//                        'delete' => ['POST'],
//                    ],
//                ],
//            ]
//        );
//    }

    /**
     * Lists all Route models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $have_access = Users::checkPremission(52);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $sub_page = [];
        $date_tab = [];

        $searchModel = new RouteSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,

        ]);
    }

    /**
     * Displays a single Route model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView()
    {
        $id = Yii::$app->request->get('id');
        $have_access = Users::checkPremission(54);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $sub_page = [];
        $date_tab = [];

        return $this->render('route-view', [
            'model' => $this->findModel($id),
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,

        ]);
    }
    public function actionRouteSort()
    {
        $id = Yii::$app->request->get('id');
        $have_access = Users::checkPremission(54);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $sub_page = [];
        $date_tab = [];

        $result = Clients::find()
            ->select('id,name')
            ->where(['=', 'route_id', intval($id)])
            ->andwhere(['status' => 1])
            ->orderBy(['sort_'=> SORT_ASC])
            ->asArray()
            ->all();

        return $this->render('route-sort', [
            'model' => $this->findModel($id),
            'result' => $result,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,

//            'alldata' => $alldata,
        ]);
    }


    public function actionSave()
    {
        $db = Yii::$app->db;
        if ($this->request->isPost) {
            if (!empty($_POST['sort'])) {
                foreach ($_POST['sort'] as $i => $row) {
                    $client = Clients::findOne($row);
                    $client->sort_ = $i;
                    $client->save(false);
                }
            }
            return 'success';
        } else {
            return 'error';
        }
    }

    /**
     * Creates a new Route model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $have_access = Users::checkPremission(49);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $model = new Route();
        $sub_page = [];
        $date_tab = [];

        $url = Url::to('', 'http');
        $url = str_replace('create', 'view', $url);
        $premission = Premissions::find()
            ->select('name')
            ->where(['id' => 49])
            ->asArray()
            ->one();
        $model = Route::getDefVals($model);
        if ($this->request->isPost) {
            date_default_timezone_set('Asia/Yerevan');
            $model->created_at = date('Y-m-d H:i:s');
            $model->updated_at = date('Y-m-d H:i:s');
            if ($model->load($this->request->post()) && $model->save()) {
                Log::afterSaves('Create', $model, '', $url.'?'.'id'.'='.$model->id, $premission);
                return $this->redirect('index');
            }
        } else {
            $model->loadDefaultValues();
        }
//        $route = Route::find()->select('id, route')->asArray()->all();

        return $this->render('create', [
            'model' => $model,
//            'route' => $route,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,

        ]);
    }

    /**
     * Updates an existing Route model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $have_access = Users::checkPremission(50);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $model = $this->findModel($id);
        $sub_page = [];
        $date_tab = [];

        $url = Url::to('', 'http');
        $oldattributes = Route::find()
            ->select('*')
            ->where(['id' => $id])
            ->asArray()
            ->one();
        $premission = Premissions::find()
            ->select('name')
            ->where(['id' => 50])
            ->asArray()
            ->one();
        if ($this->request->isPost){
            date_default_timezone_set('Asia/Yerevan');
            $model->updated_at = date('Y-m-d H:i:s');
            if($model->load($this->request->post()) && $model->save()) {
                Log::afterSaves('Update', $model, $oldattributes, $url, $premission);
                return $this->redirect('index');
            }
        }


        return $this->render('update', [
            'model' => $model,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,

        ]);
    }

    /**
     * Deletes an existing Route model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $have_access = Users::checkPremission(51);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $oldattributes = Route::find()
            ->select('route')
            ->where(['id' => $id])
            ->asArray()
            ->one();

        $premission = Premissions::find()
            ->select('name')
            ->where(['id' => 51])
            ->asArray()
            ->one();
        $this->findModel($id)->delete();
        Log::afterSaves('Delete', '', $oldattributes['route'], '#', $premission);
        return $this->redirect(['index']);
    }

    /**
     * Finds the Route model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Route the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Route::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
