<?php

namespace app\controllers;

use app\models\Users;
use app\models\Warehouse;
use app\models\WarehouseSearch;
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
        $have_access = Users::checkPremission(4);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $res = Yii::$app->runAction('custom-fields/get-table-data',['page'=>'warehouse']);
        $sub_page = [
            ['name' => 'Փաստաթղթեր','address' => '/documents'],
            ['name' => 'Անվանակարգ','address' => '/nomenclature'],
            ['name' => 'Ապրանք','address' => '/products'],
            ['name' => 'Տեղեկամատյան','address' => '/log'],

        ];
        $searchModel = new WarehouseSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'new_fields'=>$res,
            'sub_page' => $sub_page
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
        return $this->render('view', [
            'model' => $this->findModel($id),
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
        $model = new Warehouse();
        if ($this->request->isPost) {
            $post = $this->request->post();
            date_default_timezone_set('Asia/Yerevan');

            $model->name = $post['Warehouse']['name'];
            $model->location = $post['Warehouse']['location'];
            $model->type = $post['Warehouse']['type'];
            $model->created_at = date('Y-m-d H:i:s');
            $model->updated_at = date('Y-m-d H:i:s');
            $model->save();
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
        ]);
    }

    public function actionCreateFields()
    {
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

        if ($this->request->isPost) {
            date_default_timezone_set('Asia/Yerevan');
            $post = $this->request->post();
            $model->name = $post['Warehouse']['name'];
            $model->location = $post['Warehouse']['location'];
            $model->type = $post['Warehouse']['type'];
            $model->updated_at = date('Y-m-d H:i:s');
            $model->save();
            $_POST['item_id'] = $model->id;

            if($post['newblocks'] || $post['new_fild_name']){
                Yii::$app->runAction('custom-fields/create-title',$post);
            }
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
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
        $warehouse = Warehouse::findOne($id);
        $warehouse->status = '0';
        $warehouse->save();
        return $this->redirect(['index']);
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
