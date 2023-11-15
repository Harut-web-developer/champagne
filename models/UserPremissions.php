<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_premissions".
 *
 * @property int $id
 * @property int $user_id
 * @property int $premission_id
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 */
class UserPremissions extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_premissions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'premission_id', 'created_at', 'updated_at'], 'required'],
            [['id', 'user_id', 'premission_id'], 'integer'],
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
            'user_id' => 'User ID',
            'premission_id' => 'Premission ID',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
