<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "premissions".
 *
 * @property int $id
 * @property int $role_id
 * @property string $name
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 */
class Premissions extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'premissions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['role_id', 'name', 'created_at', 'updated_at'], 'required'],
            [['role_id'], 'integer'],
            [['status'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'role_id' => 'Դեր',
            'name' => 'Անուն',
            'status' => 'Status',
            'created_at' => 'Ստեղծվել է',
            'updated_at' => 'Թարմացվել է',
        ];
    }
    public function getRoleName(){
        return $this->hasOne(Roles::className(), ['id'=>'role_id']);
    }
    public static function getDefVals($model){
        if(is_null($model->status)){
            $model->status = '1';
        }
        return $model;
    }
}
