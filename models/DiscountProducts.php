<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "discount_products".
 *
 * @property int $id
 * @property int $discount_id
 * @property int $product_id
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 */
class DiscountProducts extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'discount_products';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['discount_id', 'product_id', 'created_at', 'updated_at'], 'required'],
            [['discount_id', 'product_id'], 'integer'],
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
            'product_id' => 'Product ID',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
