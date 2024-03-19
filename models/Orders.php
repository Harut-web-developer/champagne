<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "orders".
 *
 * @property int $id
 * @property int $user_id
 * @property int $clients_id
 * @property string $status
 * @property float $total_price
 * @property float $total_price_before_discount
 * @property float $total_discount
 * @property int $total_count
 * @property string $created_at
 * @property string $updated_at
 */
class Orders extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'orders';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id','clients_id', 'total_price', 'total_count','orders_date'], 'required'],
            [['user_id','clients_id', 'total_count'], 'integer'],
            [['status','comment'], 'string'],
            [['total_price'], 'number'],
            [['created_at', 'updated_at','orders_date'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Պատվերի համար',
            'user_id' => 'Մենեջեր',
            'clients_id' => 'Հաճախորդ',
            'status' => 'Կարգավիճակ',
            'is_exist_company' => 'Կարգավիճակ',
            'company_id' => 'Ընկերություն',
            'comment' => 'Մեկնաբանություն',
            'total_price_before_discount' => 'Ընդհանուր գումար',
            'total_price' => 'Ընդհանուր զեղչված գումար',
            'total_count' => 'Ընդհանուր քանակ',
            'total_discount' => 'Ընդհանուր զեղչի չափ',
            'orders_date' => 'Պատվերի ամսաթիվ',
            'created_at' => 'Ստեղծվել է',
            'updated_at' => 'Թարմացվել է',
        ];
    }
     public function getUsersName(){
        return $this->hasOne(Users::className(),['id' => 'user_id']);
     }
    public function getClientsName(){
        return $this->hasOne(Clients::className(),['id' => 'clients_id']);
    }
    public static function getDefVals($model){
        if(is_null($model->status)){
            $model->status = '1';
        }
        return $model;
    }
//    public static  function getMeneger(x)
}
