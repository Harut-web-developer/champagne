<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $name
 * @property string $username
 * @property string $password
 * @property string $email
 * @property string $phone
 * @property int $role_id
 * @property string $auth_key
 * @property string $created_at
 * @property string $updated_at
 */
class Users extends ActiveRecord implements IdentityInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'username','role_id'], 'required'],
            [['role_id'], 'string'],
            [['warehouse_id'],'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'username', 'password', 'auth_key'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Անուն',
            'username' => 'Օգտանուն',
            'warehouse_id' => 'Պահեստներ',
            'password' => 'Գաղտնաբառ',
            'role_id' => 'Կարգավիճակ',
            'auth_key' => 'Auth Key',
            'email' => 'Էլ.փոստ',
            'phone' => 'Հեռախոսահամար',
            'status' => 'status',
            'created_at' => 'Ստեղծվել է',
            'updated_at' => 'Թարմացվել է',
        ];
    }
    /**
     * Finds an identity by the given ID.
     *
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface|null the identity object that matches the given ID.
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * Finds an identity by the given token.
     *
     * @param string $token the token to be looked for
     * @return IdentityInterface|null the identity object that matches the given token.
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    /**
     * @return int|string current user ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string|null current user auth key
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @param string $authKey
     * @return bool|null if auth key is valid for current user
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

//    public static function checkUser($id){
//        $user = Users::findOne($id);
//        date_default_timezone_set('Asia/Yerevan');
//        $datetime_1 = $user->updated_at;
//        $datetime_2 = date('Y-m-d H:i:s');
//        $start_datetime = new \DateTime($datetime_1);
//        $diff = $start_datetime->diff(new \DateTime($datetime_2));
//        $total_minutes = $diff->i;
//        if($total_minutes > 1) {
//            return false;
//        } else {
//            return true;
//        }
//    }

    public function getRoleName(){
        return $this->hasOne(Roles::className(), ['id'=>'role_id']);
    }
    public static function Drivers($id){
        return ManagerDeliverCondition::find()->where(['manager_id'=>$id])->all();
    }
    public static function checkPremission($premission){
         $session = Yii::$app->session;
         $userPrem = UserPremissions::findOne(['user_id'=>$session->get('user_id'),'premission_id'=>$premission]);
         if($userPrem){
             return true;
         }
         return  false;
    }
    public function getDefaultTitle(){
        return CustomfieldsBlocksTitle::findOne(['id'=>18]);
    }

}
