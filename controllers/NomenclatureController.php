<?php

namespace app\controllers;

use app\models\Clients;
use app\models\Discount;
use app\models\Log;
use app\models\Notifications;
use app\models\Premissions;
use yii\helpers\Url;
use Yii;
use app\models\Nomenclature;
use app\models\NomenclatureSearch;
use app\models\Users;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * NomenclatureController implements the CRUD actions for Nomenclature model.
 * @method getAction()
 */
class NomenclatureController extends Controller
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
     * Lists all Nomenclature models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $have_access = Users::checkPremission(12);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $res = Yii::$app->runAction('custom-fields/get-table-data',['page'=>'nomenclature']);
        $sub_page = [
            ['name' => 'Պահեստ','address' => '/warehouse'],
            ['name' => 'Փաստաթղթեր','address' => '/documents'],
            ['name' => 'Ապրանք','address' => '/products'],
            ['name' => 'Տեղեկամատյան','address' => '/log'],
        ];
        $date_tab = [];

        $searchModel = new NomenclatureSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'new_fields'=>$res,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,

        ]);
    }

    /**
     * Displays a single Nomenclature model.
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
     * Creates a new Nomenclature model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $have_access = Users::checkPremission(9);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $model = new Nomenclature();
        $url = Url::to('', 'http');
        $url = str_replace('create', 'view', $url);
        $premission = Premissions::find()
            ->select('name')
            ->where(['id' => 9])
            ->asArray()
            ->one();
        $sub_page = [];
        $date_tab = [];

        if ($this->request->isPost) {
            date_default_timezone_set('Asia/Yerevan');
            $post = $this->request->post();
            $imageName = $_FILES['Nomenclature']['name']['image'];
            $model->image = $imageName;
            $model->image = UploadedFile::getInstance($model, 'image');
            $model->image->saveAs('upload/'.$imageName );
            $model->name = $post['Nomenclature']['name'];
            $model->cost = floatval($post['Nomenclature']['cost']);
            $model->price = floatval($post['Nomenclature']['price']);
            $model->created_at = date('Y-m-d H:i:s');
            $model->updated_at = date('Y-m-d H:i:s');
            $model = Nomenclature::getDefVals($model);
            $model->save(false);
            Log::afterSaves('Create', $model, '', $url.'?'.'id'.'='.$model->id, $premission);
            $session = Yii::$app->session;
            if ($session->has('role_id') && $session['role_id'] == 4) {
                $user_name = Users::find()->select('name')->where(['id' => $session['user_id']])->asArray()->one();
                $photoUrl = Yii::$app->urlManager->createAbsoluteUrl(['/upload/' . $model->image]);
                $text = 'Պահեստապետ՝ ' . $user_name['name'] . '(ն/ը) ' . 'ստեղծել է անվանակարգ։ Անվանակարգը ստեղծվել է՝
            «' . $model->name . '» անունով։ <img style="width:50px" src="' . $photoUrl . '" alt="photo">';
                Notifications::createNotifications('Ստեղծել անվանակարգ', $text, 'createNomenclature');
            }

            $_POST['item_id'] = $model->id;
            if($post['newblocks'] || $post['new_fild_name']){
                Yii::$app->runAction('custom-fields/create-title',$post);
            }
            return $this->redirect(['index', 'id' => $model->id]);
        } else {
            $model->loadDefaultValues();
        }
        $discounts = Discount::find()->select('id,discount')->asArray()->all();
        return $this->render('create', [
            'model' => $model,
            'discounts' => $discounts,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,

        ]);
    }

    public function actionCreateFields()
    {
        $have_access = Users::checkPremission(72);
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

        $model = new Nomenclature();
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
     * Updates an existing Nomenclature model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $have_access = Users::checkPremission(10);

        if(!$have_access){
            $this->redirect('/site/403');
        }
        if ($this->findModel($id)->status == 0) {
            return $this->redirect(Yii::$app->request->referrer);
        }
        $model = $this->findModel($id);
        $url = Url::to('', 'http');
        $oldattributes = Nomenclature::find()
            ->select('*')
            ->where(['id' => $id])
            ->asArray()
            ->one();
        $premission = Premissions::find()
            ->select('name')
            ->where(['id' => 10])
            ->asArray()
            ->one();
        $sub_page = [];
        $date_tab = [];

        if ($this->request->isPost) {
            date_default_timezone_set('Asia/Yerevan');
            $post = $this->request->post();
            if(isset($_FILES['Nomenclature']['name']['image']) && !!$_FILES['Nomenclature']['name']['image']){
                $imageName = $_FILES['Nomenclature']['name']['image'];
                $model->image = $imageName;
                $model->image = UploadedFile::getInstance($model, 'image');
                $model->image->saveAs('upload/'.$imageName );
            }
            $model->name = $post['Nomenclature']['name'];
            $model->cost = floatval($post['Nomenclature']['cost']);
            $model->price = floatval($post['Nomenclature']['price']);
            $model->updated_at = date('Y-m-d H:i:s');
            $model->save(false);
            Log::afterSaves('Update', $model, $oldattributes, $url, $premission);
            $session = Yii::$app->session;
            if ($session->has('role_id') && $session['role_id'] == 4) {
                $user_name = Users::find()->select('name')->where(['id' => $session['user_id']])->asArray()->one();
                $photoUrl = Yii::$app->urlManager->createAbsoluteUrl(['/upload/' . $model->image]);
                $text = 'Պահեստապետ՝ ' . $user_name['name'] . '(ն/ը) ' . 'փոփոխել է անվանակարգը։ Փոփոխված անվանակարգն է՝
            «' . $model->name . '»։ <img style="width:50px" src="' . $photoUrl . '" alt="photo">';
                Notifications::createNotifications('Փոփոխել անվանակարգ', $text, 'updateNomenclature');
            }
            $_POST['item_id'] = $model->id;
            if($post['newblocks'] || $post['new_fild_name']){
                Yii::$app->runAction('custom-fields/create-title',$post);
            }
            return $this->redirect(['index', 'id' => $model->id]);
        }
        return $this->render('update', [
            'model' => $model,
            'sub_page' => $sub_page,
            'date_tab' => $date_tab,

        ]);
    }

    /**
     * Deletes an existing Nomenclature model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $have_access = Users::checkPremission(11);
        if(!$have_access){
            $this->redirect('/site/403');
        }
        $oldattributes = Nomenclature::find()
            ->select('name')
            ->where(['id' => $id])
            ->asArray()
            ->one();

        $premission = Premissions::find()
            ->select('name')
            ->where(['id' => 11])
            ->asArray()
            ->one();
        $nomenclature = Nomenclature::findOne($id);
        $nomenclature->status = '0';
        $nomenclature->save();
        Log::afterSaves('Delete', '', $oldattributes['name'], '#', $premission);
        return $this->redirect(['index']);
    }

    /**
     * Finds the Nomenclature model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Nomenclature the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Nomenclature::findOne(['id' => $id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
