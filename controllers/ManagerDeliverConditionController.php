<?php

namespace app\controllers;

use app\models\DeliversGroup;
use app\models\ManagerDeliverCondition;
use app\models\ManagerDeliverConditionSearch;
use app\models\Users;
use yii\helpers\ArrayHelper;
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

    /**
     * Lists all ManagerDeliverCondition models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ManagerDeliverConditionSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $sub_page = [
            ['name' => 'Կարգավիճակ','address' => '/roles'],
            ['name' => 'Օգտատեր','address' => '/users'],

//            ['name' => 'Թույլտվություն','address' => '/premissions'], petqa pak mna

        ];
        $date_tab = [];
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
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
        $model = new ManagerDeliverCondition();
        $sub_page = [];
        $date_tab = [];
        if ($this->request->isPost) {
            date_default_timezone_set('Asia/Yerevan');
            $post = $this->request->post();
//            echo "<pre>";
//            var_dump(count($post['deliver_id']));
//            exit();
            $model->manager_id = $post['ManagerDeliverCondition']['manager_id'];
            $model->created_at = date('Y-m-d H:i:s');
            $model->updated_at = date('Y-m-d H:i:s');
            $model->save();

            for ($i = 0; $i < count($post['deliver_id']); $i++){
                $deliver_group = new DeliversGroup();
                $deliver_group->manager_deliver_condition_id = $model->id;
                $deliver_group->deliver_id = intval($post['deliver_id'][$i]);
                $deliver_group->created_at = date('Y-m-d H:i:s');
                $deliver_group->updated_at = date('Y-m-d H:i:s');
                $deliver_group->save();
            }
            return $this->redirect(['index', 'id' => $model->id]);
        }
        else {
            $model->loadDefaultValues();
        }
        $manager_id = Users::find()->select('id,name')->where(['and',['status' => '1'],['role_id' => '2']])->asArray()->all();
        $manager_id = ArrayHelper::map($manager_id,'id','name');
        $deliver_id = Users::find()->select('id,name')->where(['and',['status' => '1'],['role_id' => '3']])->asArray()->all();
        return $this->render('create', [
            'model' => $model,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,
            'manager_id' => $manager_id,
            'deliver_id' => $deliver_id,
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
        if ($this->request->isPost) {
            date_default_timezone_set('Asia/Yerevan');
            $post = $this->request->post();
//            echo "<pre>";
//            var_dump(count($post['deliver_id']));
//            exit();
            $model->manager_id = $post['ManagerDeliverCondition']['manager_id'];
            $model->updated_at = date('Y-m-d H:i:s');
            $model->save();
            if(!empty($post['deliver_id'])) {
                $discount_deliver_check = DeliversGroup::find()->where(['manager_deliver_condition_id' => $id])->exists();
                if ($discount_deliver_check) {
                    DeliversGroup::deleteAll(['manager_deliver_condition_id' => $id]);
                }
                for ($i = 0; $i < count($post['deliver_id']); $i++){
                    $deliver_group = new DeliversGroup();
                    $deliver_group->manager_deliver_condition_id = $id;
                    $deliver_group->deliver_id = intval($post['deliver_id'][$i]);
                    $deliver_group->created_at = date('Y-m-d H:i:s');
                    $deliver_group->updated_at = date('Y-m-d H:i:s');
                    $deliver_group->save();
                }
            }else{
                $discount_deliver_check = DeliversGroup::find()->where(['manager_deliver_condition_id' => $id])->exists();
                if ($discount_deliver_check) {
                    DeliversGroup::deleteAll(['manager_deliver_condition_id' => $id]);
                }
            }


            return $this->redirect(['index', 'id' => $model->id]);
        }
        $manager_id = Users::find()->select('id,name')->where(['and',['status' => '1'],['role_id' => '2']])->asArray()->all();
        $manager_id = ArrayHelper::map($manager_id,'id','name');
        $deliver_id = Users::find()->select('id,name')->where(['and',['status' => '1'],['role_id' => '3']])->asArray()->all();
        $deliver_groups = DeliversGroup::find()->select('deliver_id')->where(['=','manager_deliver_condition_id',$id])->asArray()->all();
        $deliver_groups = array_column($deliver_groups,'deliver_id');
        return $this->render('update', [
            'model' => $model,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,
            'manager_id' => $manager_id,
            'deliver_id' => $deliver_id,
            'deliver_groups' => $deliver_groups,
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
        $manager_deliver = ManagerDeliverCondition::findOne($id);
        $manager_deliver->status = '0';
        $manager_deliver->save();
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
