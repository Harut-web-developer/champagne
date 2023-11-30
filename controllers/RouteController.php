<?php

namespace app\controllers;


use app\models\Clients;
use app\models\Orders;
use app\models\Route;
use app\models\RouteSearch;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * RouteController implements the CRUD actions for Route model.
 */
class RouteController extends Controller
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

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    /**
     * Lists all Route models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new RouteSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Route model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $result = Clients::find()->select('id,name')->where(['=', 'route_id', intval($id)])->asArray()->all();

        return $this->render('view', [
            'model' => $this->findModel($id),
            'result' => $result,
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
        $model = new Route();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect('index');
            }
        } else {
            $model->loadDefaultValues();
        }

//        $route = Route::find()->select('id, route')->asArray()->all();

        return $this->render('create', [
            'model' => $model,
//            'route' => $route,
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
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect('index');
        }

        return $this->render('update', [
            'model' => $model,
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
        $this->findModel($id)->delete();

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
