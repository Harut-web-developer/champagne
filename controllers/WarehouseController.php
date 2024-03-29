<?php

namespace app\controllers;

use app\models\Clients;
use app\models\Log;
use app\models\Premissions;
use app\models\Users;
use app\models\Warehouse;
use app\models\WarehouseSearch;
use yii\helpers\Url;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * WarehouseController implements the CRUD actions for Warehouse model.
 */
class WarehouseController extends Controller
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
     * Lists all Warehouse models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $session = Yii::$app->session;
        $have_access = Users::checkPremission(4);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $res = Yii::$app->runAction('custom-fields/get-table-data',['page'=>'warehouse']);

            $sub_page = [];
        if (Users::checkPremission(40)){
            $documents = ['name' => 'Փաստաթղթեր','address' => '/documents'];
            array_push($sub_page,$documents);
        }
        if (Users::checkPremission(12)){
            $nom = ['name' => 'Անվանակարգ','address' => '/nomenclature'];
            array_push($sub_page,$nom);
        }
        if (Users::checkPremission(20)){
            $prod = ['name' => 'Ապրանք','address' => '/products'];
            array_push($sub_page,$prod);
        }
        if (Users::checkPremission(28)){
            $log = ['name' => 'Տեղեկամատյան','address' => '/log'];
            array_push($sub_page,$log);
        }

        $date_tab = [];

        $searchModel = new WarehouseSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'new_fields'=>$res,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,

        ]);
    }

    /**
     * Displays a single Warehouse model.
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
     * Creates a new Warehouse model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $have_access = Users::checkPremission(1);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $sub_page = [];
        $date_tab = [];

        $model = new Warehouse();
        $url = Url::to('', 'http');
        $url = str_replace('create', 'view', $url);
        $premission = Premissions::find()
            ->select('name')
            ->where(['id' => 1])
            ->asArray()
            ->one();
        if ($this->request->isPost) {
            $post = $this->request->post();
//            echo "<pre>";
//            var_dump($post);
//            exit();
            date_default_timezone_set('Asia/Yerevan');

            $model->name = $post['Warehouse']['name'];
            $model->location = $post['Warehouse']['location'];
            $model->type = $post['Warehouse']['type'];
            $model->created_at = date('Y-m-d H:i:s');
            $model->updated_at = date('Y-m-d H:i:s');
            $model = Warehouse::getDefVals($model);
            $model->save();
            Log::afterSaves('Create', $model, '', $url.'?'.'id'.'='.$model->id, $premission);
            $_POST['item_id'] = $model->id;
            if($post['newblocks'] || $post['new_fild_name']){
                Yii::$app->runAction('custom-fields/create-title',$post);
            }
            return $this->redirect(['index', 'id' => $model->id]);
        } else {
            $model->loadDefaultValues();
        }
        return $this->render('create', [
            'model' => $model,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,

        ]);
    }

    public function actionCreateFields()
    {
        $have_access = Users::checkPremission(73);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $sub_page = [];
        if (Users::checkPremission(4)){
            $warehouse = ['name' => 'Պահեստ','address' => '/warehouse'];
            array_push($sub_page,$warehouse);
        }
        if (Users::checkPremission(40)){
            $documents = ['name' => 'Փաստաթղթեր','address' => '/documents'];
            array_push($sub_page,$documents);
        }
        if (Users::checkPremission(12)){
            $nom = ['name' => 'Անվանակարգ','address' => '/nomenclature'];
            array_push($sub_page,$nom);
        }
        if (Users::checkPremission(20)){
            $prod = ['name' => 'Ապրանք','address' => '/products'];
            array_push($sub_page,$prod);
        }
        if (Users::checkPremission(28)){
            $log = ['name' => 'Տեղեկամատյան','address' => '/log'];
            array_push($sub_page,$log);
        }
        $date_tab = [];

        $model = new Warehouse();
        if ($this->request->isPost) {
            $post = $this->request->post();

            if($post['newblocks'] || $post['new_fild_name']){

                Yii::$app->runAction('custom-fields/create-title',$post);
            }
            return $this->redirect(['index']);
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create-fields', [
            'model' => $model,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,

        ]);
    }
    /**
     * Updates an existing Warehouse model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $have_access = Users::checkPremission(2);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $model = $this->findModel($id);
        $sub_page = [];
        $date_tab = [];
        if ($this->findModel($id)->status == 0) {
            return $this->redirect(Yii::$app->request->referrer);
        }
        $url = Url::to('', 'http');
        $oldattributes = Warehouse::find()
            ->select('*')
            ->where(['id' => $id])
            ->asArray()
            ->one();
        $premission = Premissions::find()
            ->select('name')
            ->where(['id' => 2])
            ->asArray()
            ->one();
        if ($this->request->isPost) {
            date_default_timezone_set('Asia/Yerevan');
            $post = $this->request->post();
            $model->name = $post['Warehouse']['name'];
            $model->location = $post['Warehouse']['location'];
            $model->type = $post['Warehouse']['type'];
            $model->updated_at = date('Y-m-d H:i:s');
            $model->save();
            Log::afterSaves('Update', $model, $oldattributes, $url, $premission);
            $_POST['item_id'] = $model->id;

            if($post['newblocks'] || $post['new_fild_name']){
                Yii::$app->runAction('custom-fields/create-title',$post);
            }
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,

        ]);
    }

    /**
     * Deletes an existing Warehouse model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $have_access = Users::checkPremission(3);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $oldattributes = Warehouse::find()
            ->select('name')
            ->where(['id' => $id])
            ->asArray()
            ->one();

        $premission = Premissions::find()
            ->select('name')
            ->where(['id' => 3])
            ->asArray()
            ->one();
        $warehouse = Warehouse::findOne($id);
        $warehouse->status = '0';
        $warehouse->save();
        Log::afterSaves('Delete', '', $oldattributes['name'], '#', $premission);
        return $this->redirect(['index']);
    }
    public function actionCoordsLocation()
    {
        if ($this->request->isPost) {
            $post = $this->request->post();
            $latlong = $post['coords'][0].','.$post['coords'][1];
            return json_encode($latlong);
        }
    }
    /**
     * Finds the Warehouse model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Warehouse the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Warehouse::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
