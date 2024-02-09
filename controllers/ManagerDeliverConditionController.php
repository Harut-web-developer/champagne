<?php

namespace app\controllers;

use app\models\ManagerDeliverCondition;
use app\models\ManagerDeliverConditionSearch;
use app\models\Route;
use app\models\Users;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ManagerDeliverConditionController implements the CRUD actions for ManagerDeliverCondition model.
 */
class ManagerDeliverConditionController extends Controller
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

    public function init()
    {
        parent::init();
        Yii::$app->language = 'hy';
    }
    /**
     * Lists all ManagerDeliverCondition models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ManagerDeliverConditionSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        $managers = Users::find()->select('*')->where(['and',['status' => '1'],['role_id' => '2']])->all();
        $sub_page = [
            ['name' => 'Կարգավիճակ','address' => '/roles'],
            ['name' => 'Օգտատեր','address' => '/users'],
        ];
        $date_tab = [];
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'managers' => $managers,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,
        ]);
    }

    /**
     * Displays a single ManagerDeliverCondition model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $sub_page = [];
        $date_tab = [];
        return $this->render('view', [
            'model' => $this->findModel($id),
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,
        ]);
    }

    /**
     * Creates a new ManagerDeliverCondition model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $sub_page = [];
        $date_tab = [];
        if ($this->request->isPost) {
            date_default_timezone_set('Asia/Yerevan');
            $post = $this->request->post();
            for ($i = 0; $i < count($post['deliver_id']); $i++){
                $model = new ManagerDeliverCondition();
                $model->manager_id = $post['manager_id'];
                $model->deliver_id = intval($post['deliver_id'][$i]);
                $model->route_id = intval($post['route_id']);
                $model->created_at = date('Y-m-d H:i:s');
                $model->updated_at = date('Y-m-d H:i:s');
                $model->save();
            }
            return $this->redirect(['index', 'id' => $model->id]);
        }
        $route = Route::find()->select('id, route')->asArray()->all();
        $manager_id = Users::find()->select('id,name')->where(['and',['status' => '1'],['role_id' => '2']])->asArray()->all();
        $deliver_id = Users::find()->select('id,name')->where(['and',['status' => '1'],['role_id' => '3']])->asArray()->all();
        return $this->render('create', [
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,
            'manager_id' => $manager_id,
            'deliver_id' => $deliver_id,
            'route' => $route,
        ]);
    }

    /**
     * Updates an existing ManagerDeliverCondition model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $sub_page = [];
        $date_tab = [];
        $update_manager_id = ManagerDeliverCondition::find()
            ->select('manager_id,route_id')
            ->where(['id' => $id])
            ->andWhere(['status' => '1'])
            ->asArray()
            ->one();
        $update_value = ManagerDeliverCondition::find()
            ->select('manager_id, deliver_id, route_id')
            ->where(['manager_id' => $update_manager_id['manager_id']])
            ->andWhere(['status' => '1'])
            ->asArray()
            ->all();
        if ($this->request->isPost) {
            date_default_timezone_set('Asia/Yerevan');
            $post = $this->request->post();
            if(!empty($post['deliver_id'])) {
                $manager_id_check = ManagerDeliverCondition::find()->where(['manager_id' => intval($update_manager_id['manager_id'])])->exists();
                if ($manager_id_check) {
                    ManagerDeliverCondition::deleteAll(['manager_id' => intval($update_manager_id['manager_id'])]);
                }
                for ($i = 0; $i < count($post['deliver_id']); $i++){
                    $modal = new ManagerDeliverCondition();
                    $modal->manager_id = intval($post['manager_id']);
                    $modal->deliver_id = intval($post['deliver_id'][$i]);
                    $modal->route_id = intval($post['route_id']);
                    $modal->created_at = date('Y-m-d H:i:s');
                    $modal->updated_at = date('Y-m-d H:i:s');
                    $modal->save();
                }
            }
            else{
                $manager_id_check = ManagerDeliverCondition::find()->where(['manager_id' => intval($update_manager_id['manager_id'])])->exists();
                if ($manager_id_check) {
                    ManagerDeliverCondition::deleteAll(['manager_id' => intval($update_manager_id['manager_id'])]);
                }
            }
            return $this->redirect(['index', 'id' => $model->id]);
        }
        $route = Route::find()->select('id, route')->asArray()->all();
        $manager_id = Users::find()->select('id,name')->where(['and',['status' => '1'],['role_id' => '2']])->asArray()->all();
        $deliver_id = Users::find()->select('id,name')->where(['and',['status' => '1'],['role_id' => '3']])->asArray()->all();
        return $this->render('update', [
            'model' => $model,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,
            'manager_id' => $manager_id,
            'deliver_id' => $deliver_id,
            'update_value' => $update_value,
            'route' => $route,
        ]);
    }

    /**
     * Deletes an existing ManagerDeliverCondition model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $update_manager_id = ManagerDeliverCondition::find()
            ->select('manager_id')
            ->where(['id' => $id])
            ->andWhere(['status' => '1'])
            ->asArray()
            ->one();
        $update_value = ManagerDeliverCondition::find()
            ->select('manager_id')
            ->where(['manager_id' => $update_manager_id['manager_id']])
            ->andWhere(['status' => '1'])
            ->asArray()
            ->all();
        $manager_deliver = ManagerDeliverCondition::find()
            ->where(['manager_id' => intval($update_value['manager_id'])])
            ->all();
        for ($i = 0; $i < count($manager_deliver); $i++){
            $manager_deliver[$i]->status = '0';
            $manager_deliver[$i]->save();
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the ManagerDeliverCondition model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return ManagerDeliverCondition the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ManagerDeliverCondition::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
