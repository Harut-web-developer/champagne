<?php

namespace app\controllers;

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
    public function beforeAction($action)
    {
        $session = Yii::$app->session;
        if ($action->id !== 'login' && !(isset($session['user_id']) && $session['logged'])) {
            return $this->redirect(['site/login']);
        } else if($action->id == 'login' && !(isset($session['user_id']) && $session['logged'])){
            return $this->actionLogin();
        } else if($action->id == 'login') {
            return $this->redirect('/warehouse');
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
        if ($this->request->isPost) {
            $username = Yii::$app->request->post('username');
            $password = Yii::$app->request->post('password');
            $remember = Yii::$app->request->post('remember');
            $identity = Users::findOne(['username' => $username]);
            if ($identity && $identity->password === $password) {
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
                $session->set('logged',true);
                return $this->redirect('/warehouse');
            } else {
                return $this->redirect('');
            }
        }else{
            return $this->render('login', [
//                'model' => $model,
            ]);
        }
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
    public function actionAbout()
    {
        return $this->render('about');
    }
}
