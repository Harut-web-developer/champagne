<?php

namespace app\controllers;

use app\models\Clients;
use app\models\Log;
use app\models\Orders;
use app\models\Payments;
use app\models\Premissions;
use app\models\Users;
use Yii;
use app\models\BranchGroups;
use app\models\BranchGroupsSearch;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * BranchGroupsController implements the CRUD actions for BranchGroups model.
 */
class BranchGroupsController extends Controller
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
     * Lists all BranchGroups models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $have_access = Users::checkPremission(85);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $sub_page = [];
        if (Users::checkPremission(61)){
            $groups_discount = ['name' => 'Զեղչի խմբեր','address' => '/groups-name'];
            array_push($sub_page,$groups_discount);
        }
        if (Users::checkPremission(89)){
            $company = ['name' => 'Ընկերություններ','address' => '/companies-with-cash'];
            array_push($sub_page,$company);
        }
        if (Users::checkPremission(8)){
            $clients = ['name' => 'Հաճախորդներ','address' => '/clients'];
            array_push($sub_page,$clients);
        }
        $date_tab = [];

        $searchModel = new BranchGroupsSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,
        ]);
    }

    public function actionBranches($id){
        $have_access = Users::checkPremission(90);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $sub_page = [];
        if (Users::checkPremission(61)){
            $groups_discount = ['name' => 'Զեղչի խմբեր','address' => '/groups-name'];
            array_push($sub_page,$groups_discount);
        }
        if (Users::checkPremission(89)){
            $company = ['name' => 'Ընկերություններ','address' => '/companies-with-cash'];
            array_push($sub_page,$company);
        }
        if (Users::checkPremission(8)){
            $clients = ['name' => 'Հաճախորդներ','address' => '/clients'];
            array_push($sub_page,$clients);
        }
        $date_tab = [];
        $branches = Clients::find()
            ->select(['clients.name'])
            ->addSelect(['sum(orders.total_price) as total_price' ])
            ->joinWith(['ordersSum'])
            ->where(['clients.status' => '1'])
            ->andWhere(['clients.branch_groups_id' => $id])
            ->groupBy(['clients.id'])
            ->asArray()
            ->all();
        return $this->render('branches', [
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,
            'branches' => $branches,
        ]);
    }
    /**
     * Displays a single BranchGroups model.
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
     * Creates a new BranchGroups model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $have_access = Users::checkPremission(82);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $sub_page = [
            ['name' => 'Խմբեր','address' => '/groups-name'],
        ];
        $date_tab = [];
        $model = new BranchGroups();
        $url = Url::to('', 'http');
        $url = str_replace('create', 'view', $url);
        $premission = Premissions::find()
            ->select('name')
            ->where(['id' => 82])
            ->andWhere(['status' => 1])
            ->asArray()
            ->one();
        if ($this->request->isPost) {
            date_default_timezone_set('Asia/Yerevan');
            $post = $this->request->post();
            $model->name = $post['BranchGroups']['name'];
            $model->created_at = date('Y-m-d H:i:s');
            $model->updated_at = date('Y-m-d H:i:s');
            $model->save();
            Log::afterSaves('Create', $model, '', $url.'?'.'id'.'='.$model->id, $premission);
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

    /**
     * Updates an existing BranchGroups model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $have_access = Users::checkPremission(83);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $sub_page = [
            ['name' => 'Խմբեր','address' => '/groups-name'],
        ];
        $date_tab = [];
        $model = $this->findModel($id);
        if ($this->findModel($id)->status == 0) {
            return $this->redirect(Yii::$app->request->referrer);
        }
        $url = Url::to('', 'http');
        $oldattributes = BranchGroups::find()
            ->select('*')
            ->where(['id' => $id])
            ->andWhere(['status' => 1])
            ->asArray()
            ->one();
        $premission = Premissions::find()
            ->select('name')
            ->where(['id' => 83])
            ->andWhere(['status' => 1])
            ->asArray()
            ->one();
        if ($this->request->isPost) {
            date_default_timezone_set('Asia/Yerevan');
            $post = $this->request->post();
            $model->name = $post['BranchGroups']['name'];
            $model->updated_at = date('Y-m-d H:i:s');
            $model->save();
            Log::afterSaves('Update', $model, $oldattributes, $url, $premission);
            return $this->redirect(['index', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,
        ]);
    }

    /**
     * Deletes an existing BranchGroups model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $have_access = Users::checkPremission(84);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $oldattributes = BranchGroups::find()
            ->select('name')
            ->where(['id' => $id])
            ->andWhere(['status' => 1])
            ->asArray()
            ->one();

        $premission = Premissions::find()
            ->select('name')
            ->where(['id' => 84])
            ->andWhere(['status' => 1])
            ->asArray()
            ->one();
        $branch_group = BranchGroups::findOne($id);
        $branch_group->status = '0';
        $branch_group->save(false);
        Log::afterSaves('Delete', '', $oldattributes['name'], '#', $premission);
        return $this->redirect(['index']);
    }

    /**
     * Finds the BranchGroups model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return BranchGroups the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BranchGroups::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
