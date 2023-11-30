<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "orders".
 *
 * @property int $id
 * @property int $user_id
 * @property int $clients_id
 * @property int $order_number
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
            [['user_id','clients_id', 'total_price', 'total_count'], 'required'],
            [['user_id','clients_id', 'total_count', 'order_number'], 'integer'],
            [['status'], 'string'],
            [['total_price'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Օգտագործող',
            'clients_id' => 'Հաճախորդ',
            'order_number' => 'order',
            'status' => 'Status',
            'total_price' => 'Ընդհանուր գումար',
            'total_count' => 'Ընդհանուր քանակ',
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
