<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "branch_groups".
 *
 * @property int $id
 * @property string $name
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 */
class BranchGroups extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'branch_groups';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
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
            'name' => 'Անուն',
            'status' => 'Status',
            'created_at' => 'Ստեղծվել է',
            'updated_at' => 'Թարմացվել է',
        ];
    }
}
