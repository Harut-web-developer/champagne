<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "rates".
 *
 * @property int $id
 * @property string $name
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 */
class Rates extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rates';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name', 'status'], 'string'],
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
            'name' => 'Անուն',
            'status' => 'Status',
            'created_at' => 'Ստեղծվել է',
            'updated_at' => 'Թարմացվել է',
        ];
    }
    public static function getDefVals($model){
        if(is_null($model->status)){
            $model->status = '1';
        }
        return $model;
    }
}
