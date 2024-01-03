<?php

namespace app\controllers;

use app\models\Clients;
use app\models\ClientsGroups;
use app\models\GroupsName;
use app\models\GroupsNameSearch;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * GroupsNameController implements the CRUD actions for GroupsName model.
 */
class GroupsNameController extends Controller
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
     * Lists all GroupsName models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new GroupsNameSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $sub_page = [
            ['name' => 'Հաճախորդներ','address' => '/clients'],
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
     * Displays a single GroupsName model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $sub_page = [];
        $date_tab = [];

        $clients_groups = ClientsGroups::find()
            ->select('clients.id, clients.name')
            ->leftJoin('clients', 'clients.id = clients_groups.clients_id')
            ->leftJoin('groups_name', 'groups_name.id = clients_groups.groups_id')
            ->where(['clients_groups.groups_id' => $id])
            ->asArray()
            ->all();
        return $this->render('view', [
            'model' => $this->findModel($id),
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,

            'clients_groups' => $clients_groups,
        ]);
    }

    /**
     * Creates a new GroupsName model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new GroupsName();
        $sub_page = [];
        $date_tab = [];

        if ($this->request->isPost) {
            date_default_timezone_set('Asia/Yerevan');
            $post = $this->request->post();
            $model->groups_name = $post['GroupsName']['groups_name'];
            $model->created_at = date('Y-m-d H:i:s');
            $model->updated_at = date('Y-m-d H:i:s');
            $model->save();
            $client_id = GroupsName::find()
                ->select('id')
                ->where(['groups_name' => $post['GroupsName']['groups_name']])
                ->asArray()
                ->one();
            if(!empty($post['clients'])){
                for ($i = 0; $i < count($post['clients']);$i++){
                    $model_clients_groups = new ClientsGroups();
                    $model_clients_groups->groups_id = $client_id['id'];
                    $model_clients_groups->clients_id = intval($post['clients'][$i]);
                    $model_clients_groups->save(false);
                }
            }
            return $this->redirect(['index', 'id' => $model->id]);
        } else {
            $model->loadDefaultValues();
        }
        $clients = Clients::find()->select('id,name')->asArray()->all();

        return $this->render('create', [
            'model' => $model,
            'clients' => $clients,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,

        ]);
    }

    /**
     * Updates an existing GroupsName model.
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
            $clients_groups = ClientsGroups::find()->where(['groups_id' => $id])->exists();
            if ($clients_groups){
                ClientsGroups::deleteAll(['groups_id' => $id]);
            }
            $model->groups_name = $post['GroupsName']['groups_name'];
            $model->updated_at = date('Y-m-d H:i:s');
            $model->save(false);
            $client_id = GroupsName::find()
                ->select('id')
                ->where(['groups_name' => $post['GroupsName']['groups_name']])
                ->asArray()
                ->one();

            if(!empty($post['clients'])){
                for ($i = 0; $i < count($post['clients']);$i++){
                    $model_clients_groups = new ClientsGroups();
                    $model_clients_groups->groups_id = $client_id['id'];
                    $model_clients_groups->clients_id = intval($post['clients'][$i]);
                    $model_clients_groups->save(false);
                }
            }
            else{
                $discount_clients_check = DiscountClients::find()->where(['discount_id' => $id])->exists();
                if ($discount_clients_check){
                    DiscountClients::deleteAll(['discount_id' => $id]);
                }
            }
            return $this->redirect(['index', 'id' => $model->id]);
        }
        $clients_groups = ClientsGroups::find()
            ->select('clients.id')
            ->leftJoin('clients', 'clients.id = clients_groups.clients_id')
            ->leftJoin('groups_name', 'groups_name.id = clients_groups.groups_id')
            ->where(['clients_groups.groups_id' => $id])
            ->asArray()
            ->all();
        $clients_groups = array_column($clients_groups,'id');
        $clients = Clients::find()->select('id, name')->asArray()->all();
        return $this->render('update', [
            'model' => $model,
            'clients' => $clients,
            'clients_groups' => $clients_groups,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,

        ]);
    }

    /**
     * Deletes an existing GroupsName model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $groups_name = GroupsName::findOne($id);
        $groups_name->status = '0';
        $groups_name->save(false);
        return $this->redirect(['index']);
    }

    /**
     * Finds the GroupsName model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return GroupsName the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = GroupsName::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
