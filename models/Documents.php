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
            [['user_id', 'warehouse_id', 'rate_id', 'comment', 'date', 'created_at', 'updated_at'], 'required'],
            [['user_id', 'warehouse_id', 'rate_id'], 'integer'],
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
            'user_id' => 'User ID',
            'warehouse_id' => 'Warehouse ID',
            'rate_id' => 'Rate ID',
            'comment' => 'Comment',
            'date' => 'Date',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
