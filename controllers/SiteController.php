<?php

namespace app\controllers;

use app\models\Notifications;
use app\models\Users;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
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
        } else if($action->id == 'login' && !(isset($session['user_id']) && $session['logged'])){
            return $this->actionLogin();
        } else if($action->id == 'login') {
            return $this->redirect('/dashboard');
        }
//        else if ($action->id === 'forgot-password'){
//            return  $this->redirect('site/forgot-password');
//        }
        return parent::beforeAction($action);
    }


//    public function behaviors()
//    {
//        return [
//            'access' => [
//                'class' => AccessControl::class,
//                'only' => ['logout'],
//                'rules' => [
//                    [
//                        'actions' => ['logout'],
//                        'allow' => true,
//                        'roles' => ['@'],
//                    ],
//                ],
//            ],
//            'verbs' => [
//                'class' => VerbFilter::class,
//                'actions' => [
//                    'logout' => ['post'],
//                ],
//            ],
//        ];
//    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        $session = Yii::$app->session;
        $this->layout = 'loginLayout';
//        $sub_page = [];
        if ($this->request->isPost) {
            $username = Yii::$app->request->post('username');
            $password = Yii::$app->request->post('password');
            $remember = Yii::$app->request->post('remember');
            $identity = Users::findOne(['username' => $username]);
            if ($identity && $identity->password === $password && $identity->username === $username) {
                if (isset($remember)){
                    $session->set('remember',true);
                    setcookie('username',$username, time()+60 * 60 * 24 * 30, '/');
                    setcookie('password',$password, time()+60 * 60 * 24 * 30, '/');
                }
                date_default_timezone_set('Asia/Yerevan');
                $identity->updated_at = date('Y-m-d H:i:s');
                $identity->save(false);
                $session->set('name',$identity->name);
                $session->set('username',$identity->username);
                $session->set('user_id',$identity->id);
                $session->set('role_id', $identity->role_id);
                $session->set('logged',true);
                return $this->redirect('/dashboard');
            } else {
                return $this->redirect('');
            }
        }
        else{
            return $this->render('login', [
//                'model' => $model,
            ]);
        }
//        return $this->render('login',[
//            'sub_page' => $sub_page
//        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        session_destroy();
        $this->redirect('/site/login');
    }

    public function actionForgotPassword(){
        $this->layout = 'forgotLayout';

        return $this->render('forgot');
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionGetNotifications()
    {
        $notifications_today  = Notifications::find()
            ->select(['title', 'message', 'datetime'])
            ->andWhere(['>=', 'datetime', date('Y-m-d')])
            ->orderBy(['datetime' => SORT_DESC])
            ->asArray()
            ->all();
        $notifications_all  = Notifications::find()
            ->select(['title', 'message', 'datetime'])
            ->orderBy(['datetime' => SORT_DESC])
            ->asArray()
            ->all();
        return json_encode(['notifications_today' => $notifications_today, 'notifications_all' => $notifications_all]);
    }

    public function actionCheckNotifications()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $notifications = Notifications::find()
            ->andWhere(['>=', 'datetime', date('Y-m-d')])
            ->orderBy(['datetime' => SORT_DESC])
            ->one();

        return [
            'success' => true,
            'notifications' => $notifications,
        ];
    }

}
