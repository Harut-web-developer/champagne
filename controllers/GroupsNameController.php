<?php

namespace app\controllers;

use app\models\Clients;
use app\models\ClientsGroups;
use app\models\DiscountClients;
use app\models\GroupsName;
use app\models\GroupsNameSearch;
use app\models\Log;
use app\models\Premissions;
use app\models\Users;
use Yii;
use yii\helpers\Url;
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
     * Lists all GroupsName models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $have_access = Users::checkPremission(61);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $searchModel = new GroupsNameSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $sub_page = [];
        if (Users::checkPremission(85)){
            $branches = ['name' => 'Մասնաճյուղի Խմբեր','address' => '/branch-groups'];
            array_push($sub_page,$branches);
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
        $have_access = Users::checkPremission(58);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $model = new GroupsName();
        $sub_page = [];
        $date_tab = [];

        if ($this->request->isPost) {
            date_default_timezone_set('Asia/Yerevan');
            $url = Url::to('', 'http');
            $url = str_replace('create', 'view', $url);
            $premission = Premissions::find()
                ->select('name')
                ->where(['id' => 58])
                ->asArray()
                ->one();
            $model_l = array();
            $post = $this->request->post();
            $model->groups_name = $post['GroupsName']['groups_name'];
            $model->created_at = date('Y-m-d H:i:s');
            $model->updated_at = date('Y-m-d H:i:s');
            $model->save();
            foreach ($model as $index => $item) {
                $model_l[$index] = $item;
            }
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
                    foreach ($model_clients_groups as $index => $item) {
                        $model_l[$index.$i] = $item;
                    }
                }
            }
            Log::afterSaves('Create', $model_l, '', $url.'?'.'id'.'='.$model->id, $premission);
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
        $have_access = Users::checkPremission(59);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $model = $this->findModel($id);
        $sub_page = [];
        $date_tab = [];

        if ($this->request->isPost) {
            date_default_timezone_set('Asia/Yerevan');
            $url = Url::to('', 'http');
            $url = str_replace('update', 'view', $url);
            $premission = Premissions::find()
                ->select('name')
                ->where(['id' => 59])
                ->asArray()
                ->one();
            $model_l = array();
            $post = $this->request->post();
            $clients_groups = ClientsGroups::find()
                ->where(['groups_id' => $id])
                ->exists();
            if ($clients_groups){
                ClientsGroups::deleteAll(['groups_id' => $id]);
            }
            $model->groups_name = $post['GroupsName']['groups_name'];
            $model->updated_at = date('Y-m-d H:i:s');
            $model->save(false);
            foreach ($model as $index => $item) {
                $model_l[$index] = $item;
            }
            $client_id = GroupsName::find()
                ->select('id')
                ->where(['groups_name' => $post['GroupsName']['groups_name']])
                ->andWhere(['status' => '1'])
                ->asArray()
                ->one();

            if(!empty($post['clients'])){
                for ($i = 0; $i < count($post['clients']);$i++){
                    $model_clients_groups = new ClientsGroups();
                    $model_clients_groups->groups_id = $client_id['id'];
                    $model_clients_groups->clients_id = intval($post['clients'][$i]);
                    $model_clients_groups->save(false);
                    foreach ($model_clients_groups as $index => $item) {
                        $model_l[$index.$i] = $item;
                    }
                }
            }
            else{
                $discount_clients_check = DiscountClients::find()->where(['discount_id' => $id])->exists();
                if ($discount_clients_check){
                    DiscountClients::deleteAll(['discount_id' => $id]);
                }
            }
            $old_discs = DiscountClients::find()
                ->select(['discount_id'])
                ->where(['group_id' => $id])
                ->andWhere(['status' => 1])
                ->groupBy(['discount_id'])
                ->asArray()
                ->indexBy('discount_id')
                ->column();

            if (!empty($post['clients']) && !empty($old_discs)) {
                DiscountClients::deleteAll(['in', 'discount_id', array_keys($old_discs)]);
                foreach ($old_discs as $old_disc) {
                    foreach ($post['clients'] as $client) {
                        $new_disc = new DiscountClients();
                        $new_disc->discount_id = $old_disc;
                        $new_disc->group_id = $id;
                        $new_disc->client_id = intval($client);
                        $new_disc->created_at = date('Y-m-d H:i:s');
                        $new_disc->updated_at = date('Y-m-d H:i:s');
                        $new_disc->save(false);
                    }
                }
            }
            Log::afterSaves('Update', $model_l, '', $url, $premission);
            return $this->redirect(['index', 'id' => $model->id]);
        }
        $clients_groups = ClientsGroups::find()
            ->select('clients.id')
            ->leftJoin('clients', 'clients.id = clients_groups.clients_id')
            ->leftJoin('groups_name', 'groups_name.id = clients_groups.groups_id')
            ->where(['clients_groups.groups_id' => $id])
            ->andWhere(['groups_name.status' => '1'])
            ->asArray()
            ->all();
        $clients_groups = array_column($clients_groups,'id');
        $clients = Clients::find()->select('id, name')->where(['status' => '1'])->asArray()->all();
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
        $have_access = Users::checkPremission(60);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $premission = Premissions::find()
            ->select('name')
            ->where(['id' => 60])
            ->asArray()
            ->one();
        $oldattributes = GroupsName::find()
            ->select(['groups_name as name'])
            ->where(['id' => $id])
            ->asArray()
            ->one();
        $groups_name = GroupsName::findOne($id);
        $groups_name->status = '0';
        $groups_name->save(false);
        Log::afterSaves('Delete', '', $oldattributes['name'], '#', $premission);
        $discount_clients = DiscountClients::find()
            ->select('id')
            ->where(['group_id' => $id])
            ->andWhere(['status' => '1'])
            ->asArray()
            ->all();
        foreach ($discount_clients as $clients) {
            DiscountClients::updateAll(['status' => '0'], ['id' => $clients['id']]);
        }
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
