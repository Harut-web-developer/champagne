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
            [['user_id','clients_id', 'total_price', 'total_count','comment','orders_date'], 'required'],
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
            'id' => 'ID',
            'user_id' => 'Օգտատեր',
            'clients_id' => 'Հաճախորդ',
            'status' => 'Status',
            'comment' => 'Մեկնաբանություն',
            'total_price' => 'Ընդհանուր գումար',
            'total_count' => 'Ընդհանուր քանակ',
            'orders_date' => 'Պատվերի ամսաթիվ',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
     public function getUsersName(){
        return $this->hasOne(Users::className(),['id' => 'user_id']);
     }
    public function getClientsName(){
        return $this->hasOne(Clients::className(),['id' => 'clients_id']);
    }
}
