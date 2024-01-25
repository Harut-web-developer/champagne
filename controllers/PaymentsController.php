<?php

namespace app\controllers;

use app\models\Clients;
use app\models\Payments;
use app\models\PaymentsSearch;
use app\models\Rates;
use app\models\Users;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PaymentsController implements the CRUD actions for Payments model.
 */
class PaymentsController extends Controller
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
     * Lists all Payments models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $have_access = Users::checkPremission(65);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $sub_page = [
            ['name' => 'Վիճակագրություն','address' => '/payments/statistics'],
            ['name' => 'Փոխարժեք','address' => '/rates']
        ];
        $date_tab = [];

        $searchModel = new PaymentsSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,

        ]);
    }

    /**
     * Displays a single Payments model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public  function actionStatistics(){
        $have_access = Users::checkPremission(66);
        if(!$have_access){
            $this->redirect('/site/403');
        }
//        echo "<pre>";
        $statistics = Payments::find()->select('orders.id as orders_id,SUM(orders.total_price) as debt,orders.status,
         clients.name, payments.id as payment_id,payments.payment_sum,')
            ->leftJoin('orders','orders.clients_id = payments.client_id')
            ->leftJoin('clients', 'clients.id = payments.client_id')
            ->where(['orders.status' => '2'])
            ->groupBy('payments.client_id')
            ->orderBy(['payments.created_at'=> SORT_DESC])
            ->asArray()
            ->all();
        $sub_page = [
            ['name' => 'Վճարումներ','address' => '/payments'],
            ['name' => 'Փոխարժեք','address' => '/rates']
        ];
        $date_tab = [];

        return $this->render('statistics',[
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,

            'statistics' => $statistics
        ]);
    }
    public function actionView($id)
    {
        $sub_page = [
            ['name' => 'Վիճակագրություն','address' => '/payments/statistics']
        ];
        $date_tab = [];

        return $this->render('view', [
            'model' => $this->findModel($id),
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,

        ]);
    }

    /**
     * Creates a new Payments model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $have_access = Users::checkPremission(62);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $model = new Payments();
        if ($this->request->isPost) {
            date_default_timezone_set('Asia/Yerevan');
            $post = $this->request->post();
            $model->client_id = $post['Payments']['client_id'];
            $model->payment_sum = $post['Payments']['payment_sum'];
            $model->pay_date = $post['Payments']['pay_date'];
            $model->rate_id = $post['Payments']['rate_id'];
            $model->rate_value = $post['Payments']['rate_value'];
            $model->comment = $post['Payments']['comment'];
            $model->created_at = date('Y-m-d H:i:s');
            $model->updated_at = date('Y-m-d H:i:s');
            $model->save();
            return $this->redirect(['index', 'id' => $model->id]);
        } else {
            $model->loadDefaultValues();
        }
        $sub_page = [
            ['name' => 'Վիճակագրություն','address' => '/payments/statistics']
        ];
        $date_tab = [];

//        echo "<pre>";
        $client = Clients::find()->select('id,name')->asArray()->all();
        $client = ArrayHelper::map($client,'id','name');
        $rates = Rates::find()->select('id,name')->asArray()->all();
        $rates = ArrayHelper::map($rates,'id','name');
//        var_dump($client);
        return $this->render('create', [
            'model' => $model,
            'client' => $client,
            'rates' => $rates,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,

        ]);
    }

    /**
     * Updates an existing Payments model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $have_access = Users::checkPremission(63);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $model = $this->findModel($id);

        if ($this->request->isPost) {
            date_default_timezone_set('Asia/Yerevan');
            $post = $this->request->post();
            $model->client_id = $post['Payments']['client_id'];
            $model->payment_sum = $post['Payments']['payment_sum'];
            $model->pay_date = $post['Payments']['pay_date'];
            $model->rate_id = $post['Payments']['rate_id'];
            $model->rate_value = $post['Payments']['rate_value'];
            $model->comment = $post['Payments']['comment'];
            $model->updated_at = date('Y-m-d H:i:s');
            $model->save();
            return $this->redirect(['index', 'id' => $model->id]);
        }
        $sub_page = [
            ['name' => 'Վիճակագրություն','address' => '/payments/statistics']
        ];
        $date_tab = [];

        $rates = Rates::find()->select('id,name')->asArray()->all();
        $rates = ArrayHelper::map($rates,'id','name');
        $client = Clients::find()->select('id,name')->asArray()->all();
        $client = ArrayHelper::map($client,'id','name');
        return $this->render('update', [
            'model' => $model,
            'client' => $client,
            'rates' => $rates,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,


        ]);
    }

    /**
     * Deletes an existing Payments model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $have_access = Users::checkPremission(64);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $payments = Payments::findOne($id);
        $payments->status = '0';
        $payments->save();
        return $this->redirect(['index']);
    }

    /**
     * Finds the Payments model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Payments the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Payments::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
