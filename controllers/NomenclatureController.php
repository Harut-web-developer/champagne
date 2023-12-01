<?php

namespace app\controllers;

use app\models\Discount;
use Yii;
use app\models\Nomenclature;
use app\models\NomenclatureSearch;
use app\models\Users;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * NomenclatureController implements the CRUD actions for Nomenclature model.
 */
class NomenclatureController extends Controller
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
     * Lists all Nomenclature models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $have_access = Users::checkPremission(12);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $searchModel = new NomenclatureSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Nomenclature model.
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
     * Creates a new Nomenclature model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $have_access = Users::checkPremission(9);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $model = new Nomenclature();

        if ($this->request->isPost) {
            date_default_timezone_set('Asia/Yerevan');
            $post = $this->request->post();
            $model->name = $post['Nomenclature']['name'];
            $model->cost = intval($post['Nomenclature']['cost']);
            $model->price = intval($post['Nomenclature']['price']);
            $model->created_at = date('Y-m-d H:i:s');
            $model->updated_at = date('Y-m-d H:i:s');
            $model->save();
            $_POST['item_id'] = $model->id;
            if($post['newblocks'] || $post['new_fild_name']){
                Yii::$app->runAction('custom-fields/create-title',$post);
            }
            return $this->redirect(['create', 'id' => $model->id]);
        } else {
            $model->loadDefaultValues();
        }
        $discounts = Discount::find()->select('id,discount')->asArray()->all();
        return $this->render('create', [
            'model' => $model,
            'discounts' => $discounts
        ]);
    }

    public function actionCreateFields()
    {
        $model = new Nomenclature();
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
     * Updates an existing Nomenclature model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $have_access = Users::checkPremission(10);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $model = $this->findModel($id);

        if ($this->request->isPost) {
            date_default_timezone_set('Asia/Yerevan');
            $post = $this->request->post();
            $model->name = $post['Nomenclature']['name'];
            $model->cost = intval($post['Nomenclature']['cost']);
            $model->price = $post['Nomenclature']['price'];
            $model->updated_at = date('Y-m-d H:i:s');
            $model->save();
            $_POST['item_id'] = $model->id;
            if($post['newblocks'] || $post['new_fild_name']){
                Yii::$app->runAction('custom-fields/create-title',$post);
            }
            return $this->redirect(['create', 'id' => $model->id]);
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Nomenclature model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $have_access = Users::checkPremission(11);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $nomenclature = Nomenclature::findOne($id);
        $nomenclature->status = '0';
        $nomenclature->save();
        return $this->redirect(['index']);
    }

    /**
     * Finds the Nomenclature model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Nomenclature the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Nomenclature::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
