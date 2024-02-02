<?php

namespace app\controllers;

use app\models\DocumentItems;
use app\models\Documents;
use app\models\DocumentsSearch;
use app\models\Nomenclature;
use app\models\Log;
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
            $post = $this->request->post();
//            echo "<pre>";
//            var_dump($post);
//            exit();
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
                for ($i = 0; $i < count($post['document_items']); $i++){
                    $document_items = new DocumentItems();
                    $document_items->document_id = $model->id;
                    $document_items->nomenclature_id = $post['document_items'][$i];
                    $document_items->count = $post['count_'][$i];
                    $document_items->price = floatval($post['price'][$i]);
                    $document_items->price_with_aah = floatval($post['pricewithaah'][$i]);
                    $document_items->AAH = $post['aah'];
                    $document_items->created_at = date('Y-m-d H:i:s');
                    $document_items->updated_at = date('Y-m-d H:i:s');
                    $document_items->save(false);
                }
                if ($post['Documents']['document_type'] === '1'){
                    for ($i = 0; $i < count($post['document_items']); $i++) {
                        $products = new Products();
                        $products->warehouse_id = $post['Documents']['warehouse_id'];
                        $products->nomenclature_id = $post['document_items'][$i];
                        $products->document_id = $model->id;
                        $products->type = 1;
                        $products->count = intval($post['count_'][$i]);
                        if ($post['aah'] == 'true'){
                            $products->price = floatval($post['pricewithaah'][$i]);
                        }else{
                            $products->price = floatval($post['price'][$i]);
                        }

                        $products->created_at = date('Y-m-d H:i:s');
                        $products->updated_at = date('Y-m-d H:i:s');
                        $products->save(false);
                    }
                }
                if ($post['Documents']['document_type'] == '2'){
                    for ($i = 0; $i < count($post['document_items']); $i++) {
                        $products = new Products();
                        $products->warehouse_id = $post['Documents']['warehouse_id'];
                        $products->nomenclature_id = $post['document_items'][$i];
                        $products->document_id = $model->id;
                        $products->type = 5;
                        $products->count = -intval($post['count_'][$i]);
                        if ($post['aah'] == 'true'){
                            $products->price = floatval($post['pricewithaah'][$i]);
                        }else{
                            $products->price = floatval($post['price'][$i]);
                        }
                        $products->created_at = date('Y-m-d H:i:s');
                        $products->updated_at = date('Y-m-d H:i:s');
                        $products->save(false);

                    }
                }
                if ($post['Documents']['document_type'] == '3'){
                    for ($i = 0; $i < count($post['document_items']); $i++) {
                        $products = new Products();
                        $products->warehouse_id = $post['Documents']['warehouse_id'];
                        $products->nomenclature_id = $post['document_items'][$i];
                        $products->document_id = $model->id;
                        $products->type = 3;
                        $products->count = -intval($post['count_'][$i]);
                        if ($post['aah'] == 'true'){
                            $products->price = floatval($post['pricewithaah'][$i]);
                        }else{
                            $products->price = floatval($post['price'][$i]);
                        }
                        $products->created_at = date('Y-m-d H:i:s');
                        $products->updated_at = date('Y-m-d H:i:s');
                        $products->save(false);

                    }
                    for ($j = 0; $j < count($post['document_items']); $j++) {
                        $products = new Products();
                        $products->warehouse_id = $post['Documents']['to_warehouse']; // texapoxvac pahest
                        $products->nomenclature_id = $post['document_items'][$j];
                        $products->document_id = $model->id;
                        $products->type = 3;
                        $products->count = intval($post['count_'][$j]);
                        if ($post['aah'] == 'true'){
                            $products->price = floatval($post['pricewithaah'][$j]);
                        }else{
                            $products->price = floatval($post['price'][$j]);
                        }
                        $products->created_at = date('Y-m-d H:i:s');
                        $products->updated_at = date('Y-m-d H:i:s');
                        $products->save(false);

                    }
                }
                if($post['Documents']['document_type'] === '4'){
                    for ($i = 0; $i < count($post['document_items']); $i++) {
                        $products = new Products();
                        $products->warehouse_id = $post['Documents']['warehouse_id'];
                        $products->nomenclature_id = $post['document_items'][$i];
                        $products->document_id = $model->id;
                        $products->type = 4;
                        $products->count = -intval($post['count_'][$i]);
                        if ($post['aah'] == 'true'){
                            $products->price = floatval($post['pricewithaah'][$i]);
                        }else{
                            $products->price = floatval($post['price'][$i]);
                        }
                        $products->created_at = date('Y-m-d H:i:s');
                        $products->updated_at = date('Y-m-d H:i:s');
                        $products->save(false);
                    }
                }
                $model_new = [];
                foreach ($document_items as $name => $value) {
                    $model_new[$name] = $value;
                }
                foreach ($model as $name => $value) {
                    $model_new[$name] = $value;
                }
                Log::afterSaves('Create', $model_new, '', $url.'?'.'id'.'='.$model->id, $premission);
                return $this->redirect(['index', 'id' => $model->id]);

        } else {
            $model->loadDefaultValues();
        }

        $users = Users::find()->select('id,name')->where(['status' => '1'])->andWhere(['or', ['role_id' => '1'], ['role_id' => '4']])->asArray()->all();
        $users = ArrayHelper::map($users,'id','name');
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
        $urlId = intval($_POST['urlId']);
        $search_name = $_GET['nomenclature'] ?? false;
        $pageSize = 10;
        $offset = ($page-1) * $pageSize;
        $query = Nomenclature::find();
        $countQuery = clone $query;
        $document_items = DocumentItems::find()->select('document_items.*,nomenclature.name, nomenclature.id as nom_id')
            ->leftJoin('nomenclature','document_items.nomenclature_id = nomenclature.id')
            ->where(['document_items.document_id' => $urlId])
            ->asArray()
            ->all();
        $nomenclatures = $query->where(['not in','id' , array_column($document_items,'nomenclature_id')])
            ->groupBy('nomenclature.id');
                if ($search_name){
                    $nomenclatures->andWhere(['like', 'nomenclature.name', $search_name])
                        ->offset(0);
                }else{
                    $nomenclatures->offset($offset)
                        ->limit($pageSize);
                }
        $nomenclatures = $nomenclatures
//            ->orderBy(['nomenclature.id'=> SORT_DESC])
            ->asArray()
            ->all();
        $total = $countQuery->count() - count($document_items);
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
        $have_access = Users::checkPremission(38);
        if(!$have_access){
            $this->redirect('/site/403');
        }
//        echo  "<pre>";
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
            $post = $this->request->post();
//            echo "<pre>";
//            var_dump($post);
//            exit();
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
                if ($item != 'null'){
                    $document_items_update = DocumentItems::findOne(intval($item));
                    $document_items_update->document_id = $id;
                    $document_items_update->nomenclature_id = $post['items'][$j];
                    $document_items_update->count = intval($post['count_'][$j]);
                    $document_items_update->price = floatval($post['price'][$j]);
                    $document_items_update->price_with_aah = floatval($post['pricewithaah'][$j]);
                    $document_items_update->AAH = $post['aah'];
                    $document_items_update->updated_at = date('Y-m-d H:i:s');
                    $document_items_update->save();
                    if ($post['Documents']['document_type'] === 'Մուտք'){
                        $product_write_in = Products::find()->select('products.*')
                            ->where(['and',['document_id' => $model->id,'type' => 1,'nomenclature_id' => $post['items'][$j]]])->one();
                        $product_write_in->warehouse_id = $post['Documents']['warehouse_id'];
                        $product_write_in->nomenclature_id = $post['items'][$j];
                        $product_write_in->document_id = $model->id;
                        $product_write_in->type = 1;
                        $product_write_in->count = intval($post['count_'][$j]);
                        if ($post['aah'] == 'true'){
                            $product_write_in->price = floatval($post['pricewithaah'][$j]);
                        }else{
                            $product_write_in->price = floatval($post['price'][$j]);
                        }
                        $product_write_in->updated_at = date('Y-m-d H:i:s');
                        $product_write_in->save(false);
                    }
                    if ($post['Documents']['document_type'] === 'Ելք'){
                        $product_write_out = Products::find()->select('products.*')
                            ->where(['and',['document_id' => $model->id,'type' => 5,'nomenclature_id' => $post['items'][$j]]])->one();
                        $product_write_out->warehouse_id = $post['Documents']['warehouse_id'];
                        $product_write_out->nomenclature_id = $post['items'][$j];
                        $product_write_out->document_id = $model->id;
                        $product_write_out->type = 5;
                        $product_write_out->count = -intval($post['count_'][$j]);
                        if ($post['aah'] == 'true'){
                            $product_write_out->price = floatval($post['pricewithaah'][$j]);
                        }else{
                            $product_write_out->price = floatval($post['price'][$j]);
                        }
                        $product_write_out->updated_at = date('Y-m-d H:i:s');
                        $product_write_out->save(false);
                    }
                    if ($post['Documents']['document_type'] === 'Տեղափոխություն'){
                        $product_write_out = Products::find()->select('products.*')
                            ->where(['and',['warehouse_id' => $post['Documents']['warehouse_id'], 'document_id' => $model->id,'type' => 3,'nomenclature_id' => $post['items'][$j]]])->one();
                        $product_write_out->warehouse_id = $post['Documents']['warehouse_id'];
                        $product_write_out->nomenclature_id = $post['items'][$j];
                        $product_write_out->document_id = $model->id;
                        $product_write_out->type = 3;
                        $product_write_out->count = -intval($post['count_'][$j]);
                        if ($post['aah'] == 'true'){
                            $product_write_out->price = floatval($post['pricewithaah'][$j]);
                        }else{
                            $product_write_out->price = floatval($post['price'][$j]);
                        }
                        $product_write_out->updated_at = date('Y-m-d H:i:s');
                        $product_write_out->save(false);

                        $product_write_in = Products::find()->select('products.*')
                            ->where(['and',['warehouse_id' =>  $post['Documents']['to_warehouse'], 'document_id' => $model->id,'type' => 3,'nomenclature_id' => $post['items'][$j]]])->one();
                        $product_write_in->warehouse_id = $post['Documents']['to_warehouse'];
                        $product_write_in->nomenclature_id = $post['items'][$j];
                        $product_write_in->document_id = $model->id;
                        $product_write_in->type = 3;
                        $product_write_in->count = intval($post['count_'][$j]);
                        if ($post['aah'] == 'true'){
                            $product_write_in->price = floatval($post['pricewithaah'][$j]);
                        }else{
                            $product_write_in->price = floatval($post['price'][$j]);
                        }
                        $product_write_in->updated_at = date('Y-m-d H:i:s');
                        $product_write_in->save(false);
                    }
                    if ($post['Documents']['document_type'] === 'Խոտան'){
                        $product_write_out = Products::find()->select('products.*')
                            ->where(['and',['document_id' => $model->id,'type' => 4,'nomenclature_id' => $post['items'][$j]]])->one();
                        $product_write_out->warehouse_id = $post['Documents']['warehouse_id'];
                        $product_write_out->nomenclature_id = $post['items'][$j];
                        $product_write_out->document_id = $model->id;
                        $product_write_out->type = 4;
                        $product_write_out->count = -intval($post['count_'][$j]);
                        if ($post['aah'] == 'true'){
                            $product_write_out->price = floatval($post['pricewithaah'][$j]);
                        }else{
                            $product_write_out->price = floatval($post['price'][$j]);
                        }
                        $product_write_out->updated_at = date('Y-m-d H:i:s');
                        $product_write_out->save(false);
                    }

                } else {
                    $document_items_update = new DocumentItems();
                    $document_items_update->document_id = $id;
                    $document_items_update->nomenclature_id = $post['items'][$j];
                    $document_items_update->count = intval($post['count_'][$j]);
                    $document_items_update->price = floatval($post['price'][$j]);
                    $document_items_update->price_with_aah = floatval($post['pricewithaah'][$j]);
                    $document_items_update->AAH = $post['aah'];
                    $document_items_update->created_at = date('Y-m-d H:i:s');
                    $document_items_update->updated_at = date('Y-m-d H:i:s');
                    $document_items_update->save();

                    if ($post['Documents']['document_type'] === 'Մուտք'){
                        $product_write_in = new Products();
                        $product_write_in->warehouse_id = $post['Documents']['warehouse_id'];
                        $product_write_in->nomenclature_id = $post['items'][$j];
                        $product_write_in->document_id = $model->id;
                        $product_write_in->type = 1;
                        $product_write_in->count = intval($post['count_'][$j]);
                        if ($post['aah'] == 'true'){
                            $product_write_in->price = floatval($post['pricewithaah'][$j]);
                        }else{
                            $product_write_in->price = floatval($post['price'][$j]);
                        }
                        $product_write_in->created_at = date('Y-m-d H:i:s');
                        $product_write_in->updated_at = date('Y-m-d H:i:s');
                        $product_write_in->save(false);
                    }
                    if ($post['Documents']['document_type'] === 'Ելք'){
                        $product_write_out = new Products();
                        $product_write_out->warehouse_id = $post['Documents']['warehouse_id'];
                        $product_write_out->nomenclature_id = $post['items'][$j];
                        $product_write_out->document_id = $model->id;
                        $product_write_out->type = 5;
                        $product_write_out->count = -intval($post['count_'][$j]);
                        if ($post['aah'] == 'true'){
                            $product_write_out->price = floatval($post['pricewithaah'][$j]);
                        }else{
                            $product_write_out->price = floatval($post['price'][$j]);
                        }
                        $product_write_out->created_at = date('Y-m-d H:i:s');
                        $product_write_out->updated_at = date('Y-m-d H:i:s');
                        $product_write_out->save(false);
                    }
                    if ($post['Documents']['document_type'] === 'Տեղափոխություն'){
                        $product_write_out = new Products();
                        $product_write_out->warehouse_id = $post['Documents']['warehouse_id'];
                        $product_write_out->nomenclature_id = $post['items'][$j];
                        $product_write_out->document_id = $model->id;
                        $product_write_out->type = 3;
                        $product_write_out->count = -intval($post['count_'][$j]);
                        if ($post['aah'] == 'true'){
                            $product_write_out->price = floatval($post['pricewithaah'][$j]);
                        }else{
                            $product_write_out->price = floatval($post['price'][$j]);
                        }
                        $product_write_out->created_at = date('Y-m-d H:i:s');
                        $product_write_out->updated_at = date('Y-m-d H:i:s');
                        $product_write_out->save(false);

                        $product_write_in = new Products();
                        $product_write_in->warehouse_id = $post['Documents']['to_warehouse'];
                        $product_write_in->nomenclature_id = $post['items'][$j];
                        $product_write_in->document_id = $model->id;
                        $product_write_in->type = 3;
                        $product_write_in->count = intval($post['count_'][$j]);
                        if ($post['aah'] == 'true'){
                            $product_write_in->price = floatval($post['pricewithaah'][$j]);
                        }else{
                            $product_write_in->price = floatval($post['price'][$j]);
                        }
                        $product_write_in->created_at = date('Y-m-d H:i:s');
                        $product_write_in->updated_at = date('Y-m-d H:i:s');
                        $product_write_in->save(false);
                    }
                    if ($post['Documents']['document_type'] === 'Խոտան'){
                        $product_write_out = new Products();
                        $product_write_out->warehouse_id = $post['Documents']['warehouse_id'];
                        $product_write_out->nomenclature_id = $post['items'][$j];
                        $product_write_out->document_id = $model->id;
                        $product_write_out->type = 4;
                        $product_write_out->count = -intval($post['count_'][$j]);
                        if ($post['aah'] == 'true'){
                            $product_write_out->price = floatval($post['pricewithaah'][$j]);
                        }else{
                            $product_write_out->price = floatval($post['price'][$j]);
                        }
                        $product_write_out->created_at = date('Y-m-d H:i:s');
                        $product_write_out->updated_at = date('Y-m-d H:i:s');
                        $product_write_out->save(false);
                    }
//                    $document_items_update = new DocumentItems();
//                    $document_items_update->document_id = $id;
//                    $document_items_update->save();
                }
            }
//            exit();

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
        $users = Users::find()->select('id,name')->andWhere(['or', ['role_id' => '1'], ['role_id' => '4']])->asArray()->all();
        $users = ArrayHelper::map($users,'id','name');
        $warehouse = Warehouse::find()->select('id,name')->where(['id' => $get_documents_id->warehouse_id])->asArray()->all();
        $warehouse =  ArrayHelper::map($warehouse,'id','name');
        $to_warehouse =  Warehouse::find()->select('id,name')->where(['id' => $get_documents_id->to_warehouse])->andWhere(['status' => '1'])->asArray()->all();
        $to_warehouse = ArrayHelper::map($to_warehouse,'id','name');
        $rates = Rates::find()->select('id,name')->asArray()->all();
        $rates = ArrayHelper::map($rates,'id','name');
        $query = Nomenclature::find();
        $countQuery = clone $query;
        $document_items = DocumentItems::find()->select('document_items.*,nomenclature.name, nomenclature.id as nom_id')
            ->leftJoin('nomenclature','document_items.nomenclature_id = nomenclature.id')
            ->where(['document_items.document_id' => $id])
            ->asArray()->all();

        $nomenclatures = $query->where(['not in','id' , array_column($document_items,'nomenclature_id')])
            ->offset(0)
            ->limit(10)
//            ->orderBy(['nomenclature.id'=> SORT_DESC])
            ->asArray()
            ->all();
        $total = $countQuery->count() - count($document_items);
        $aah = DocumentItems::find()->select('AAH')->where(['document_id' => $id])->asArray()->one();
        return $this->render('update', [
            'model' => $model,
            'users' => $users,
            'warehouse' => $warehouse,
            'rates' => $rates,
            'nomenclatures' => $nomenclatures,
            'document_items' => $document_items,
            'total' => $total,
            'aah' => $aah,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,
            'to_warehouse' => $to_warehouse,
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
            $nom_id = $this->request->post('nomId');
            $document_id = $this->request->post('urlId');

            $delete_items = DocumentItems::findOne($items_id)->delete();
            $documents = Documents::findOne($document_id);
            $delete_product_items = Products::find()->where(['nomenclature_id' => $nom_id,'document_id' => $document_id,'type' => $documents->document_type])->all();
            foreach ($delete_product_items as $delete_product_item){
                $delete_product_item->delete();
            }

            if (isset($delete_items) && isset($delete_product_items)){
                return json_encode(true);
            }
        }
    }

//    public function actionSearch(){
//        if ($this->request->isPost){
//            $nom = $this->request->post('nomenclature');
//            $query = Nomenclature::find()
//                ->where(['like', 'name', $nom]);
//
//            $nomenclature = $query
//                ->asArray()
//                ->all();
//            $res = [];
//            $res['nomenclature'] = $nomenclature;
//            return json_encode($res);
//
//        }
//    }
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

            return $this->renderAjax('widget', [
                'sub_page' => $sub_page,
                'date_tab' => $date_tab,
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }
    }

}
