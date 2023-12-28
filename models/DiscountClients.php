<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "discount_clients".
 *
 * @property int $id
 * @property int $discount_id
 * @property int $client_id
 * @property int $group_id
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 */
class DiscountClients extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'discount_clients';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['discount_id', 'client_id',], 'required'],
            [['discount_id', 'client_id', 'group_id'], 'integer'],
            [['status'], 'string'],
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
            'discount_id' => 'Discount ID',
            'client_id' => 'Client ID',
            'group_id' => 'Group id',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
