<?php

namespace app\controllers;
use app\models\Users;
use Yii;
use app\models\Nomenclature;
use app\models\Products;
use app\models\ProductsSearch;
use app\models\Warehouse;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ProductsController implements the CRUD actions for Products model.
 */
class ProductsController extends Controller
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
     * Lists all Products models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $have_access = Users::checkPremission(20);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $sub_page = [
            ['name' => 'Պահեստ','address' => '/warehouse'],
            ['name' => 'Փաստաթղթեր','address' => '/documents'],
            ['name' => 'Անվանակարգ','address' => '/nomenclature'],
            ['name' => 'Տեղեկամատյան','address' => '/log'],
        ];
        $date_tab = [];

        $searchModel = new ProductsSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $warehouse = Warehouse::find()
            ->select('id, name')
            ->asArray()
            ->all();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,
            'warehouse' => $warehouse,
        ]);
    }

    /**
     * Displays a single Products model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $sub_page = [];
        $date_tab = [];

        return $this->render('view', [
            'model' => $this->findModel($id),
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,

        ]);
    }

    /**
     * Creates a new Products model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $have_access = Users::checkPremission(17);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $sub_page = [];
        $date_tab = [];

        $model = new Products();
        if ($this->request->isPost) {
            date_default_timezone_set('Asia/Yerevan');
            $post = $this->request->post();
            $model->warehouse_id = $post['Products']['warehouse_id'];
            $model->nomenclature_id = $post['Products']['nomenclature_id'];
            $model->count = $post['Products']['count'];
            $model->price = $post['Products']['price'];
            $model->created_at = date('Y-m-d H:i:s');
            $model->updated_at = date('Y-m-d H:i:s');
            $model->save();
            $_POST['item_id'] = $model->id;
            if($post['newblocks'] || $post['new_fild_name']){
                Yii::$app->runAction('custom-fields/create-title',$post);
            }
                return $this->redirect(['index', 'id' => $model->id]);
        } else {
            $model->loadDefaultValues();
        }
        $warehouse = Warehouse::find()->select('id,name')->asArray()->all();
        $warehouse = ArrayHelper::map($warehouse,'id','name');
        $nomenclature = Nomenclature::find()->select('id,name')->asArray()->all();
        $nomenclature = ArrayHelper::map($nomenclature,'id','name');
        return $this->render('create', [
            'model' => $model,
            'warehouse' => $warehouse,
            'nomenclature' => $nomenclature,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,

        ]);
    }

    public function actionCreateFields()
    {
        $sub_page = [
            ['name' => 'Պահեստ','address' => '/warehouse'],
            ['name' => 'Փաստաթղթեր','address' => '/documents'],
            ['name' => 'Ապրանք','address' => '/products'],
            ['name' => 'Անվանակարգ','address' => '/nomenclature'],
            ['name' => 'Տեղեկամատյան','address' => '/log'],
        ];
        $date_tab = [];

        $model = new Products();
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
     * Updates an existing Products model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $have_access = Users::checkPremission(18);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $model = $this->findModel($id);
        $sub_page = [];
        $date_tab = [];

        if ($this->request->isPost) {
            date_default_timezone_set('Asia/Yerevan');
            $post = $this->request->post();
            $model->warehouse_id = $post['Products']['warehouse_id'];
            $model->nomenclature_id = $post['Products']['nomenclature_id'];
            $model->count = $post['Products']['count'];
            $model->price = $post['Products']['price'];
            $model->updated_at = date('Y-m-d H:i:s');
            $model->save();
            $_POST['item_id'] = $model->id;
            if($post['newblocks'] || $post['new_fild_name']){
                Yii::$app->runAction('custom-fields/create-title',$post);
            }
            return $this->redirect(['index', 'id' => $model->id]);
        }
        $warehouse = Warehouse::find()->select('id,name')->asArray()->all();
        $warehouse = ArrayHelper::map($warehouse,'id','name');
        $nomenclature = Nomenclature::find()->select('id,name')->asArray()->all();
        $nomenclature = ArrayHelper::map($nomenclature,'id','name');
        return $this->render('update', [
            'model' => $model,
            'warehouse' => $warehouse,
            'nomenclature' => $nomenclature,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,

        ]);
    }

    /**
     * Deletes an existing Products model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $have_access = Users::checkPremission(19);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $products = Products::findOne($id);
        $products->status = '0';
        $products->save();
        return $this->redirect(['index']);
    }
    public function actionGetProducts(){
        if ($this->request->isPost){
            $post = $this->request->post();
//            var_dump($post);
            $products_count = Products::find()->select('SUM(count) as count')->where(['nomenclature_id' => intval($post['itemId'])])
                ->andWhere(['warehouse_id' => $post['warehouse_id']])
                ->asArray()->all();
//            var_dump($products_count);
//            exit();
            if ($products_count[0]['count'] === null){
                return json_encode(['count' => 'dontExists']);
            }elseif ($post['count'] > intval($products_count[0]['count'])){
                return json_encode(['count' => 'countMore']);
            }elseif (intval($post['count']) <= 0){
                return json_encode(['count' => 'nullable']);
            }else{
                return json_encode(['count' => 'exists']);
            }
        }
    }
    /**
     * Finds the Products model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Products the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Products::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public  function actionProductsFilterStatus(){
        if ($_GET){
            $searchModel = new ProductsSearch();
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
