<?php

namespace app\controllers;

use app\models\Rates;
use app\models\RatesSearch;
use app\models\Users;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * RatesController implements the CRUD actions for Rates model.
 */
class RatesController extends Controller
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
     * Lists all Rates models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $have_access = Users::checkPremission(48);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $searchModel = new RatesSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Rates model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Rates model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $have_access = Users::checkPremission(45);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $model = new Rates();
        if ($this->request->isPost) {
            $post = $this->request->post();
            date_default_timezone_set('Asia/Yerevan');
            $model->name = $post['Rates']['name'];
            $model->created_at = date('Y-m-d H:i:s');
            $model->updated_at = date('Y-m-d H:i:s');
            $model->save();
            return $this->redirect(['index', 'id' => $model->id]);
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Rates model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $have_access = Users::checkPremission(46);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $model = $this->findModel($id);

        if ($this->request->isPost) {
            $post = $this->request->post();
            date_default_timezone_set('Asia/Yerevan');
            $model->name = $post['Rates']['name'];
            $model->updated_at = date('Y-m-d H:i:s');
            $model->save();
            return $this->redirect(['index', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Rates model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $have_access = Users::checkPremission(47);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $rates = Rates::findOne($id);
        $rates->status = '0';
        $rates->save();
        return $this->redirect(['index']);
    }

    /**
     * Finds the Rates model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Rates the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Rates::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}