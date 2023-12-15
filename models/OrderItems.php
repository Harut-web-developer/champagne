<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "order_items".
 *
 * @property int $id
 * @property int $order_id
 * @property int $product_id
 * @property float $price
 * @property int $count
 * @property float $cost
 * @property int $discount
 * @property float $price_before_discount
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 */
class OrderItems extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order_items';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'product_id', 'price', 'count', 'cost', 'discount', 'price_before_discount', 'created_at', 'updated_at'], 'required'],
            [['order_id', 'product_id', 'count', 'discount'], 'integer'],
            [['price', 'cost', 'price_before_discount'], 'number'],
            [['status','count_discount_id'], 'string'],
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
            'order_id' => 'Order ID',
            'product_id' => 'Product ID',
            'price' => 'Price',
            'count' => 'Count',
            'cost' => 'Cost',
            'discount' => 'Discount',
            'price_before_discount' => 'Price Before Discount',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
