<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "documents".
 *
 * @property int $id
 * @property int $user_id
 * @property int $warehouse_id
 * @property int $rate_id
 * @property int $rate_value
 * @property int $document_type
 * @property string $comment
 * @property string $date
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 */
class Documents extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'documents';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'warehouse_id', 'rate_id', 'rate_value', 'document_type', 'comment', 'date'], 'required'],
            [['user_id', 'warehouse_id', 'rate_id', 'rate_value'], 'integer'],
            [['date', 'created_at', 'updated_at'], 'safe'],
            [['status'], 'string'],
            [['comment'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Օգտատեր',
            'warehouse_id' => 'Պահեստ',
            'rate_id' => 'Փոխարժեք',
            'rate_value' => 'Տոկոսադրույք',
            'document_type' => 'Փաստաթղթի տեսակ',
            'comment' => 'Մեկնաբանություն',
            'date' => 'Ստեղծման ժամանակ',
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
    public function getDefaultTitle(){
        return CustomfieldsBlocksTitle::findOne(['id'=>41]);
    }
     public function getUsersName(){
        return $this->hasOne(Users::className(), ['id'=>'user_id']);
     }
    public function getWarehouseName(){
        return $this->hasOne(Warehouse::className(), ['id'=>'warehouse_id']);
    }
    public function getRateName(){
        return $this->hasOne(Rates::className(), ['id'=>'rate_id']);
    }
}
