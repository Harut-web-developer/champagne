<?php

namespace app\controllers;

use Yii;
use app\models\Log;
use app\models\LogSearch;
use app\models\Users;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * LogController implements the CRUD actions for Log model.
 */
class LogController extends Controller
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
//        else if ($action->id == 'forgot-password'){
//            return  $this->redirect('site/forgot-password');
//        }
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
     * Lists all Log models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $have_access = Users::checkPremission(28);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $sub_page = [
            ['name' => 'Պահեստ','address' => '/warehouse'],
            ['name' => 'Փաստաթղթեր','address' => '/documents'],
            ['name' => 'Անվանակարգ','address' => '/nomenclature'],
            ['name' => 'Ապրանք','address' => '/products'],
        ];
        $searchModel = new LogSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'sub_page' => $sub_page
        ]);
    }

    /**
     * Displays a single Log model.
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
     * Creates a new Log model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
//        $have_access = Users::checkPremission(25);
//        if(!$have_access){
//            $this->redirect('/site/403');
//        }
//        $model = new Log();
//        $sub_page = [];
////        if ($this->request->isPost) {
////            date_default_timezone_set('Asia/Yerevan');
////            $post = $this->request->post();
////            $model->user_id = $post['Log']['user_id'];
////            $model->action = $post['Log']['action'];
////            $model->create_date = date('Y-m-d H:i:s');
////                return $this->redirect(['index', 'id' => $model->id]);
////        } else {
////            $model->loadDefaultValues();
////        }
//        $log = Users::find()->select('id,name')->asArray()->all();
//        $log = ArrayHelper::map($log,'id','name');
//        return $this->render('create', [
//            'model' => $model,
//            'log' => $log,
//            'sub_page' => $sub_page
//        ]);

    }

    /**
     * Updates an existing Log model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $have_access = Users::checkPremission(26);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $model = $this->findModel($id);
        $sub_page = [];
        if ($this->request->isPost) {
            date_default_timezone_set('Asia/Yerevan');
            $post = $this->request->post();
            $model->user_id = $post['Log']['user_id'];
            $model->action = $post['Log']['action'];
            $model->create_date = date('Y-m-d H:i:s');
            return $this->redirect(['index', 'id' => $model->id]);
        }
        $log = Users::find()->select('id,name')->asArray()->all();
        $log = ArrayHelper::map($log,'id','name');
        return $this->render('update', [
            'model' => $model,
            'log' => $log,
            'sub_page' => $sub_page
        ]);
    }

    /**
     * Deletes an existing Log model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $have_access = Users::checkPremission(27);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $log = Log::findOne($id);
        $log->save();
        return $this->redirect(['index']);
    }

    /**
     * Finds the Log model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Log the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Log::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
