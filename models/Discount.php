<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "discount".
 *
 * @property int $id
 * @property string $type
 * @property int $discount
 * @property string $start_date
 * @property string $end_date
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 */
class Discount extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'discount';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'discount','discount_option'], 'required'],
            [['type', 'status','discount_filter_type'], 'string'],
            [['discount','min','max'], 'integer'],
            [['start_date', 'end_date','discount_option', 'created_at', 'updated_at'], 'safe'],
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
            'type' => 'Տեսակ',
            'discount' => 'Զեղչի չափ',
            'start_date' => 'Զեղչի սկիզբ',
            'end_date' => 'Զեղչի ավարտ',
            'discount_check' => 'Ստուգում',
            'discount_sortable' => 'Զեղչի տեսակավորում',
            'discount_option' => 'Զեղչի ձև',
            'discount_filter_type' => 'Ֆիլտրել',
            'comment' => 'Մեկնաբանություն',
            'min' => 'Նվազագույն',
            'max' => 'Առավելագույն',
            'status' => 'Status',
            'created_at' => 'Ստեղծվել է',
            'updated_at' => 'Թարմացվել է',
        ];
    }
    public static function getDefVals($model){
        if(is_null($model->discount_sortable	)){
            $model->discount_sortable = 0;
        }
        if(is_null($model->status)){
            $model->status = '1';
        }
        return $model;
    }

    public function getClients($client_id = 0){
        $for_client = $this->hasMany(DiscountClients::class, ['discount_id' => 'id'])
            ->andWhere([DiscountClients::tableName().'.status' => 1]);
        if($client_id){
            $for_client->andWhere(['client_id' => $client_id]);
        }
        return $for_client;
    }
    public function getNomenclatures($product_id = 0){
        $for_prod = $this->hasMany(DiscountProducts::class, ['discount_id' => 'id'])
            ->andWhere([DiscountProducts::tableName().'.status' => 1]);
        if($product_id){
            $for_prod->andWhere(['product_id' => $product_id]);
        }
        return $for_prod;
    }
}
