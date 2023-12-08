<?php

namespace app\controllers;

use app\models\DocumentItems;
use app\models\Documents;
use app\models\DocumentsSearch;
use app\models\Nomenclature;
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
        $sub_page = [];
        $searchModel = new DocumentsSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'sub_page' => $sub_page
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
        return $this->render('view', [
            'model' => $this->findModel($id),
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
        $model = new Documents();
        if ($this->request->isPost) {
            $post = $this->request->post();
            date_default_timezone_set('Asia/Yerevan');
            $model->user_id = $post['Documents']['user_id'];
            $model->warehouse_id = $post['Documents']['warehouse_id'];
            $model->rate_id = $post['Documents']['rate_id'];
            $model->rate_value = $post['Documents']['rate_value'];
            $model->document_type = $post['Documents']['document_type'];
            $model->comment = $post['Documents']['comment'];
            $model->date = $post['Documents']['date'];
            $model->created_at = date('Y-m-d H:i:s');
            $model->updated_at = date('Y-m-d H:i:s');
            $model->save(false);
                for ($i = 0; $i < count($post['document_items']); $i++){
                    $document_items = new DocumentItems();
                    $document_items->document_id = $model->id;
                    $document_items->nomenclature_id = $post['document_items'][$i];
                    $document_items->count = $post['count_'][$i];
                    $document_items->price = $post['price'][$i];
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
                        $products->count = $post['count_'][$i];
                        $products->price = $post['price'][$i];
                        $products->created_at = date('Y-m-d H:i:s');
                        $products->updated_at = date('Y-m-d H:i:s');
                        $products->save(false);
                    }
                }

                    return $this->redirect(['index', 'id' => $model->id]);

        } else {
            $model->loadDefaultValues();
        }

        $users = Users::find()->select('id,name')->asArray()->all();
        $users = ArrayHelper::map($users,'id','name');
        $warehouse = Warehouse::find()->select('id,name')->asArray()->all();
        $warehouse =  ArrayHelper::map($warehouse,'id','name');
        $rates = Rates::find()->select('id,name')->asArray()->all();
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
            ->asArray()->all();
        return $this->render('create', [
            'model' => $model,
            'users' => $users,
            'warehouse' => $warehouse,
            'rates' => $rates,
            'nomenclatures' => $nomenclatures,
            'total' => $total,
        ]);
    }

    public function actionCreateFields()
    {
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
        $query = Nomenclature::find();
        $countQuery = clone $query;
        $nomenclatures = $query->select('nomenclature.id,nomenclature.image,nomenclature.name,nomenclature.price,
        nomenclature.cost,products.id as products_id,products.count')
            ->leftJoin('products','nomenclature.id = products.nomenclature_id')
            ->groupBy('nomenclature.id');
                if ($search_name){
                    $nomenclatures->andWhere(['like', 'nomenclature.name', $search_name])
                        ->offset(0)
                        ->limit(10);
                    $total = $nomenclatures->count();
                }else{
                    $total = $countQuery->count();
                    $nomenclatures->offset($offset)
                        ->limit($pageSize);
                }
        $nomenclatures = $nomenclatures
            ->asArray()
            ->all();
        return $this->renderAjax('get-nom', [
            'nomenclatures' => $nomenclatures,
            'total' => $total,
            'search_name' => $search_name
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

        if ($this->request->isPost) {
            $post = $this->request->post();
            date_default_timezone_set('Asia/Yerevan');
            $model->user_id = $post['Documents']['user_id'];
            $model->warehouse_id = $post['Documents']['warehouse_id'];
            $model->rate_id = $post['Documents']['rate_id'];
            $model->rate_value = $post['Documents']['rate_value'];
            $model->comment = $post['Documents']['comment'];
            $model->date = $post['Documents']['date'];
            $model->updated_at = date('Y-m-d H:i:s');
            $model->save();
            $items = $post['document_items'];
            foreach ($items as $j => $item){
                if ($item != 'null'){
                    $document_items_update = DocumentItems::findOne(intval($item));
                    $document_items_update->document_id = $id;
                    $document_items_update->nomenclature_id = $post['items'][$j];
                    $document_items_update->count = $post['count_'][$j];
                    $document_items_update->price = $post['price'][$j];
                    $document_items_update->AAH = $post['aah'];
                    $document_items_update->updated_at = date('Y-m-d H:i:s');
                    $document_items_update->save();
                }else{
                    $document_items_update = new DocumentItems();
                    $document_items_update->document_id = $id;
                    $document_items_update->nomenclature_id = $post['items'][$j];
                    $document_items_update->count = $post['count_'][$j];
                    $document_items_update->price = $post['price'][$j];
                    $document_items_update->AAH = $post['aah'];
                    $document_items_update->created_at = date('Y-m-d H:i:s');
                    $document_items_update->updated_at = date('Y-m-d H:i:s');
                    $document_items_update->save();
                }
            }
//            $_POST['item_id'] = $model->id;
//            if($post['newblocks'] || $post['new_fild_name']){
//                Yii::$app->runAction('custom-fields/create-title',$post);
//            }
            return $this->redirect(['index', 'id' => $model->id]);
//            return $this->redirect(['index', 'id' => $model->id]);
        }
        $users = Users::find()->select('id,name')->asArray()->all();
        $users = ArrayHelper::map($users,'id','name');
        $warehouse = Warehouse::find()->select('id,name')->asArray()->all();
        $warehouse =  ArrayHelper::map($warehouse,'id','name');
        $rates = Rates::find()->select('id,name')->asArray()->all();
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
            ->asArray()->all();
        $document_items = DocumentItems::find()->select('document_items.*,nomenclature.name, nomenclature.id as nom_id')
            ->leftJoin('nomenclature','document_items.nomenclature_id = nomenclature.id')
            ->where(['document_items.document_id' => $id])
            ->asArray()->all();
        $aah = DocumentItems::find()->select('AAH')->where(['document_id' => $id])->asArray()->one();
        return $this->render('update', [
            'model' => $model,
            'users' => $users,
            'warehouse' => $warehouse,
            'rates' => $rates,
            'nomenclatures' => $nomenclatures,
            'document_items' => $document_items,
            'total' => $total,
            'aah' => $aah
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
        $documents = Documents::findOne($id);
        $documents->status = '0';
        $documents->save();
        return $this->redirect(['index']);
    }

    public function actionDeleteDocumentItems(){
        if ($this->request->isPost){
            $post_id = intval($this->request->post('id'));
            $delete_items = DocumentItems::findOne($post_id)->delete();
            if (isset($delete_items)){
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
}
