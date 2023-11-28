<?php

namespace app\controllers;

use app\models\DocumentItems;
use app\models\Documents;
use app\models\DocumentsSearch;
use app\models\Nomenclature;
use app\models\Rates;
use app\models\Users;
use app\models\Warehouse;
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
     * Lists all Documents models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new DocumentsSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
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
            $model->save();
                for ($i = 0; $i < count($post['document_items']); $i++){
                    $document_items = new DocumentItems();
                    $document_items->document_id = $model->id;
                    $document_items->nomenclature_id = $post['document_items'][$i];
                    $document_items->count = $post['count_'][$i];
                    $document_items->price = $post['price'][$i];
                    $document_items->AAH = $post['aah'];
                    $document_items->created_at = date('Y-m-d H:i:s');
                    $document_items->updated_at = date('Y-m-d H:i:s');
                    $document_items->save();
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
        $nomenclatures = Nomenclature::find()->select('nomenclature.id,nomenclature.name,nomenclature.price,
        nomenclature.cost,products.id as products_id,products.count,')
            ->leftJoin('products','nomenclature.id = products.nomenclature_id')
            ->asArray()->all();
        return $this->render('create', [
            'model' => $model,
            'users' => $users,
            'warehouse' => $warehouse,
            'rates' => $rates,
            'nomenclatures' => $nomenclatures
        ]);
    }

    /**
     * Updates an existing Documents model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
//        echo  "<pre>";
        $model = $this->findModel($id);

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
            return $this->redirect(['index', 'id' => $model->id]);
        }
        $users = Users::find()->select('id,name')->asArray()->all();
        $users = ArrayHelper::map($users,'id','name');
        $warehouse = Warehouse::find()->select('id,name')->asArray()->all();
        $warehouse =  ArrayHelper::map($warehouse,'id','name');
        $rates = Rates::find()->select('id,name')->asArray()->all();
        $rates = ArrayHelper::map($rates,'id','name');
        $nomenclatures = Nomenclature::find()->select('nomenclature.id,nomenclature.name,nomenclature.price,
        nomenclature.cost,products.count,')
            ->leftJoin('products','nomenclature.id = products.nomenclature_id')
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
