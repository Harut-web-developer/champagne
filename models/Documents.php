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
            'user_id' => 'Օգտագործող',
            'warehouse_id' => 'Պահեստ',
            'rate_id' => 'Փոխարժեք',
            'rate_value' => 'Փոխարժեք',
            'document_type' => 'Փաստաթղթի տեսակը',
            'comment' => 'Մեկնաբանություն',
            'date' => 'Ստեղծման ժամանակը',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
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
