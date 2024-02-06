<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "manager_deliver_condition".
 *
 * @property int $id
 * @property int|null $manager_id
 * @property int|null $deliver_id
 * @property string $status
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class ManagerDeliverCondition extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'manager_deliver_condition';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['manager_id'], 'integer'],
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
            'manager_id' => 'Մենեջեր',
            'deliver_id' => 'Առաքիչներ',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
    public function getManagerName(){
        return $this->hasOne(Users::className(), ['id'=>'manager_id']);
    }
}
