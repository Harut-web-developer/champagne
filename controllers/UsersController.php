<?php

namespace app\controllers;


use app\models\Log;
use app\models\Premissions;
use app\models\User;
use app\models\UserPremissions;
use app\models\Warehouse;
use Yii;
use app\models\Users;
use app\models\UsersSearch;
use app\models\Roles;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UsersController implements the CRUD actions for Users model.
 */
class UsersController extends Controller
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
     * Lists all Users models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $session = Yii::$app->session;
        $have_access = Users::checkPremission(16);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $res = Yii::$app->runAction('custom-fields/get-table-data',['page'=>'users']);

        if ($session['role_id'] == 1) {
            $sub_page = [
                ['name' => 'Կարգավիճակ','address' => '/roles'],
                ['name' => 'Մենեջեր-առաքիչ','address' => '/manager-deliver-condition'],
//            ['name' => 'Թույլտվություն','address' => '/premissions'], փակ մնա
            ];
        }else{
            $sub_page = [];
        }
        $date_tab = [];

        $searchModel = new UsersSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,
            'new_fields'=>$res,

        ]);
    }

    public function actionProfile(){
        $sub_page = [];
        $date_tab = [];

        $session = Yii::$app->session;
        $model = $this->findModel($session['user_id']);
        if($this->request->isPost){
            date_default_timezone_set('Asia/Yerevan');
            $post = $this->request->post();
            $password = $post['Users']['password'];
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $model->password = $hash;
            $model->email = $post['Users']['email'];
            $model->phone = $post['Users']['phone'];
            $model->updated_at = date('Y-m-d H:i:s');
            $model->save(false);
            return $this->redirect(['profile', 'id' => $model->id]);
        }else{
            $model->loadDefaultValues();
        }
        return $this->render('profile',[
            'model' => $model,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,

        ] );
    }

    /**
     * Displays a single Users model.
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
     * Creates a new Users model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $have_access = Users::checkPremission(13);
        if(!$have_access){
            $this->redirect('/site/403');
        }
//        echo "<pre>";
        $sub_page = [];
        $date_tab = [];

        $model = new Users();
        $url = Url::to('', 'http');
        $url = str_replace('create', 'view', $url);
        $premission_users = Premissions::find()
            ->select('name')
            ->where(['id' => 13])
            ->asArray()
            ->one();
        if ($this->request->isPost) {
            date_default_timezone_set('Asia/Yerevan');
            $post = $this->request->post();
            $model->name = $post['Users']['name'];
            $model->username = $post['Users']['username'];
            $model->role_id = $post['Users']['role_id'];
            if ($post['Users']['role_id'] == 4){
                $model->warehouse_id = $post['Users']['warehouse_id'];
            }else{
                $model->warehouse_id = null;
            }
            $password = $post['Users']['password'];
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $model->auth_key = $this->generateRandomString();
            $model->password = $hash;
            $model->email = $post['Users']['email'];
            $model->phone = $post['Users']['phone'];
            $model->created_at = date('Y-m-d H:i:s');
            $model->updated_at = date('Y-m-d H:i:s');
            $model = User::getDefVals($model);
            $model->save(false);
            if(!empty($post['premission'])){
                for ($i = 0; $i < count($post['premission']);$i++){
                    $premission = new UserPremissions();
                    $premission->user_id = $model->id;
                    $premission->premission_id = intval($post['premission'][$i]);
                    $premission->created_at = date('Y-m-d H:i:s');
                    $premission->updated_at = date('Y-m-d H:i:s');
                    $premission->save(false);
                }
            }
            $_POST['item_id'] = $model->id;
            if($post['newblocks'] || $post['new_fild_name']){
                Yii::$app->runAction('custom-fields/create-title',$post);
            }
            Log::afterSaves('Create', $model, '', $url.'?'.'id'.'='.$model->id, $premission_users);
            return $this->redirect(['index', 'id' => $model->id]);
        } else {
            $model->loadDefaultValues();
        }
        $roles = Roles::find()->select('id,name')->asArray()->all();
        $roles = ArrayHelper::map($roles,'id','name');
        $warehouse = Warehouse::find()->select('id,name')->where(['status' => 1])->asArray()->all();
        $warehouse = ArrayHelper::map($warehouse,'id','name');
        $premissions_check = Premissions::find()->select('id,name')->where(['status' => '1'])->asArray()->all();
        return $this->render('create', [
            'model' => $model,
            'roles' => $roles,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,
            'warehouse' => $warehouse,
            'premissions_check' => $premissions_check

        ]);
    }

    public function actionCheckUsers(){
        if ($this->request->isPost){
            $users = Users::find()->where(['username' => $this->request->post('userText')])->andWhere(['status' => '1'])->exists();
            if ($users){
                return json_encode(true);
            }else{
                return json_encode(false);
            }
        }
    }
    public function actionCheckMail(){
        if ($this->request->isPost){
            $users = Users::find()->where(['email' => $this->request->post('userText')])->andWhere(['status' => '1'])->exists();
            if ($users){
                return json_encode(true);
            }else{
                return json_encode(false);
            }
        }
    }
    public function actionCreateFields()
    {
        $have_access = Users::checkPremission(74);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $sub_page = [
            ['name' => 'Կարգավիճակ','address' => '/roles'],
            ['name' => 'Թույլտվություն','address' => '/premissions'],
            ['name' => 'Օգտատեր','address' => '/users'],
        ];
        $date_tab = [];

        $model = new Users();

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
     * Updates an existing Users model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $have_access = Users::checkPremission(14);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $model = $this->findModel($id);
        unset($model->password);
        $sub_page = [];
        $date_tab = [];
        if ($this->findModel($id)->status == 0) {
            return $this->redirect(Yii::$app->request->referrer);
        }
        $url = Url::to('', 'http');
        $oldattributes = Users::find()
            ->select('*')
            ->where(['id' => $id])
            ->asArray()
            ->one();
        $premission_users = Premissions::find()
            ->select('name')
            ->where(['id' => 14])
            ->asArray()
            ->one();
        if ($this->request->isPost) {
            date_default_timezone_set('Asia/Yerevan');
            $post = $this->request->post();
            if ($post['Users']['role_id'] == 4){
                $model->warehouse_id = $post['Users']['warehouse_id'];
            }else{
                $model->warehouse_id = null;
            }
            $model->name = $post['Users']['name'];
            $model->username = $post['Users']['username'];
            $model->role_id = $post['Users']['role_id'];
            if ($post['Users']['role_id'] == 4){
                $model->warehouse_id = $post['Users']['warehouse_id'];
            }
            $model->auth_key = $this->generateRandomString();
            if ($post['Users']['password'] != ''){
                $password = $post['Users']['password'];
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $model->password = $hash;
            }
            $model->email = $post['Users']['email'];
            $model->phone = $post['Users']['phone'];
            $model->updated_at = date('Y-m-d H:i:s');
            $model->save(false);
            if(!empty($post['premission'])){
                UserPremissions::deleteAll(['user_id' => $model->id]);
                for ($i = 0; $i < count($post['premission']);$i++){
                    $premission = new UserPremissions();
                    $premission->user_id = $model->id;
                    $premission->premission_id = intval($post['premission'][$i]);
                    $premission->created_at = date('Y-m-d H:i:s');
                    $premission->updated_at = date('Y-m-d H:i:s');
                    $premission->save(false);
                }
            }
            Log::afterSaves('Update', $model, $oldattributes, $url, $premission_users);
            $_POST['item_id'] = $model->id;
            if($post['newblocks'] || $post['new_fild_name']){
                Yii::$app->runAction('custom-fields/create-title',$post);
            }
            return $this->redirect(['index', 'id' => $model->id]);
        }
        $roles = Roles::find()->select('id,name')->where(['status' => '1'])->asArray()->all();
        $roles = ArrayHelper::map($roles,'id','name');
        $warehouse = Warehouse::find()->select('id,name')->where(['status' => 1])->asArray()->all();
        $warehouse = ArrayHelper::map($warehouse,'id','name');
        $user_premission_select = UserPremissions::find()->select('id,premission_id')->where(['user_id' => $id])->asArray()->all();
        $premissions_check = Premissions::find()->select('id,name')->where(['status' => '1'])->asArray()->all();

        return $this->render('update', [
            'model' => $model,
            'roles' => $roles,
            'user_premission_select' => $user_premission_select,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,
            'warehouse' => $warehouse,
            'premissions_check' => $premissions_check
        ]);
    }

    /**
     * Deletes an existing Users model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $have_access = Users::checkPremission(15);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $oldattributes = Users::find()
            ->select('name')
            ->where(['id' => $id])
            ->asArray()
            ->one();

        $premission = Premissions::find()
            ->select('name')
            ->where(['id' => 15])
            ->asArray()
            ->one();
        $users = Users::findOne($id);
        $users->status = '0';
        $users->save();
        Log::afterSaves('Delete', '', $oldattributes['name'], '#', $premission);
        return $this->redirect(['index']);
    }

    /**
     * Finds the Users model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Users the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Users::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    public function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
