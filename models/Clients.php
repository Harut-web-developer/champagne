<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "clients".
 *
 * @property int $id
 * @property int $status
 * @property int $sort_
 * @property string $name
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
            [['name', 'location', 'phone', 'route_id'], 'required'],
            [['route_id'], 'integer'],
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
            'route_id' => 'Երթուղի',
            'sort_' => 'Երթուղու համար',
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
}
