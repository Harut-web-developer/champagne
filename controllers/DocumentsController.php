<?php

namespace app\controllers;

use app\models\DocumentItems;
use app\models\Documents;
use app\models\DocumentsSearch;
use app\models\Nomenclature;
use app\models\Log;
use app\models\Notifications;
use app\models\Premissions;
use yii\helpers\Url;
use app\models\Products;
use app\models\Rates;
use app\models\Users;
use app\models\Warehouse;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use function PHPUnit\Framework\exactly;

/**
 * DocumentsController implements the CRUD actions for Documents model.
 */
class DocumentsController extends Controller
{
    public function init()
    {
        parent::init();
        Yii::$app->language = 'hy';
    }

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
     * Lists all Documents models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $have_access = Users::checkPremission(40);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $sub_page = [
            ['name' => 'Պահեստ','address' => '/warehouse'],
            ['name' => 'Անվանակարգ','address' => '/nomenclature'],
            ['name' => 'Ապրանք','address' => '/products'],
            ['name' => 'Տեղեկամատյան','address' => '/log'],
        ];
        $date_tab = [];

        $searchModel = new DocumentsSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,

        ]);
    }

    /**
     * Displays a single Documents model.
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
     * Creates a new Documents model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $session = Yii::$app->session;
        $have_access = Users::checkPremission(37);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $sub_page = [];
        $date_tab = [];

        $model = new Documents();
        $url = Url::to('', 'http');
        $url = str_replace('create', 'view', $url);
        $premission = Premissions::find()
            ->select('name')
            ->where(['id' => 37])
            ->asArray()
            ->one();
        if ($this->request->isPost) {
        echo "<pre>";
            $post = $this->request->post();
            date_default_timezone_set('Asia/Yerevan');
            $model->user_id = $post['Documents']['user_id'];
            $model->warehouse_id = $post['Documents']['warehouse_id'];
            if ($post['Documents']['document_type'] == '3'){
                $model->to_warehouse = $post['Documents']['to_warehouse'];
            }
            $model->rate_id = $post['Documents']['rate_id'];
            $model->rate_value = $post['Documents']['rate_value'];
            $model->document_type = $post['Documents']['document_type'];
            $model->comment = $post['Documents']['comment'];
            $model->date = $post['Documents']['date'];
            $model->created_at = date('Y-m-d H:i:s');
            $model->updated_at = date('Y-m-d H:i:s');
            $model = Documents::getDefVals($model);
            $model->save(false);
                if ($post['Documents']['document_type'] === '1'){
                    for ($i = 0; $i < count($post['document_items']); $i++) {
                        $products = new Products();
                        $products->warehouse_id = $post['Documents']['warehouse_id'];
                        $products->nomenclature_id = $post['document_items'][$i];
                        $products->document_id = $model->id;
                        $products->type = 1;
                        $products->count = intval($post['count_'][$i]);
                        $products->count_balance = intval($post['count_'][$i]);
                        if ($post['aah'] == 'true'){
                            $products->price = floatval($post['pricewithaah'][$i]);
                            $products->AAH = 1;
                        }else{
                            $products->price = floatval($post['price'][$i]);
                            $products->AAH = 0;

                        }
                        $products->created_at = date('Y-m-d H:i:s');
                        $products->updated_at = date('Y-m-d H:i:s');
                        $products->save(false);

                        $document_items = new DocumentItems();
                        $document_items->document_id = $model->id;
                        $document_items->nomenclature_id = $post['document_items'][$i];
                        $document_items->count = $post['count_'][$i];
                        $document_items->price = floatval($post['price'][$i]);
                        $document_items->refuse_product_id = $products->id;
                        $document_items->price_with_aah = floatval($post['pricewithaah'][$i]);
                        $document_items->AAH = $post['aah'];
                        $document_items->created_at = date('Y-m-d H:i:s');
                        $document_items->updated_at = date('Y-m-d H:i:s');
                        $document_items->save(false);
                    }
                }

                if ($post['Documents']['document_type'] == '2'){
                    for ($i = 0; $i < count($post['document_items']); $i++) {
                        $first_product = Products::find()
                            ->where(['and',['nomenclature_id' => $post['document_items'][$i]],
                                ['warehouse_id' => $post['Documents']['warehouse_id']]])
                            ->andWhere(['or',['type' => 1],['type' => 3],['type' => 8]])
                            ->andWhere(['!=', 'count_balance', 0])
                            ->andWhere(['status' => '1'])
                            ->all();
                        $bal = $post['count_'][$i];;
                        foreach ($first_product as $item){
                            if ($item->count_balance - $bal >= 0) {
                                $products = new Products();
                                $item->count_balance -= $bal;
                                $item->save(false);
                                $products->count_balance = 0;
                                $products->parent_id = $item->id;
                                $products->count = $bal;
                                $products->warehouse_id = $post['Documents']['warehouse_id'];
                                $products->nomenclature_id = $post['document_items'][$i];
                                $products->document_id = $model->id;
                                $products->type = 5;
                                if ($post['aah'] == 'true'){
                                    $products->price = floatval($post['pricewithaah'][$i]);
                                    $products->AAH = 1;
                                }else{
                                    $products->price = floatval($post['price'][$i]);
                                    $products->AAH = 0;
                                }
                                $products->created_at = date('Y-m-d H:i:s');
                                $products->updated_at = date('Y-m-d H:i:s');
                                $products->save(false);

                                $document_items = new DocumentItems();
                                $document_items->document_id = $model->id;
                                $document_items->nomenclature_id = $post['document_items'][$i];
                                $document_items->count = $products->count;
                                $document_items->price = floatval($post['price'][$i]);
                                $document_items->refuse_product_id = $products->id;
                                $document_items->price_with_aah = floatval($post['pricewithaah'][$i]);
                                $document_items->AAH = $post['aah'];
                                $document_items->created_at = date('Y-m-d H:i:s');
                                $document_items->updated_at = date('Y-m-d H:i:s');
                                $document_items->save(false);
                                break;
                            }else{
                                $products = new Products();
                                $bal -= $item->count_balance;
                                $products->count = $item->count_balance;
                                $item->count_balance = 0;
                                $item->save(false);
                                $products->count_balance = 0;
                                $products->parent_id = $item->id;
                                $products->warehouse_id = $post['Documents']['warehouse_id'];
                                $products->nomenclature_id = $post['document_items'][$i];
                                $products->document_id = $model->id;
                                $products->type = 5;
                                if ($post['aah'] == 'true'){
                                    $products->price = floatval($post['pricewithaah'][$i]);
                                    $products->AAH = 1;
                                }else{
                                    $products->price = floatval($post['price'][$i]);
                                    $products->AAH = 0;
                                }
                                $products->created_at = date('Y-m-d H:i:s');
                                $products->updated_at = date('Y-m-d H:i:s');
                                $products->save(false);

                                $document_items = new DocumentItems();
                                $document_items->document_id = $model->id;
                                $document_items->nomenclature_id = $post['document_items'][$i];
                                $document_items->count = $products->count;
                                $document_items->price = floatval($post['price'][$i]);
                                $document_items->refuse_product_id = $products->id;
                                $document_items->price_with_aah = floatval($post['pricewithaah'][$i]);
                                $document_items->AAH = $post['aah'];
                                $document_items->created_at = date('Y-m-d H:i:s');
                                $document_items->updated_at = date('Y-m-d H:i:s');
                                $document_items->save(false);
                            }

                        }
                    }
                }
                if ($post['Documents']['document_type'] == '3'){
                    for ($i = 0; $i < count($post['document_items']); $i++) {
                        $first_product = Products::find()
                            ->where(['and',['nomenclature_id' => $post['document_items'][$i]],
                                ['warehouse_id' => $post['Documents']['warehouse_id']]])
                            ->andWhere(['or',['type' => 1],['type' => 3],['type' => 8]])
                            ->andWhere(['!=', 'count_balance', 0])
                            ->andWhere(['status' => '1'])
                            ->all();
                        $bal = $post['count_'][$i];;
                        foreach ($first_product as $item) {
                            if ($item->count_balance - $bal >= 0) {
                                $products = new Products();
                                $item->count_balance -= $bal;
                                $item->save(false);
                                $products->count_balance = 0;
                                $products->parent_id = $item->id;
                                $products->count = $bal;
                                $products->warehouse_id = $post['Documents']['warehouse_id'];
                                $products->nomenclature_id = $post['document_items'][$i];
                                $products->document_id = $model->id;
                                $products->type = 5;
                                if ($post['aah'] == 'true'){
                                    $products->price = floatval($post['pricewithaah'][$i]);
                                    $products->AAH = 1;
                                }else{
                                    $products->price = floatval($post['price'][$i]);
                                    $products->AAH = 0;
                                }
                                $products->created_at = date('Y-m-d H:i:s');
                                $products->updated_at = date('Y-m-d H:i:s');
                                $products->save(false);

                                $to_warehouse_products = new Products();
                                $to_warehouse_products->warehouse_id = $post['Documents']['to_warehouse']; // texapoxvac pahest
                                $to_warehouse_products->nomenclature_id = $post['document_items'][$i];
                                $to_warehouse_products->document_id = $model->id;
                                $to_warehouse_products->type = 3;
                                $to_warehouse_products->count = $bal;
                                $to_warehouse_products->count_balance = $bal;
                                $to_warehouse_products->parent_id = $products->id;
                                if ($post['aah'] == 'true'){
                                    $to_warehouse_products->price = floatval($post['pricewithaah'][$i]);
                                    $to_warehouse_products->AAH = 1;
                                }else{
                                    $to_warehouse_products->price = floatval($post['price'][$i]);
                                    $to_warehouse_products->AAH = 0;
                                }
                                $to_warehouse_products->created_at = date('Y-m-d H:i:s');
                                $to_warehouse_products->updated_at = date('Y-m-d H:i:s');
                                $to_warehouse_products->save(false);

                                $document_items = new DocumentItems();
                                $document_items->document_id = $model->id;
                                $document_items->nomenclature_id = $post['document_items'][$i];
                                $document_items->count = $products->count;
                                $document_items->price = floatval($post['price'][$i]);
                                $document_items->refuse_product_id = $products->id;
                                $document_items->price_with_aah = floatval($post['pricewithaah'][$i]);
                                $document_items->AAH = $post['aah'];
                                $document_items->created_at = date('Y-m-d H:i:s');
                                $document_items->updated_at = date('Y-m-d H:i:s');
                                $document_items->save(false);
                                break;
                            }else{
                                $products = new Products();
                                $bal -= $item->count_balance;
                                $products->count = $item->count_balance;
                                $item->count_balance = 0;
                                $item->save(false);
                                $products->count_balance = 0;
                                $products->parent_id = $item->id;
                                $products->warehouse_id = $post['Documents']['warehouse_id'];
                                $products->nomenclature_id = $post['document_items'][$i];
                                $products->document_id = $model->id;
                                $products->type = 5;
                                if ($post['aah'] == 'true'){
                                    $products->price = floatval($post['pricewithaah'][$i]);
                                    $products->AAH = 1;
                                }else{
                                    $products->price = floatval($post['price'][$i]);
                                    $products->AAH = 0;
                                }
                                $products->created_at = date('Y-m-d H:i:s');
                                $products->updated_at = date('Y-m-d H:i:s');
                                $products->save(false);

                                $to_warehouse_products = new Products();
                                $to_warehouse_products->warehouse_id = $post['Documents']['to_warehouse']; // texapoxvac pahest
                                $to_warehouse_products->nomenclature_id = $post['document_items'][$i];
                                $to_warehouse_products->document_id = $model->id;
                                $to_warehouse_products->type = 3;
                                $to_warehouse_products->count = $products->count;
                                $to_warehouse_products->count_balance = $products->count;
                                $to_warehouse_products->parent_id = $products->id;
                                if ($post['aah'] == 'true'){
                                    $to_warehouse_products->price = floatval($post['pricewithaah'][$i]);
                                    $to_warehouse_products->AAH = 1;
                                }else{
                                    $to_warehouse_products->price = floatval($post['price'][$i]);
                                    $to_warehouse_products->AAH = 0;
                                }
                                $to_warehouse_products->created_at = date('Y-m-d H:i:s');
                                $to_warehouse_products->updated_at = date('Y-m-d H:i:s');
                                $to_warehouse_products->save(false);

                                $document_items = new DocumentItems();
                                $document_items->document_id = $model->id;
                                $document_items->nomenclature_id = $post['document_items'][$i];
                                $document_items->count = $products->count;
                                $document_items->price = floatval($post['price'][$i]);
                                $document_items->refuse_product_id = $products->id;
                                $document_items->price_with_aah = floatval($post['pricewithaah'][$i]);
                                $document_items->AAH = $post['aah'];
                                $document_items->created_at = date('Y-m-d H:i:s');
                                $document_items->updated_at = date('Y-m-d H:i:s');
                                $document_items->save(false);
                            }
                        }
                    }
                }
                if($post['Documents']['document_type'] === '4'){
                    for ($i = 0; $i < count($post['document_items']); $i++) {
                        $first_product = Products::find()
                            ->where(['and',['nomenclature_id' => $post['document_items'][$i]],
                                ['warehouse_id' => $post['Documents']['warehouse_id']]])
                            ->andWhere(['or',['type' => 1],['type' => 3],['type' => 8]])
                            ->andWhere(['!=', 'count_balance', 0])
                            ->andWhere(['status' => '1'])
                            ->all();
                        $bal = $post['count_'][$i];;
                        foreach ($first_product as $item) {
                            if ($item->count_balance - $bal >= 0) {
                                $products = new Products();
                                $item->count_balance -= $bal;
                                $item->save(false);
                                $products->count_balance = 0;
                                $products->parent_id = $item->id;
                                $products->count = $bal;
                                $products->warehouse_id = $post['Documents']['warehouse_id'];
                                $products->nomenclature_id = $post['document_items'][$i];
                                $products->document_id = $model->id;
                                $products->type = 4;
                                if ($post['aah'] == 'true'){
                                    $products->price = floatval($post['pricewithaah'][$i]);
                                    $products->AAH = 1;
                                }else{
                                    $products->price = floatval($post['price'][$i]);
                                    $products->AAH = 0;
                                }
                                $products->created_at = date('Y-m-d H:i:s');
                                $products->updated_at = date('Y-m-d H:i:s');
                                $products->save(false);

                                $document_items = new DocumentItems();
                                $document_items->document_id = $model->id;
                                $document_items->nomenclature_id = $post['document_items'][$i];
                                $document_items->count = $products->count;
                                $document_items->price = floatval($post['price'][$i]);
                                $document_items->refuse_product_id = $products->id;
                                $document_items->price_with_aah = floatval($post['pricewithaah'][$i]);
                                $document_items->AAH = $post['aah'];
                                $document_items->created_at = date('Y-m-d H:i:s');
                                $document_items->updated_at = date('Y-m-d H:i:s');
                                $document_items->save(false);
                                break;
                            }else{
                                $products = new Products();
                                $bal -= $item->count_balance;
                                $products->count = $item->count_balance;
                                $item->count_balance = 0;
                                $item->save(false);
                                $products->count_balance = 0;
                                $products->parent_id = $item->id;
                                $products->warehouse_id = $post['Documents']['warehouse_id'];
                                $products->nomenclature_id = $post['document_items'][$i];
                                $products->document_id = $model->id;
                                $products->type = 4;
                                if ($post['aah'] == 'true'){
                                    $products->price = floatval($post['pricewithaah'][$i]);
                                    $products->AAH = 1;
                                }else{
                                    $products->price = floatval($post['price'][$i]);
                                    $products->AAH = 0;
                                }
                                $products->created_at = date('Y-m-d H:i:s');
                                $products->updated_at = date('Y-m-d H:i:s');
                                $products->save(false);

                                $document_items = new DocumentItems();
                                $document_items->document_id = $model->id;
                                $document_items->nomenclature_id = $post['document_items'][$i];
                                $document_items->count = $products->count;
                                $document_items->price = floatval($post['price'][$i]);
                                $document_items->refuse_product_id = $products->id;
                                $document_items->price_with_aah = floatval($post['pricewithaah'][$i]);
                                $document_items->AAH = $post['aah'];
                                $document_items->created_at = date('Y-m-d H:i:s');
                                $document_items->updated_at = date('Y-m-d H:i:s');
                                $document_items->save(false);
                            }
                        }
                    }
                }
            $model_new = [];
            foreach ($document_items as $name => $value) {
                $model_new[$name] = $value;
            }
            foreach ($model as $name => $value) {
                $model_new[$name] = $value;
            }
            $session = Yii::$app->session;
            $document_tipe = (object) [
                '1' => 'մուտքի',
                '2' => 'ելքի',
                '3' => 'տեղափոխման',
                '4' => 'խոտանի',
                '6' => 'վերադարձի',
                '7' => 'մերժման',
            ];
            $user_name = Users::find()->select('*')->where(['id' => $session['user_id']])->asArray()->one();
            $text = $user_name['name'] . '(ն\ը) ստեղծել է ' . $document_tipe->{$post['Documents']['document_type']} . ' փաստաթուղթ։';
            Notifications::createNotifications('Ստեղծել փաստաթուղթ', $text,'documentscreate');
            Log::afterSaves('Create', $model_new, '', $url.'?'.'id'.'='.$model->id, $premission);
            return $this->redirect(['index', 'id' => $model->id]);
        } else {
            $model->loadDefaultValues();
        }
        if ($session['role_id'] == 1){
            $users = Users::find()->select('id,name')->where(['status' => '1'])->andWhere(['role_id' => '4'])->asArray()->all();
            $users = ArrayHelper::map($users,'id','name');
        }elseif($session['role_id'] == 4){
            $users = Users::find()->select('id,name')->where(['status' => '1'])->andWhere(['id' => $session['user_id']])->asArray()->all();
            $users = ArrayHelper::map($users,'id','name');
        }
        $warehouse = Warehouse::find()->select('id,name')->where(['status' => '1'])->asArray()->all();
        $warehouse =  ArrayHelper::map($warehouse,'id','name');
        $rates = Rates::find()->select('id,name')->where(['status' => '1'])->asArray()->all();
        $rates = ArrayHelper::map($rates,'id','name');
        $query = Nomenclature::find();
        $countQuery = clone $query;
        $total = $countQuery->count();
        $nomenclatures = $query->select('nomenclature.id,nomenclature.image,nomenclature.name,nomenclature.price,
        nomenclature.cost,products.id as products_id,products.count,')
            ->leftJoin('products','nomenclature.id = products.nomenclature_id')
            ->offset(0)
            ->groupBy('nomenclature.id')
            ->limit(10)
//            ->orderBy(['nomenclature.id'=> SORT_DESC])
            ->asArray()
            ->all();
            $to_warehouse =  Warehouse::find()->select('id,name')->where(['status' => '1'])->asArray()->all();
            $to_warehouse = ArrayHelper::map($to_warehouse,'id','name');
        return $this->render('create', [
            'model' => $model,
            'users' => $users,
            'warehouse' => $warehouse,
            'rates' => $rates,
            'nomenclatures' => $nomenclatures,
            'total' => $total,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,
            'to_warehouse' => $to_warehouse,
        ]);
    }

    public function actionCreateFields()
    {
        $have_access = Users::checkPremission(71);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $sub_page = [
            ['name' => 'Պահեստ','address' => '/warehouse'],
            ['name' => 'Փաստաթղթեր','address' => '/documents'],
            ['name' => 'Անվանակարգ','address' => '/nomenclature'],
            ['name' => 'Ապրանք','address' => '/products'],
            ['name' => 'Տեղեկամատյան','address' => '/log'],
        ];
        $date_tab = [];

        $model = new Documents();
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
     * Updates an existing Documents model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionGetNomiclature(){
        $page = $_GET['paging'] ?? 1;
        $search_name = $_GET['nomenclature'] ?? false;
        $pageSize = 10;
        $offset = ($page-1) * $pageSize;
        $_GET['warehouse_id'] = $_GET['warehouse_id'] ?? $_POST['warehouse_id'];
        $_POST['warehouse_id'] = $_POST['warehouse_id'] ?? $_GET['warehouse_id'];
        $warehouse_id = $_POST['warehouse_id'] ?? $_GET['warehouse_id'];
        $_GET['documents_type'] = $_GET['documents_type'] ?? $_POST['documents_type'];
        $_POST['documents_type'] = $_POST['documents_type'] ?? $_GET['documents_type'];
        $document_type = $_POST['documents_type'];
        $query = Nomenclature::find();
        $countQuery = clone $query;
        $nomenclatures = $query->select('*')
            ->where(['status' => '1'])
            ->groupBy('nomenclature.id');
        if ($search_name){
            $nomenclatures->andWhere(['like', 'nomenclature.name', $search_name])
                ->offset(0);
        }else{
            $nomenclatures->offset($offset)
                ->limit($pageSize);
        }
        $nomenclatures = $nomenclatures
            ->asArray()
            ->all();
        $total = $countQuery->count();
        if ($document_type != 1){
            $query = Products::find();
            $nomenclatures = $query->select('SUM(count_balance) as count_balance,products.id,nomenclature.id as nomenclature_id,
                nomenclature.image,nomenclature.name,nomenclature.cost,products.count,products.price')
                ->leftJoin('nomenclature','nomenclature.id = products.nomenclature_id')
                ->where(['and',['products.status' => 1,'nomenclature.status' => 1]])
                ->andWhere(['or',['products.type' => 1],['products.type' => 3],['products.type' => 8]])
                ->andWhere(['products.warehouse_id' => intval($warehouse_id)])
                ->groupBy('products.nomenclature_id')
                ->having(['!=', 'SUM(count_balance)', 0]);

            if ($search_name){
                $nomenclatures->andWhere(['like', 'nomenclature.name', $search_name])
                    ->offset(0);
            }else{
                $nomenclatures->offset($offset)
                    ->limit($pageSize);
            }
            $total = $nomenclatures->count();
            $nomenclatures = $nomenclatures
                ->asArray()
                ->all();
        }
        $id_count = $_POST['id_count'] ?? [];
        return $this->renderAjax('get-nom', [
            'nomenclatures' => $nomenclatures,
            'id_count' => $id_count ,
            'total' => $total,
            'search_name' => $search_name,
        ]);
    }

    public function actionGetNomiclatureUpdate(){
        $page = $_GET['paging'] ?? 1;
        $urlId = intval($_POST['urlId']);
        $_GET['warehouse_id'] = $_GET['warehouse_id'] ?? $_POST['warehouse_id'];
        $_POST['warehouse_id'] = $_POST['warehouse_id'] ?? $_GET['warehouse_id'];
        $warehouse_id = $_POST['warehouse_id'] ?? $_GET['warehouse_id'];
        $_GET['documents_type'] = $_GET['documents_type'] ?? $_POST['documents_type'];
        $_POST['documents_type'] = $_POST['documents_type'] ?? $_GET['documents_type'];
        $document_type = $_POST['documents_type'];
        $search_name = $_GET['nomenclature'] ?? false;
        $pageSize = 10;
        $offset = ($page-1) * $pageSize;
        $document_items = DocumentItems::find()->select('document_items.*,nomenclature.name, nomenclature.id as nom_id')
            ->leftJoin('nomenclature','document_items.nomenclature_id = nomenclature.id')
            ->where(['document_items.document_id' => $urlId])
            ->asArray()->all();
        $query = Nomenclature::find();
        $nomenclatures = $query->select('*')
//            ->where(['not in','id' , array_column($document_items,'nomenclature_id')])
            ->andWhere(['status' => '1'])
            ->groupBy('nomenclature.id');
        $total = $query->count();
        if ($search_name){
            $nomenclatures->andWhere(['like', 'nomenclature.name', $search_name])
                ->offset(0);
        }else{
            $nomenclatures->offset($offset)
                ->limit($pageSize);
        }
        $nomenclatures = $nomenclatures
            ->asArray()
            ->all();
        if ($document_type != "Մուտք"){
            $query = Products::find();
            $nomenclatures = $query->select('products.id,nomenclature.id as nomenclature_id,
                nomenclature.image,nomenclature.name,nomenclature.cost,products.count,products.price')
                ->leftJoin('nomenclature','nomenclature.id = products.nomenclature_id')
                ->where(['and',['products.status' => 1,'nomenclature.status' => 1,'products.type' => 1]])
                ->andWhere(['products.warehouse_id' => intval($warehouse_id)]);
//                ->groupBy('products.nomenclature_id');
            if ($search_name){
                $nomenclatures->andWhere(['like', 'nomenclature.name', $search_name])
                    ->offset(0);
            }else{
                $nomenclatures->offset($offset)
                    ->limit($pageSize);
            }
            $total = $nomenclatures->count();
            $nomenclatures = $nomenclatures
                ->asArray()
                ->all();
        }
        $id_count = $_POST['id_count'] ?? [];
        return $this->renderAjax('get-nom', [
            'nomenclatures' => $nomenclatures,
            'id_count' => $id_count ,
            'total' => $total,
            'search_name' => $search_name,
            'urlId' => $urlId,
        ]);
    }
    public function actionUpdate($id)
    {
        $session = Yii::$app->session;
        $have_access = Users::checkPremission(38);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $model = $this->findModel($id);
        $sub_page = [];
        $date_tab = [];

        $url = Url::to('', 'http');
        $oldattributes = Documents::find()
            ->select('*')
            ->where(['id' => $id])
            ->asArray()
            ->one();
        $premission = Premissions::find()
            ->select('name')
            ->where(['id' => 38])
            ->asArray()
            ->one();
        if ($this->request->isPost) {
            echo "<pre>";
            $post = $this->request->post();
            date_default_timezone_set('Asia/Yerevan');
            $model->user_id = $post['Documents']['user_id'];
            $model->warehouse_id = $post['Documents']['warehouse_id'];
            if ($post['Documents']['document_type'] == 'Տեղափոխություն'){
                $model->to_warehouse = $post['Documents']['to_warehouse'];
            }
            $model->rate_id = $post['Documents']['rate_id'];
            $model->rate_value = $post['Documents']['rate_value'];
            $model->comment = $post['Documents']['comment'];
            $model->date = $post['Documents']['date'];
            $model->updated_at = date('Y-m-d H:i:s');
            $model->save();
            Log::afterSaves('Update', $model, $oldattributes, $url, $premission);
            $items = $post['document_items'];
            foreach ($items as $j => $item){
                if ($post['count_'][$j]) {
                    if ($item != 'null') {
//                        $document_items_update = DocumentItems::findOne(intval($item));
//                        $document_items_update->document_id = $id;
//                        $document_items_update->nomenclature_id = $post['items'][$j];
//                        $document_items_update->count = intval($post['count_'][$j]);
//                        $document_items_update->price = floatval($post['price'][$j]);
//                        $document_items_update->price_with_aah = floatval($post['pricewithaah'][$j]);
//                        $document_items_update->AAH = $post['aah'];
//                        $document_items_update->updated_at = date('Y-m-d H:i:s');
//                        $document_items_update->save();
//                        if ($post['Documents']['document_type'] === 'Մուտք') {
//                            $product_write_in = Products::find()->select('products.*')
//                                ->where(['and', ['document_id' => $model->id, 'type' => 1, 'nomenclature_id' => $post['items'][$j]]])->one();
//                            $product_write_in->warehouse_id = $post['Documents']['warehouse_id'];
//                            $product_write_in->nomenclature_id = $post['items'][$j];
//                            $product_write_in->document_id = $model->id;
//                            $product_write_in->type = 1;
//                            $product_write_in->count = intval($post['count_'][$j]);
//                            if ($post['aah'] == 'true') {
//                                $product_write_in->price = floatval($post['pricewithaah'][$j]);
//                                $product_write_in->AAH = 1;
//                            } else {
//                                $product_write_in->price = floatval($post['price'][$j]);
//                                $product_write_in->AAH = 0;
//                            }
//                            $product_write_in->updated_at = date('Y-m-d H:i:s');
//                            $product_write_in->save(false);
//                        }
//                        if ($post['Documents']['document_type'] === 'Ելք') {
//                            $product_write_out = Products::find()->select('products.*')
//                                ->where(['and', ['document_id' => $model->id, 'type' => 5, 'nomenclature_id' => $post['items'][$j]]])->one();
//                            $product_write_out->warehouse_id = $post['Documents']['warehouse_id'];
//                            $product_write_out->nomenclature_id = $post['items'][$j];
//                            $product_write_out->document_id = $model->id;
//                            $product_write_out->type = 5;
//                            $product_write_out->count = -intval($post['count_'][$j]);
//                            if ($post['aah'] == 'true') {
//                                $product_write_out->price = floatval($post['pricewithaah'][$j]);
//                                $product_write_out->AAH = 1;
//                            } else {
//                                $product_write_out->price = floatval($post['price'][$j]);
//                                $product_write_out->AAH = 0;
//                            }
//                            $product_write_out->updated_at = date('Y-m-d H:i:s');
//                            $product_write_out->save(false);
//                        }
//                        if ($post['Documents']['document_type'] === 'Տեղափոխություն') {
//                            $product_write_out = Products::find()->select('products.*')
//                                ->where(['and', ['warehouse_id' => $post['Documents']['warehouse_id'], 'document_id' => $model->id, 'type' => 3, 'nomenclature_id' => $post['items'][$j]]])->one();
//                            $product_write_out->warehouse_id = $post['Documents']['warehouse_id'];
//                            $product_write_out->nomenclature_id = $post['items'][$j];
//                            $product_write_out->document_id = $model->id;
//                            $product_write_out->type = 3;
//                            $product_write_out->count = -intval($post['count_'][$j]);
//                            if ($post['aah'] == 'true') {
//                                $product_write_out->price = floatval($post['pricewithaah'][$j]);
//                                $product_write_out->AAH = 1;
//                            } else {
//                                $product_write_out->price = floatval($post['price'][$j]);
//                                $product_write_out->AAH = 0;
//                            }
//                            $product_write_out->updated_at = date('Y-m-d H:i:s');
//                            $product_write_out->save(false);
//
//                            $product_write_in = Products::find()->select('products.*')
//                                ->where(['and', ['warehouse_id' => $post['Documents']['to_warehouse'], 'document_id' => $model->id, 'type' => 3, 'nomenclature_id' => $post['items'][$j]]])->one();
//                            $product_write_in->warehouse_id = $post['Documents']['to_warehouse'];
//                            $product_write_in->nomenclature_id = $post['items'][$j];
//                            $product_write_in->document_id = $model->id;
//                            $product_write_in->type = 3;
//                            $product_write_in->count = intval($post['count_'][$j]);
//                            if ($post['aah'] == 'true') {
//                                $product_write_in->price = floatval($post['pricewithaah'][$j]);
//                                $product_write_in->AAH = 1;
//                            } else {
//                                $product_write_in->price = floatval($post['price'][$j]);
//                                $product_write_in->AAH = 0;
//                            }
//                            $product_write_in->updated_at = date('Y-m-d H:i:s');
//                            $product_write_in->save(false);
//                        }
//                        if ($post['Documents']['document_type'] === 'Խոտան') {
//                            $product_write_out = Products::find()->select('products.*')
//                                ->where(['and', ['document_id' => $model->id, 'type' => 4, 'nomenclature_id' => $post['items'][$j]]])->one();
//                            $product_write_out->warehouse_id = $post['Documents']['warehouse_id'];
//                            $product_write_out->nomenclature_id = $post['items'][$j];
//                            $product_write_out->document_id = $model->id;
//                            $product_write_out->type = 4;
//                            $product_write_out->count = -intval($post['count_'][$j]);
//                            if ($post['aah'] == 'true') {
//                                $product_write_out->price = floatval($post['pricewithaah'][$j]);
//                                $product_write_out->AAH = 1;
//                            } else {
//                                $product_write_out->price = floatval($post['price'][$j]);
//                                $product_write_out->AAH = 0;
//                            }
//                            $product_write_out->updated_at = date('Y-m-d H:i:s');
//                            $product_write_out->save(false);
//                        }

                    } else {


                        if ($post['Documents']['document_type'] === 'Մուտք') {
                            $products = new Products();
                            $products->warehouse_id = $post['Documents']['warehouse_id'];
                            $products->nomenclature_id = $post['items'][$j];
                            $products->document_id = $model->id;
                            $products->type = 1;
                            $products->count = intval($post['count_'][$j]);
                            $products->count_balance = intval($post['count_'][$j]);
                            if ($post['aah'] == 'true') {
                                $products->price = floatval($post['pricewithaah'][$j]);
                                $products->AAH = 1;
                            } else {
                                $products->price = floatval($post['price'][$j]);
                                $products->AAH = 0;
                            }
                            $products->created_at = date('Y-m-d H:i:s');
                            $products->updated_at = date('Y-m-d H:i:s');
                            $products->save(false);

                            $document_items_update = new DocumentItems();
                            $document_items_update->document_id = $id;
                            $document_items_update->nomenclature_id = $post['items'][$j];
                            $document_items_update->count = intval($post['count_'][$j]);
                            $document_items_update->price = floatval($post['price'][$j]);
                            $document_items_update->refuse_product_id = $products->id;
                            $document_items_update->price_with_aah = floatval($post['pricewithaah'][$j]);
                            $document_items_update->AAH = $post['aah'];
                            $document_items_update->created_at = date('Y-m-d H:i:s');
                            $document_items_update->updated_at = date('Y-m-d H:i:s');
                            $document_items_update->save();
                        }
                        if ($post['Documents']['document_type'] === 'Ելք') {
                            $first_product = Products::find()
                                ->where(['and',['nomenclature_id' => $post['items'][$j]],
                                    ['warehouse_id' => $post['Documents']['warehouse_id']]])
                                ->andWhere(['or',['type' => 1],['type' => 3],['type' => 8]])
                                ->andWhere(['!=', 'count_balance', 0])
                                ->andWhere(['status' => '1'])
                                ->all();
//                            var_dump($first_product);
//                            exit();
                            $bal = $post['count_'][$j];;
                            foreach ($first_product as $item) {
                                if ($item->count_balance - $bal >= 0) {
                                    $products = new Products();
                                    $item->count_balance -= $bal;
                                    $item->save(false);
                                    $products->count_balance = 0;
                                    $products->parent_id = $item->id;
                                    $products->count = $bal;
                                    $products->warehouse_id = $post['Documents']['warehouse_id'];
                                    $products->nomenclature_id = $post['items'][$j];
                                    $products->document_id = $model->id;
                                    $products->type = 5;
                                    if ($post['aah'] == 'true'){
                                        $products->price = floatval($post['pricewithaah'][$j]);
                                        $products->AAH = 1;
                                    }else{
                                        $products->price = floatval($post['price'][$j]);
                                        $products->AAH = 0;
                                    }
                                    $products->created_at = date('Y-m-d H:i:s');
                                    $products->updated_at = date('Y-m-d H:i:s');
                                    $products->save(false);

                                    $document_items_update = new DocumentItems();
                                    $document_items_update->document_id = $id;
                                    $document_items_update->nomenclature_id = $post['items'][$j];
                                    $document_items_update->count = $products->count;
                                    $document_items_update->price = floatval($post['price'][$j]);
                                    $document_items_update->refuse_product_id = $products->id;
                                    $document_items_update->price_with_aah = floatval($post['pricewithaah'][$j]);
                                    $document_items_update->AAH = $post['aah'];
                                    $document_items_update->created_at = date('Y-m-d H:i:s');
                                    $document_items_update->updated_at = date('Y-m-d H:i:s');
                                    $document_items_update->save();
                                    break;
                                }else{
                                    $products = new Products();
                                    $bal -= $item->count_balance;
                                    $products->count = $item->count_balance;
                                    $item->count_balance = 0;
                                    $item->save(false);
                                    $products->count_balance = 0;
                                    $products->parent_id = $item->id;
                                    $products->warehouse_id = $post['Documents']['warehouse_id'];
                                    $products->nomenclature_id = $post['items'][$j];
                                    $products->document_id = $model->id;
                                    $products->type = 5;
                                    if ($post['aah'] == 'true'){
                                        $products->price = floatval($post['pricewithaah'][$j]);
                                        $products->AAH = 1;
                                    }else{
                                        $products->price = floatval($post['price'][$j]);
                                        $products->AAH = 0;
                                    }
                                    $products->created_at = date('Y-m-d H:i:s');
                                    $products->updated_at = date('Y-m-d H:i:s');
                                    $products->save(false);

                                    $document_items_update = new DocumentItems();
                                    $document_items_update->document_id = $id;
                                    $document_items_update->nomenclature_id = $post['items'][$j];
                                    $document_items_update->count = $products->count;
                                    $document_items_update->price = floatval($post['price'][$j]);
                                    $document_items_update->refuse_product_id = $products->id;
                                    $document_items_update->price_with_aah = floatval($post['pricewithaah'][$j]);
                                    $document_items_update->AAH = $post['aah'];
                                    $document_items_update->created_at = date('Y-m-d H:i:s');
                                    $document_items_update->updated_at = date('Y-m-d H:i:s');
                                    $document_items_update->save();
                                }
                            }
                        }
                        if ($post['Documents']['document_type'] === 'Տեղափոխություն') {
                            $first_product = Products::find()
                                ->where(['and',['nomenclature_id' => $post['items'][$j]],
                                    ['warehouse_id' => $post['Documents']['warehouse_id']]])
                                ->andWhere(['or',['type' => 1],['type' => 3],['type' => 8]])
                                ->andWhere(['!=', 'count_balance', 0])
                                ->andWhere(['status' => '1'])
                                ->all();
                            $bal = $post['count_'][$j];;
                            foreach ($first_product as $item) {
                                if ($item->count_balance - $bal >= 0) {
                                    $products = new Products();
                                    $item->count_balance -= $bal;
                                    $item->save(false);
                                    $products->count_balance = 0;
                                    $products->parent_id = $item->id;
                                    $products->count = $bal;
                                    $products->warehouse_id = $post['Documents']['warehouse_id'];
                                    $products->nomenclature_id = $post['items'][$j];
                                    $products->document_id = $model->id;
                                    $products->type = 5;
                                    if ($post['aah'] == 'true'){
                                        $products->price = floatval($post['pricewithaah'][$j]);
                                        $products->AAH = 1;
                                    }else{
                                        $products->price = floatval($post['price'][$j]);
                                        $products->AAH = 0;
                                    }
                                    $products->created_at = date('Y-m-d H:i:s');
                                    $products->updated_at = date('Y-m-d H:i:s');
                                    $products->save(false);

                                    $to_warehouse_products = new Products();
                                    $to_warehouse_products->warehouse_id = $post['Documents']['to_warehouse']; // texapoxvac pahest
                                    $to_warehouse_products->nomenclature_id = $post['items'][$j];
                                    $to_warehouse_products->document_id = $model->id;
                                    $to_warehouse_products->type = 3;
                                    $to_warehouse_products->count = $bal;
                                    $to_warehouse_products->count_balance = $bal;
                                    $to_warehouse_products->parent_id = $products->id;
                                    if ($post['aah'] == 'true'){
                                        $to_warehouse_products->price = floatval($post['pricewithaah'][$j]);
                                        $to_warehouse_products->AAH = 1;
                                    }else{
                                        $to_warehouse_products->price = floatval($post['price'][$j]);
                                        $to_warehouse_products->AAH = 0;
                                    }
                                    $to_warehouse_products->created_at = date('Y-m-d H:i:s');
                                    $to_warehouse_products->updated_at = date('Y-m-d H:i:s');
                                    $to_warehouse_products->save(false);

                                    $document_items_update = new DocumentItems();
                                    $document_items_update->document_id = $id;
                                    $document_items_update->nomenclature_id = $post['items'][$j];
                                    $document_items_update->count = $products->count;
                                    $document_items_update->price = floatval($post['price'][$j]);
                                    $document_items_update->refuse_product_id = $products->id;
                                    $document_items_update->price_with_aah = floatval($post['pricewithaah'][$j]);
                                    $document_items_update->AAH = $post['aah'];
                                    $document_items_update->created_at = date('Y-m-d H:i:s');
                                    $document_items_update->updated_at = date('Y-m-d H:i:s');
                                    $document_items_update->save();
                                    break;
                                }else{
                                    $products = new Products();
                                    $bal -= $item->count_balance;
                                    $products->count = $item->count_balance;
                                    $item->count_balance = 0;
                                    $item->save(false);
                                    $products->count_balance = 0;
                                    $products->parent_id = $item->id;
                                    $products->warehouse_id = $post['Documents']['warehouse_id'];
                                    $products->nomenclature_id = $post['items'][$j];
                                    $products->document_id = $model->id;
                                    $products->type = 5;
                                    if ($post['aah'] == 'true'){
                                        $products->price = floatval($post['pricewithaah'][$j]);
                                        $products->AAH = 1;
                                    }else{
                                        $products->price = floatval($post['price'][$j]);
                                        $products->AAH = 0;
                                    }
                                    $products->created_at = date('Y-m-d H:i:s');
                                    $products->updated_at = date('Y-m-d H:i:s');
                                    $products->save(false);

                                    $to_warehouse_products = new Products();
                                    $to_warehouse_products->warehouse_id = $post['Documents']['to_warehouse']; // texapoxvac pahest
                                    $to_warehouse_products->nomenclature_id = $post['items'][$j];
                                    $to_warehouse_products->document_id = $model->id;
                                    $to_warehouse_products->type = 3;
                                    $to_warehouse_products->count = $products->count;
                                    $to_warehouse_products->count_balance = $products->count;
                                    $to_warehouse_products->parent_id = $products->id;
                                    if ($post['aah'] == 'true'){
                                        $to_warehouse_products->price = floatval($post['pricewithaah'][$j]);
                                        $to_warehouse_products->AAH = 1;
                                    }else{
                                        $to_warehouse_products->price = floatval($post['price'][$j]);
                                        $to_warehouse_products->AAH = 0;
                                    }
                                    $to_warehouse_products->created_at = date('Y-m-d H:i:s');
                                    $to_warehouse_products->updated_at = date('Y-m-d H:i:s');
                                    $to_warehouse_products->save(false);

                                    $document_items_update = new DocumentItems();
                                    $document_items_update->document_id = $id;
                                    $document_items_update->nomenclature_id = $post['items'][$j];
                                    $document_items_update->count = $products->count;
                                    $document_items_update->price = floatval($post['price'][$j]);
                                    $document_items_update->refuse_product_id = $products->id;
                                    $document_items_update->price_with_aah = floatval($post['pricewithaah'][$j]);
                                    $document_items_update->AAH = $post['aah'];
                                    $document_items_update->created_at = date('Y-m-d H:i:s');
                                    $document_items_update->updated_at = date('Y-m-d H:i:s');
                                    $document_items_update->save();
                                }
                            }
                        }
                        if ($post['Documents']['document_type'] === 'Խոտան') {
                            $first_product = Products::find()
                                ->where(['and',['nomenclature_id' => $post['items'][$j]],
                                    ['warehouse_id' => $post['Documents']['warehouse_id']]])
                                ->andWhere(['or',['type' => 1],['type' => 3],['type' => 8]])
                                ->andWhere(['!=', 'count_balance', 0])
                                ->andWhere(['status' => '1'])
                                ->all();
                            $bal = $post['count_'][$j];;
                            foreach ($first_product as $item) {
                                if ($item->count_balance - $bal >= 0) {
                                    $products = new Products();
                                    $item->count_balance -= $bal;
                                    $item->save(false);
                                    $products->count_balance = 0;
                                    $products->parent_id = $item->id;
                                    $products->count = $bal;
                                    $products->warehouse_id = $post['Documents']['warehouse_id'];
                                    $products->nomenclature_id = $post['items'][$j];
                                    $products->document_id = $model->id;
                                    $products->type = 4;
                                    if ($post['aah'] == 'true'){
                                        $products->price = floatval($post['pricewithaah'][$j]);
                                        $products->AAH = 1;
                                    }else{
                                        $products->price = floatval($post['price'][$j]);
                                        $products->AAH = 0;
                                    }
                                    $products->created_at = date('Y-m-d H:i:s');
                                    $products->updated_at = date('Y-m-d H:i:s');
                                    $products->save(false);

                                    $document_items_update = new DocumentItems();
                                    $document_items_update->document_id = $id;
                                    $document_items_update->nomenclature_id = $post['items'][$j];
                                    $document_items_update->count = $products->count;
                                    $document_items_update->price = floatval($post['price'][$j]);
                                    $document_items_update->refuse_product_id = $products->id;
                                    $document_items_update->price_with_aah = floatval($post['pricewithaah'][$j]);
                                    $document_items_update->AAH = $post['aah'];
                                    $document_items_update->created_at = date('Y-m-d H:i:s');
                                    $document_items_update->updated_at = date('Y-m-d H:i:s');
                                    $document_items_update->save();
                                    break;
                                }else{
                                    $products = new Products();
                                    $bal -= $item->count_balance;
                                    $products->count = $item->count_balance;
                                    $item->count_balance = 0;
                                    $item->save(false);
                                    $products->count_balance = 0;
                                    $products->parent_id = $item->id;
                                    $products->warehouse_id = $post['Documents']['warehouse_id'];
                                    $products->nomenclature_id = $post['items'][$j];
                                    $products->document_id = $model->id;
                                    $products->type = 4;
                                    if ($post['aah'] == 'true'){
                                        $products->price = floatval($post['pricewithaah'][$j]);
                                        $products->AAH = 1;
                                    }else{
                                        $products->price = floatval($post['price'][$j]);
                                        $products->AAH = 0;
                                    }
                                    $products->created_at = date('Y-m-d H:i:s');
                                    $products->updated_at = date('Y-m-d H:i:s');
                                    $products->save(false);

                                    $document_items_update = new DocumentItems();
                                    $document_items_update->document_id = $id;
                                    $document_items_update->nomenclature_id = $post['items'][$j];
                                    $document_items_update->count = $products->count;
                                    $document_items_update->price = floatval($post['price'][$j]);
                                    $document_items_update->refuse_product_id = $products->id;
                                    $document_items_update->price_with_aah = floatval($post['pricewithaah'][$j]);
                                    $document_items_update->AAH = $post['aah'];
                                    $document_items_update->created_at = date('Y-m-d H:i:s');
                                    $document_items_update->updated_at = date('Y-m-d H:i:s');
                                    $document_items_update->save();
                                }
                            }
                        }
                        //                    $document_items_update = new DocumentItems();
                        //                    $document_items_update->document_id = $id;
                        //                    $document_items_update->save();
                    }
                }
            }
            $model_new = [];
            foreach ($document_items_update as $name => $value) {
                $model_new[$name] = $value;
            }
            foreach ($model as $name => $value) {
                $model_new[$name] = $value;
            }
            Log::afterSaves('Update', $model, $oldattributes, $url, $premission);
            return $this->redirect(['index', 'id' => $model->id]);
        }
        $get_documents_id = Documents::findOne($id);
        if ($session['role_id'] == 1){
            $users = Users::find()->select('id,name')->where(['status' => '1'])->andWhere(['role_id' => '4'])->asArray()->all();
            $users = ArrayHelper::map($users,'id','name');
        }elseif($session['role_id'] == 4){
            $users = Users::find()->select('id,name')->where(['status' => '1'])->andWhere(['id' => $session['user_id']])->asArray()->all();
            $users = ArrayHelper::map($users,'id','name');
        }
        $warehouse = Warehouse::find()->select('id,name')->where(['id' => $get_documents_id->warehouse_id])->asArray()->all();
        $warehouse =  ArrayHelper::map($warehouse,'id','name');
        $to_warehouse =  Warehouse::find()->select('id,name')->where(['id' => $get_documents_id->to_warehouse])->andWhere(['status' => '1'])->asArray()->all();
        $to_warehouse = ArrayHelper::map($to_warehouse,'id','name');
        $rates = Rates::find()->select('id,name')->asArray()->all();
        $rates = ArrayHelper::map($rates,'id','name');

        $document_items = DocumentItems::find()->select('document_items.*,nomenclature.name, nomenclature.id as nom_id')
            ->leftJoin('nomenclature','document_items.nomenclature_id = nomenclature.id')
            ->where(['document_items.document_id' => $id])
            ->andWhere(['document_items.status' => '1'])
            ->asArray()->all();

        $aah = DocumentItems::find()->select('AAH')->where(['document_id' => $id])->asArray()->one();
        return $this->render('update', [
            'model' => $model,
            'users' => $users,
            'warehouse' => $warehouse,
            'rates' => $rates,
            'document_items' => $document_items,
            'aah' => $aah,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,
            'to_warehouse' => $to_warehouse,
        ]);
    }

    public function actionDelivered($id){
        $have_access = Users::checkPremission(75);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        date_default_timezone_set('Asia/Yerevan');
        $session = Yii::$app->session;
//        echo "<pre>";
        $document = Documents::findOne($id);
        $new_document = new Documents();
        if ($session['role_id'] == 4){
            $new_document->user_id = $session['user_id'];
        }
        $new_document->warehouse_id = $document->warehouse_id;
        $new_document->rate_id = 1;
        $new_document->rate_value = 1;
        $new_document->document_type = 8;
        $new_document->comment = 'Վերադարձի մուտքագրված փաստաթուղթ';
        $new_document->status = '1';
        $new_document->date = date('Y-m-d H:i:s');;
        $new_document->created_at = date('Y-m-d H:i:s');
        $new_document->updated_at = date('Y-m-d H:i:s');
        $new_document->save(false);
        $document_items = DocumentItems::find()->where(['document_id' => $id])->asArray()->all();
        if (!empty($document_items)){
            for ($k = 0;$k < count($document_items); $k++){
                $new_document_items = new DocumentItems();
                $new_document_items->document_id = $new_document->id;
                $new_document_items->nomenclature_id = $document_items[$k]['nomenclature_id'];
                $new_document_items->count = $document_items[$k]['count'];
                $new_document_items->price = $document_items[$k]['price'];
                $new_document_items->price_with_aah = $document_items[$k]['price_with_aah'];
                $new_document_items->AAH =  $document_items[$k]['AAH'];
                $new_document_items->status = '1';
                $new_document_items->created_at = date('Y-m-d H:i:s');
                $new_document_items->updated_at = date('Y-m-d H:i:s');
                $new_document_items->save(false);

                $new_product = new Products();
                $new_product->warehouse_id = $document->warehouse_id;
                $new_product->nomenclature_id = $document_items[$k]['nomenclature_id'];
                $new_product->document_id = $new_document->id;
                $new_product->type = 8;
                $new_product->count = $document_items[$k]['count'];
                $new_product->count_balance = $document_items[$k]['count'];
                if ($document_items[$k]['AAH'] == 'true'){
                    $new_product->AAH = 1;
                    $new_product->price = $document_items[$k]['price_with_aah'];
                }else{
                    $new_product->price = $document_items[$k]['price'];
                    $new_product->AAH = 0;
                }
                $new_product->status = '1';
                $new_product->created_at = date('Y-m-d H:i:s');
                $new_product->updated_at = date('Y-m-d H:i:s');
                $new_product->save(false);
            }
        }

        $document_status = Documents::findOne($id);
        $document_status->status = '0';
        $document_status->save(false);
        $document_items_status = DocumentItems::find()->where(['document_id' => $id])->all();
        if (!empty($document_items_status)){
            foreach ($document_items_status as $value){
                $value->status = '0';
                $value->save(false);
            }
        }
        return $this->redirect(['index']);
    }

    public function actionRefuseModal(){
        if($this->request->isGet){
            $get = $this->request->get('documentId');
        }
        return $this->renderAjax('refuse',[
            'id' => $get,
        ]);
    }
    public function actionMessage(){
        $sub_page = [
            ['name' => 'Պահեստ','address' => '/warehouse'],
            ['name' => 'Անվանակարգ','address' => '/nomenclature'],
            ['name' => 'Ապրանք','address' => '/products'],
            ['name' => 'Տեղեկամատյան','address' => '/log'],
        ];
        $date_tab = [];
        if ($this->request->isPost){
            $post = $this->request->post();
            $document = Documents::findOne($post['document_id']);
            $document->document_type = 7;
            $document->comment = 'Մերժման պատճառն է՝ «' . $post['message'] . '»';
            $document->save(false);
            $session = Yii::$app->session;
            $mes = $post['message'];
            $message_length = strlen($mes);
            $ch = '';
            for ($i = 0; $i < $message_length; $i++) {
                $ch .= $mes[$i];
                if (($i + 1) % 20 == 0 && $i != $message_length - 1) {
                    $ch .= "\n";
                }
            }
            if($session['role_id'] == 4){
                $user_name = Users::find()->select('*')->where(['id' => $session['user_id']])->asArray()->one();
                $text = $user_name['name'] . '(ն/ը) ' . 'մերժել է ապրանքի հետ վերդարձը, նշելով մերժման պատճառն ՝ «'
                    . $ch . '»: '
                    . "\n" .
                    '<a href="http://champagne/documents/update?id=' . $post['document_id'] . '">
                    <img width="15" height="15" src="/upload/view.png" alt="view"></a>';
                Notifications::createNotifications('Մերժել վերադարձը', $text,'refusalreturn');
            }
            return $this->redirect('message');
        }

        $searchModel = new DocumentsSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,

        ]);
    }
    /**
     * Deletes an existing Documents model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $have_access = Users::checkPremission(39);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $premission = Premissions::find()
            ->select('name')
            ->where(['id' => 39])
            ->asArray()
            ->one();
        $oldattributes = Documents::find()
            ->select(['users.id', 'users.name', 'users.username'])
            ->leftJoin('users', 'users.id = documents.user_id')
            ->where(['documents.id' => $id])
            ->asArray()
            ->one();
        $documents = Documents::findOne($id);
        $documents->status = '0';
        $documents->save();
        Log::afterSaves('Delete', '', $oldattributes['name'] . ' ' . $oldattributes['username'], '#', $premission);
        return $this->redirect(['index']);
    }

    public function actionDeleteDocumentItems(){
        if ($this->request->isPost){
            $items_id = $this->request->post('docItemsId');
//            $nom_id = $this->request->post('nomId');
//            $document_id = $this->request->post('urlId');
            $doc_type = $this->request->post('docType');
//            var_dump($doc_type);
//            die;
            if ($doc_type == 'Մուտք'){
            $delete_items = DocumentItems::findOne($items_id);
            $delete_items->status = '0';
            $delete_items->save(false);
            $delete_product = Products::findOne($delete_items->refuse_product_id);
            $delete_product->status = '0';
            $delete_product->save(false);
                return json_encode(true);
            }elseif ($doc_type == 'Ելք'){
                $delete_items = DocumentItems::findOne($items_id);
                $delete_items->status = '0';
                $delete_items->save(false);
                $delete_product = Products::findOne($delete_items->refuse_product_id);
                $delete_product->status = '0';
                $delete_product->save(false);
                $refuse_product = Products::findOne($delete_product->parent_id);
                $refuse_product->count_balance += $delete_product->count;
                $refuse_product->save(false);
                return json_encode(true);
            }elseif ($doc_type == 'Տեղափոխություն'){
                $delete_items = DocumentItems::findOne($items_id);
                $delete_items->status = '0';
                $delete_items->save(false);
                $delete_product = Products::findOne($delete_items->refuse_product_id);
                $delete_product->status = '0';
                $delete_product->save(false);
                $refuse_product = Products::findOne($delete_product->parent_id);
                $refuse_product->count_balance += $delete_product->count;
                $refuse_product->save(false);
                $exit_product = Products::findOne(['parent_id' => $delete_product->id,'document_id' => $delete_product->document_id, 'type' => 3]);
                $exit_product->status = '0';
                $exit_product->save(false);
                return json_encode(true);
//                var_dump($exit_product);
//                exit();
            }elseif($doc_type == 'Խոտան'){
                $delete_items = DocumentItems::findOne($items_id);
                $delete_items->status = '0';
                $delete_items->save(false);
                $delete_product = Products::findOne($delete_items->refuse_product_id);
                $delete_product->status = '0';
                $delete_product->save(false);
                $refuse_product = Products::findOne($delete_product->parent_id);
                $refuse_product->count_balance += $delete_product->count;
                $refuse_product->save(false);
                return json_encode(true);
            }
//            exit();

//            $documents = Documents::findOne($document_id);
//            $delete_product_items = Products::find()->where(['nomenclature_id' => $nom_id,'document_id' => $document_id,'type' => $documents->document_type])->all();
//            foreach ($delete_product_items as $delete_product_item){
//                $delete_product_item->delete();
//            }

//            if (isset($delete_items) && isset($delete_product_items)){
//            }
        }
    }

    /**
     * Finds the Documents model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Documents the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Documents::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionChangeRates(){
        if ($this->request->isPost){
            $id = $this->request->post('id');
            if ($id != 1){
                return json_encode('others');
            }else{
                return json_encode('amd');
            }
        }
    }
    public  function actionDocumentFilterStatus(){
        if ($_GET){
            $searchModel = new DocumentsSearch();
            $dataProvider = $searchModel->search($this->request->queryParams);
            $sub_page = [];
            $date_tab = [];
            $page_value = null;
            if(isset($_GET["dp-1-page"]))
                $page_value = intval($_GET["dp-1-page"]);
            $render_array = [
                'sub_page' => $sub_page,
                'date_tab' => $date_tab,
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'page_value' => $page_value,

            ];

            if(Yii::$app->request->isAjax){
                return $this->renderAjax('widget', $render_array);
            }else{
                return $this->render('widget', $render_array);
            }
        }
    }

}
