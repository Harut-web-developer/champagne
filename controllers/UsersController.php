<?php

namespace app\controllers;


use app\models\User;
use app\models\UserPremissions;
use Yii;
use app\models\Users;
use app\models\UsersSearch;
use app\models\Roles;
use yii\helpers\ArrayHelper;
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
     * Lists all Users models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $have_access = Users::checkPremission(16);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $searchModel = new UsersSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Users model.
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
        $model = new Users();
        if ($this->request->isPost) {
            date_default_timezone_set('Asia/Yerevan');
            $post = $this->request->post();
            $model->name = $post['Users']['name'];
            $model->username = $post['Users']['username'];
            $model->role_id = $post['Users']['role_id'];
            $model->auth_key = $this->generateRandomString();
            $model->password = $post['Users']['password'];
            $model->created_at = date('Y-m-d H:i:s');
            $model->updated_at = date('Y-m-d H:i:s');
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
                return $this->redirect(['create', 'id' => $model->id]);
        } else {
            $model->loadDefaultValues();
        }
        $roles = Roles::find()->select('id,name')->asArray()->all();
        $roles = ArrayHelper::map($roles,'id','name');
        return $this->render('create', [
            'model' => $model,
            'roles' => $roles,
        ]);
    }

    public function actionCreateFields()
    {
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

        if ($this->request->isPost) {
            date_default_timezone_set('Asia/Yerevan');
            $post = $this->request->post();
            $model->name = $post['Users']['name'];
            $model->username = $post['Users']['username'];
            $model->role_id = $post['Users']['role_id'];
            $model->auth_key = $this->generateRandomString();
            $model->password = $post['Users']['password'];
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
            $_POST['item_id'] = $model->id;
            if($post['newblocks'] || $post['new_fild_name']){
                Yii::$app->runAction('custom-fields/create-title',$post);
            }
            return $this->redirect(['create', 'id' => $model->id]);
        }
        $roles = Roles::find()->select('id,name')->asArray()->all();
        $roles = ArrayHelper::map($roles,'id','name');
        $user_premission_select = UserPremissions::find()->select('id,premission_id')->where(['user_id' => $id])->asArray()->all();
//        $user_premission_select = array_column($user_premission_select,'premission_id');
        return $this->render('update', [
            'model' => $model,
            'roles' => $roles,
            'user_premission_select' => $user_premission_select,
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
        $users = Users::findOne($id);
        $users->status = '0';
        $users->save();
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
