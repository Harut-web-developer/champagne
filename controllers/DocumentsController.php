<?php

namespace app\controllers;

use app\models\Documents;
use app\models\DocumentsSearch;
use app\models\Rates;
use app\models\Users;
use app\models\Warehouse;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * DocumentsController implements the CRUD actions for Documents model.
 */
class DocumentsController extends Controller
{
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
     * Lists all Documents models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new DocumentsSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Documents model.
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
     * Creates a new Documents model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
//        echo "<pre>";
        $model = new Documents();

        if ($this->request->isPost) {
            $post = $this->request->post();
            date_default_timezone_set('Asia/Yerevan');
            $model->user_id = $post['Documents']['user_id'];
            $model->warehouse_id = $post['Documents']['warehouse_id'];
            $model->rate_id = $post['Documents']['rate_id'];
            $model->comment = $post['Documents']['comment'];
            $model->date = $post['Documents']['date'];
            $model->created_at = date('Y-m-d H:i:s');
            $model->updated_at = date('Y-m-d H:i:s');
            $model->save();
                return $this->redirect(['index', 'id' => $model->id]);
        } else {
            $model->loadDefaultValues();
        }
        $users = Users::find()->select('id,name')->asArray()->all();
        $users = ArrayHelper::map($users,'id','name');
        $warehouse = Warehouse::find()->select('id,name')->asArray()->all();
        $warehouse =  ArrayHelper::map($warehouse,'id','name');
        $rates = Rates::find()->select('id,name')->asArray()->all();
        $rates = ArrayHelper::map($rates,'id','name');
        return $this->render('create', [
            'model' => $model,
            'users' => $users,
            'warehouse' => $warehouse,
            'rates' => $rates
        ]);
    }

    /**
     * Updates an existing Documents model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost) {
            $post = $this->request->post();
            date_default_timezone_set('Asia/Yerevan');
            $model->user_id = $post['Documents']['user_id'];
            $model->warehouse_id = $post['Documents']['warehouse_id'];
            $model->rate_id = $post['Documents']['rate_id'];
            $model->comment = $post['Documents']['comment'];
            $model->date = $post['Documents']['date'];
            $model->updated_at = date('Y-m-d H:i:s');
            $model->save();
            return $this->redirect(['index', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Documents model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $documents = Documents::findOne($id);
        $documents->status = '0';
        $documents->save();
        return $this->redirect(['index']);
    }

    /**
     * Finds the Documents model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Documents the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Documents::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
