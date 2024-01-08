<?php

namespace app\controllers;

use app\models\Log;
use app\models\Premissions;
use app\models\PremissionsSearch;
use app\models\Roles;
use app\models\Users;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PremissionsController implements the CRUD actions for Premissions model.
 */
class PremissionsController extends Controller
{
    /**
     * @inheritDoc
     */
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
     * Lists all Premissions models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $have_access = Users::checkPremission(36);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $sub_page = [
            ['name' => 'Օգտատեր','address' => '/users'],
            ['name' => 'Կարգավիճակ','address' => '/roles'],
        ];
        $date_tab = [];

        $searchModel = new PremissionsSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,

        ]);
    }

    /**
     * Displays a single Premissions model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $sub_page = [];
        $date_tab = [];
        if ($this->findModel($id)->status == 0) {
            return $this->redirect(Yii::$app->request->referrer);
        }
        return $this->render('view', [
            'model' => $this->findModel($id),
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,

        ]);
    }

    /**
     * Creates a new Premissions model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $have_access = Users::checkPremission(33);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $model = new Premissions();
        $sub_page = [];
        $date_tab = [];

        $url = Url::to('', 'http');
        $url = str_replace('create', 'view', $url);
        $premission = Premissions::find()
            ->select('name')
            ->where(['id' => 33])
            ->asArray()
            ->one();
        if ($this->request->isPost) {
            date_default_timezone_set('Asia/Yerevan');
            $post = $this->request->post();
            $model->role_id = $post['Premissions']['role_id'];
            $model->name = $post['Premissions']['name'];
            $model->created_at = date('Y-m-d H:i:s');
            $model->updated_at = date('Y-m-d H:i:s');
            $model = Premissions::getDefVals($model);
            $model->save();
            Log::afterSaves('Create', $model, '', $url.'?'.'id'.'='.$model->id, $premission);
                return $this->redirect(['index', 'id' => $model->id]);
        } else {
            $model->loadDefaultValues();
        }
        $roles = Roles::find()->select('id,name')->asArray()->all();
        $roles = ArrayHelper::map($roles,'id','name');
        return $this->render('create', [
            'model' => $model,
            'roles' => $roles,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,

        ]);
    }

    /**
     * Updates an existing Premissions model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $have_access = Users::checkPremission(34);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        if ($this->findModel($id)->status == 0) {
            return $this->redirect(Yii::$app->request->referrer);
        }
        $model = $this->findModel($id);
        $sub_page = [];
        $date_tab = [];

        $url = Url::to('', 'http');
        $oldattributes = Premissions::find()
            ->select('*')
            ->where(['id' => $id])
            ->asArray()
            ->one();
        $premission = Premissions::find()
            ->select('name')
            ->where(['id' => 34])
            ->asArray()
            ->one();
        if ($this->request->isPost) {
            date_default_timezone_set('Asia/Yerevan');
            $post = $this->request->post();
            $model->role_id = $post['Premissions']['role_id'];
            $model->name = $post['Premissions']['name'];
            $model->updated_at = date('Y-m-d H:i:s');
            $model->save();
            Log::afterSaves('Update', $model, $oldattributes, $url, $premission);
            return $this->redirect(['index', 'id' => $model->id]);
        }
        $roles = Roles::find()->select('id,name')->asArray()->all();
        $roles = ArrayHelper::map($roles,'id','name');
        return $this->render('update', [
            'model' => $model,
            'roles' => $roles,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,

        ]);
    }

    /**
     * Deletes an existing Premissions model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $have_access = Users::checkPremission(35);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $oldattributes = Premissions::find()
            ->select('name')
            ->where(['id' => $id])
            ->asArray()
            ->one();

        $premission = Premissions::find()
            ->select('name')
            ->where(['id' => 35])
            ->asArray()
            ->one();
        $premissions = Premissions::findOne($id);
        $premissions->status = '0';
        $premissions->save();
        Log::afterSaves('Delete', '', $oldattributes['name'], '#', $premission);
        return $this->redirect(['index']);
    }

    /**
     * Finds the Premissions model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Premissions the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Premissions::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
