<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "orders".
 *
 * @property int $id
 * @property int $user_id
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
            [['user_id', 'total_price', 'total_count'], 'required'],
            [['user_id', 'total_count'], 'integer'],
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
            'user_id' => 'User',
            'status' => 'Status',
            'total_price' => 'Total Price',
            'total_count' => 'Total Count',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
     public function getUsersName(){
        return $this->hasOne(Users::className(),['id' => 'user_id']);
     }
}
