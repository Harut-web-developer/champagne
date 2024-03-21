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
        } else if($action->id == 'login' && ($session['role_id'] == 1 || $session['role_id'] == 2)) {
            return $this->redirect('/dashboard');
        } else if($action->id == 'login' && $session['role_id'] == 4) {
            return $this->redirect('/warehouse');
        } else if($action->id == 'login' && $session['role_id'] == 3) {
            return $this->redirect('/map');
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
            $valid = '';
            if ($identity && $identity->username === $username){
                $valid = password_verify($password, $identity->password);
            }
            if ($identity && $valid && $identity->username === $username) {
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
                if($session['role_id'] == 1 || $session['role_id'] == 2) {
                    return $this->redirect('/dashboard');
                } else if($session['role_id'] == 4) {
                    return $this->redirect('/warehouse');
                } else if($session['role_id'] == 3) {
                    return $this->redirect('/map');
                }
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
    public function actionIndexNotificationsClick()
    {
        $session = Yii::$app->session;
        if (isset($_GET)){
            if ($session['role_id'] == '1') {
                $notification_badge_admin_index = Notifications::find()
                    ->select('id, status')
                    ->Where(['or', ['sort_' => 'updateclientskeeper'], ['sort_' => 'createclientskeeper'], ['sort_' => 'ordersdeliveredkeeper'], ['sort_' => 'orderscreatekeeper'], ['sort_' => 'orderscreate'], ['sort_' => 'ordersupdate'], ['sort_' => 'ordersdelivered'], ['sort_' => 'documentscreate'], ['sort_' => 'exitdocument'], ['sort_' => 'changeorderscount'], ['sort_' => 'refusalreturn'], ['sort_' => 'createclients'], ['sort_' => 'updateclients'], ['sort_' => 'createNomenclature'], ['sort_' => 'updateNomenclature']])
                    ->andWhere(['NOT LIKE', 'status', ',' . $session['role_id'] . ','])
                    ->andWhere(['>=', 'datetime', date('Y-m-d')])
                    ->asArray()
                    ->all();
                foreach ($notification_badge_admin_index as $notification) {
                    $status_value = $notification['status'] . $session['role_id'] . ',';
                    Notifications::updateAll(['status' => $status_value], ['id' => $notification['id']]);
                }
            }
            if ($session['role_id'] == '4') {
                $notification_badge_storekeeper = Notifications::find()
                    ->select('id, status')
                    ->Where(['or', ['sort_' => 'orderscreate'], ['sort_' => 'ordersdelivered'], ['sort_' => 'changeorderscount']])
                    ->andWhere(['NOT LIKE', 'status', ',' . $session['role_id'] . ','])
                    ->andWhere(['>=', 'datetime', date('Y-m-d')])
                    ->asArray()
                    ->all();
                foreach ($notification_badge_storekeeper as $notification) {
                    $status_value = $notification['status'] . $session['role_id'] . ',';
                    Notifications::updateAll(['status' => $status_value], ['id' => $notification['id']]);
                }
            }
        }
    }
    public function actionIndexNotifications()
    {
        $session = Yii::$app->session;
        $render_array = [];
        if (isset($_GET)) {
            if ($session['role_id'] == '1') {
                $notification_badge_admin = Notifications::find()
                    ->select('id')
                    ->Where(['or', ['sort_' => 'updateclientskeeper'], ['sort_' => 'createclientskeeper'], ['sort_' => 'ordersdeliveredkeeper'], ['sort_' => 'orderscreatekeeper'], ['sort_' => 'orderscreate'], ['sort_' => 'ordersupdate'], ['sort_' => 'ordersdelivered'], ['sort_' => 'documentscreate'], ['sort_' => 'exitdocument'], ['sort_' => 'changeorderscount'], ['sort_' => 'refusalreturn'], ['sort_' => 'createclients'], ['sort_' => 'updateclients'], ['sort_' => 'createNomenclature'], ['sort_' => 'updateNomenclature']])
                    ->andWhere(['NOT LIKE', 'status', ',' . $session['role_id'] . ','])
                    ->andWhere(['>=', 'datetime', date('Y-m-d')])
                    ->count();
                $render_array = [
                    'notification_badge' => $notification_badge_admin,
                ];
            }
            if ($session['role_id'] == '4') {
                $notification_badge_storekeeper = Notifications::find()
                    ->select('id')
                    ->Where(['or', ['sort_' => 'orderscreate'], ['sort_' => 'ordersdelivered'], ['sort_' => 'changeorderscount']])
                    ->andWhere(['NOT LIKE', 'status', ',' . $session['role_id'] . ','])
                    ->andWhere(['>=', 'datetime', date('Y-m-d')])
                    ->count();
                $render_array = [
                    'notification_badge' => $notification_badge_storekeeper,
                ];
            }
        }
        return json_encode($render_array);
    }
    public function actionGetNotifications()
    {
        $session = Yii::$app->session;
        $notifications_today = 0;
        $notifications_all = 0;
        if ($session['role_id'] == '1') {
            $notifications_today = Notifications::find()
                ->select(['title', 'message', 'datetime'])
                ->andWhere(['>=', 'datetime', date('Y-m-d')])
                ->andWhere(['or', ['sort_' => 'updateclientskeeper'], ['sort_' => 'createclientskeeper'], ['sort_' => 'ordersdeliveredkeeper'], ['sort_' => 'orderscreatekeeper'], ['sort_' => 'orderscreate'], ['sort_' => 'ordersupdate'], ['sort_' => 'ordersdelivered'], ['sort_' => 'documentscreate'], ['sort_' => 'exitdocument'], ['sort_' => 'changeorderscount'], ['sort_' => 'refusalreturn'], ['sort_' => 'createclients'], ['sort_' => 'updateclients'], ['sort_' => 'createNomenclature'], ['sort_' => 'updateNomenclature']])
                ->orderBy(['datetime' => SORT_DESC])
                ->asArray()
                ->all();
            $notifications_all = Notifications::find()
                ->select(['title', 'message', 'datetime'])
                ->Where(['or', ['sort_' => 'orderscreate'], ['sort_' => 'ordersupdate'], ['sort_' => 'ordersdelivered'], ['sort_' => 'documentscreate'], ['sort_' => 'exitdocument'], ['sort_' => 'changeorderscount'], ['sort_' => 'refusalreturn'], ['sort_' => 'createclients'], ['sort_' => 'updateclients'], ['sort_' => 'createNomenclature'], ['sort_' => 'updateNomenclature']])
                ->orderBy(['datetime' => SORT_DESC])
                ->asArray()
                ->all();
        }
        if ($session['role_id'] == '4') {
            $notifications_today = Notifications::find()
                ->select(['title', 'message', 'datetime'])
                ->andWhere(['>=', 'datetime', date('Y-m-d')])
                ->andWhere(['or', ['sort_' => 'orderscreate'], ['sort_' => 'ordersdelivered'], ['sort_' => 'changeorderscount']])
                ->andWhere(['!=', 'role_id', 4])
                ->orderBy(['datetime' => SORT_DESC])
                ->asArray()
                ->all();
            $notifications_all = Notifications::find()
                ->select(['title', 'message', 'datetime'])
                ->andWhere(['or', ['sort_' => 'orderscreate'], ['sort_' => 'ordersdelivered'], ['sort_' => 'changeorderscount']])
                ->andWhere(['!=', 'role_id', 4])
                ->orderBy(['datetime' => SORT_DESC])
                ->asArray()
                ->all();
        }
        return json_encode([
            'notifications_today' => $notifications_today,
            'notifications_all' => $notifications_all,
        ]);
    }

    public function actionCheckNotifications()
    {
        $session = Yii::$app->session;
        Yii::$app->response->format = Response::FORMAT_JSON;
        $users_premissions = [];
        if ($session['role_id'] == '1') {
            $users_premissions = ['updateclientskeeper','createclientskeeper','ordersdeliveredkeeper','orderscreatekeeper','orderscreate','ordersupdate','ordersdelivered','exitdocument','documentscreate','changeorderscount','refusalreturn','createclients','updateclients','createNomenclature','updateNomenclature'];
        }
        if ($session['role_id'] == '4') {
            $users_premissions = ['orderscreate','ordersdelivered','changeorderscount'];
        }
        $notification = Notifications::find()
            ->andWhere(['>=', 'datetime', date('Y-m-d')])
//            ->andWhere(['status' => ','])
            ->andWhere(['or', ['role_id' => '2'], ['role_id' => '3']])
            ->andWhere([
                'or',
                ['watched' => ','],
                ['NOT LIKE', 'watched', ',' . $session['role_id'] . ',']
            ])
            ->all();

        $not_count = count($notification);

        for ($i = 0; $i < $not_count; $i++){
            if (in_array($notification[$i]['sort_'], $users_premissions)) {
                $notification = $notification[$i];
                break;
            }elseif ($i == $not_count-1){
                $notification = null;
            }
        }
        if ($session['role_id'] == '1' || $session['role_id'] == '4') {
            if (!empty($notification)){
                if (!is_null($notification)){
                    if (($notification->watched != ',') && ($notification->sort_ == 'orderscreate' || $notification->sort_ == 'changeorderscount' || $notification->sort_ == 'ordersdelivered')) {
                        $watched_array = explode(',', $notification->watched);
                        if (in_array('1', $watched_array) && in_array('4', $watched_array)) {
                            $notification->status = 0;
                            $notification->save();
                            return [
                                'success' => false,
                            ];
                        } elseif (!in_array($session['role_id'], $watched_array)) {
                            $watched_value = $notification->watched . $session['role_id'] . ',';
                            $notification->watched = $watched_value;
                            $notification->save();
                            return [
                                'success' => true,
                                'notifications' => $notification,
                            ];
                        }
                    }elseif(($notification->watched == ',') && ($notification->sort_ == 'orderscreate' || $notification->sort_ == 'changeorderscount' || $notification->sort_ == 'ordersdelivered')){
                        $watched_value = $notification->watched . $session['role_id'] . ',';
                        $notification->watched = $watched_value;
                        $notification->save();
                        return [
                            'success' => true,
                            'notifications' => $notification,
                        ];
                    }
                }
            }
        }

        if ($session['role_id'] == '1') {
            if (!empty($notification)){
                if (!is_null($notification)){
                    if (($notification->watched != ',') && ($notification->sort_ == 'ordersupdate' || $notification->sort_ == 'createclients' || $notification->sort_ == 'updateclients')) {
                        $watched_array = explode(',', $notification->watched);
                        if (in_array('1', $watched_array)) {
                            $notification->status = 0;
                            $notification->save();
                            return [
                                'success' => false
                            ];
                        } elseif (!in_array($session['role_id'], $watched_array)) {
                            $watched_value = $notification->watched . $session['role_id'] . ',';
                            $notification->watched = $watched_value;
                            $notification->save();
                            return [
                                'success' => true,
                                'notifications' => $notification,
                            ];
                        }
                    }elseif(($notification->watched == ',') && ($notification->sort_ == 'ordersupdate' || $notification->sort_ == 'createclients' || $notification->sort_ == 'updateclients')){
                        $watched_value = $notification->watched . $session['role_id'] . ',';
                        $notification->watched = $watched_value;
                        $notification->save();
                        return [
                            'success' => true,
                            'notifications' => $notification,
                        ];
                    }
                }
            }
        }
        $notification_doc = Notifications::find()
            ->andWhere(['>=', 'datetime', date('Y-m-d')])
//            ->andWhere(['status' => '1'])
            ->andWhere(['or', ['role_id' => '4'],['role_id' => '2'], ['role_id' => '3']])
            ->andWhere([
                'or',
                ['watched' => ','],
                ['NOT LIKE', 'watched', ',' . $session['role_id'] . ',']
            ])
            ->all();
        $not_count = count($notification_doc);
        for ($i = 0; $i < $not_count; $i++){
            if (in_array($notification_doc[$i]['sort_'], $users_premissions)) {
                $notification_doc = $notification_doc[$i];
                break;
            }elseif ($i == $not_count-1){
                $notification_doc = null;
            }
        }

        if ($session['role_id'] == '1') {
            if (!empty($notification_doc)){
                if (!is_null($notification_doc)){
                    if (($notification_doc->watched != ',') && ($notification_doc->sort_ == 'updateclientskeeper' || $notification_doc->sort_ == 'createclientskeeper' || $notification_doc->sort_ == 'ordersdeliveredkeeper' || $notification_doc->sort_ == 'orderscreatekeeper' || $notification_doc->sort_ == 'exitdocument' || $notification_doc->sort_ == 'refusalreturn' || $notification_doc->sort_ == 'createNomenclature' || $notification_doc->sort_ == 'updateNomenclature' || $notification_doc->sort_ == 'documentscreate')) {
                        $watched_array = explode(',', $notification_doc->watched);
                        if (in_array('1', $watched_array)) {
                            $notification_doc->status = 0;
                            $notification_doc->save();
                            return [
                                'success' => false
                            ];
                        } elseif (!in_array($session['role_id'], $watched_array)) {
                            $watched_value = $notification_doc->watched . $session['role_id'] . ',';
                            $notification_doc->watched = $watched_value;
                            $notification_doc->save();
                            return [
                                'success' => true,
                                'notifications' => $notification_doc,
                            ];
                        }
                    }elseif(($notification_doc->watched == ',') && ($notification_doc->sort_ == 'updateclientskeeper' || $notification_doc->sort_ == 'createclientskeeper' || $notification_doc->sort_ == 'ordersdeliveredkeeper' || $notification_doc->sort_ == 'orderscreatekeeper' || $notification_doc->sort_ == 'exitdocument' || $notification_doc->sort_ == 'refusalreturn' || $notification_doc->sort_ == 'createNomenclature' || $notification_doc->sort_ == 'updateNomenclature' || $notification_doc->sort_ == 'documentscreate')){
                        $watched_value = $notification_doc->watched . $session['role_id'] . ',';
                        $notification_doc->watched = $watched_value;
                        $notification_doc->save();
                        return [
                            'success' => true,
                            'notifications' => $notification_doc,
                        ];
                    }
                }

            }
        }
        return [
            'success' => false,
        ];
    }

}
