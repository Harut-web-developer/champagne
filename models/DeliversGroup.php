<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "delivers_group".
 *
 * @property int $id
 * @property int $manager_deliver_condition_id
 * @property int $deliver_id
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 */
class DeliversGroup extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'delivers_group';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['manager_deliver_condition_id', 'deliver_id'], 'required'],
            [['manager_deliver_condition_id', 'deliver_id'], 'integer'],
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
            'manager_deliver_condition_id' => 'Manager ID',
            'deliver_id' => 'Deliver ID',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
