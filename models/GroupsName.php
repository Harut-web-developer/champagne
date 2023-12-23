<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "groups_name".
 *
 * @property int $id
 * @property string $groups_name
 * @property string|null $status
 * @property string $created_at
 * @property string $updated_at
 */
class GroupsName extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'groups_name';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['groups_name','created_at', 'updated_at'], 'required'],
            [['groups_name'], 'string', 'max' => 255],
//            [['created_at', 'updated_at'], 'required'],
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
            'groups_name' => 'Խմբի անվանում',
        ];
    }
}
