<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "clients".
 *
 * @property int $id
 * @property int $status
 * @property int $sort_
 * @property int $debt_limit
 * @property string $name
 * @property string $client_warehouse_id
 * @property string $location
 * @property string $route_id
 * @property string $phone
 * @property string $created_at
 * @property string $updated_at
 */
class Clients extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'clients';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name','location', 'phone', 'debt_limit' ], 'required'],
            [['route_id','debt_limit'], 'integer'],
            [['status', 'phone'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'location'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'branch_groups_id' => 'Հիմնական անուն',
            'route_id' => 'Երթուղի',
            'client_warehouse_id' => 'Պահեստ',
            'sort_' => 'Երթուղու համար',
            'debt_limit' => 'Պարտքի սահմանաչափ',
            'name' => 'Անուն',
            'location' => 'Տեղադիրք',
            'phone' => 'Հեռախոսահամար',
            'status' => 'Status',
            'created_at' => 'Ստեղծվել է',
            'updated_at' => 'Թարմացվել է',
        ];
    }
    public function getDefaultTitle(){
        return CustomfieldsBlocksTitle::findOne(['id'=>17]);
    }
    public static function getDefVals($model){
        if(is_null($model->sort_)){
            $model->sort_ = 0;
        }
        if(is_null($model->status)){
            $model->status = '1';
        }
        return $model;
    }
    public function getRouteName(){
        return $this->hasOne(Route::className(), ['id'=>'route_id']);
    }
    public function getWarehouseName(){
        return $this->hasOne(Warehouse::className(), ['id'=>'client_warehouse_id']);
    }
    public function getOrders()
    {
        return $this->hasMany(Orders::class, ['clients_id' => 'id'])->onCondition(['orders.status' => '2']);
    }
    public function getOrdersSum()
    {
        return $this->hasMany(Orders::class, ['clients_id' => 'id'])->onCondition(['orders.status' => '2'])->orOnCondition(['orders.status' => '4']);
    }
    public function getPayments()
    {
        return $this->hasMany(Payments::class, ['client_id' => 'id'])->onCondition(['payments.status' => '1']);
    }
}
