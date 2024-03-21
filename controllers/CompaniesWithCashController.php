<?php

namespace app\controllers;

use app\models\Log;
use app\models\Premissions;
use app\models\Users;
use  Yii;
use app\models\CompaniesWithCash;
use app\models\CompaniesWithCashSearch;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CompaniesWithCashController implements the CRUD actions for CompaniesWithCash model.
 */
class CompaniesWithCashController extends Controller
{
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
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all CompaniesWithCash models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $have_access = Users::checkPremission(89);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $searchModel = new CompaniesWithCashSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $sub_page = [];
        if (Users::checkPremission(61)){
            $groups_discount = ['name' => 'Զեղչի խմբեր','address' => '/groups-name'];
            array_push($sub_page,$groups_discount);
        }
        if (Users::checkPremission(85)){
            $branches = ['name' => 'Մասնաճյուղի Խմբեր','address' => '/branch-groups'];
            array_push($sub_page,$branches);
        }
        if (Users::checkPremission(8)){
            $clients = ['name' => 'Հաճախորդներ','address' => '/clients'];
            array_push($sub_page,$clients);
        }
        $date_tab = [];
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,
        ]);
    }

    /**
     * Displays a single CompaniesWithCash model.
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
     * Creates a new CompaniesWithCash model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $have_access = Users::checkPremission(86);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $model = new CompaniesWithCash();
        $url = Url::to('', 'http');
        $url = str_replace('create', 'view', $url);
        $premission = Premissions::find()
            ->select('name')
            ->where(['id' => 86])
            ->andWhere(['status' => 1])
            ->asArray()
            ->one();
        if ($this->request->isPost) {
            date_default_timezone_set('Asia/Yerevan');
            $post = $this->request->post();
            $model->name = $post['CompaniesWithCash']['name'];
            $model->created_at = date('Y-m-d H:i:s');
            $model->updated_at = date('Y-m-d H:i:s');
            $model->save();
            Log::afterSaves('Create', $model, '', $url.'?'.'id'.'='.$model->id, $premission);
            return $this->redirect(['index', 'id' => $model->id]);
        } else {
            $model->loadDefaultValues();
        }
        $sub_page = [
            ['name' => 'Խմբեր','address' => '/groups-name'],
        ];
        $date_tab = [];
        return $this->render('create', [
            'model' => $model,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,
        ]);
    }

    /**
     * Updates an existing CompaniesWithCash model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $have_access = Users::checkPremission(87);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $model = $this->findModel($id);
        if ($this->findModel($id)->status == 0) {
            return $this->redirect(Yii::$app->request->referrer);
        }
        $url = Url::to('', 'http');
        $oldattributes = CompaniesWithCash::find()
            ->select('*')
            ->where(['id' => $id])
            ->andWhere(['status' => 1])
            ->asArray()
            ->one();
        $premission = Premissions::find()
            ->select('name')
            ->where(['id' => 87])
            ->andWhere(['status' => 1])
            ->asArray()
            ->one();
        if ($this->request->isPost) {
            date_default_timezone_set('Asia/Yerevan');
            $post = $this->request->post();
            $model->name = $post['CompaniesWithCash']['name'];
            $model->updated_at = date('Y-m-d H:i:s');
            $model->save();
            Log::afterSaves('Update', $model, $oldattributes, $url, $premission);
            return $this->redirect(['index', 'id' => $model->id]);
        }
        $sub_page = [
            ['name' => 'Խմբեր','address' => '/groups-name'],
        ];
        $date_tab = [];
        return $this->render('update', [
            'model' => $model,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,
        ]);
    }

    /**
     * Deletes an existing CompaniesWithCash model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $have_access = Users::checkPremission(88);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $oldattributes = CompaniesWithCash::find()
            ->select('name')
            ->where(['id' => $id])
            ->andWhere(['status' => 1])
            ->asArray()
            ->one();

        $premission = Premissions::find()
            ->select('name')
            ->where(['id' => 88])
            ->andWhere(['status' => 1])
            ->asArray()
            ->one();
        $companies = CompaniesWithCash::findOne($id);
        $companies->status = '0';
        $companies->save(false);
        Log::afterSaves('Delete', '', $oldattributes['name'], '#', $premission);
        return $this->redirect(['index']);
    }

    /**
     * Finds the CompaniesWithCash model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return CompaniesWithCash the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CompaniesWithCash::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
