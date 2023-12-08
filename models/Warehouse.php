<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "warehouse".
 *
 * @property int $id
 * @property string $name
 * @property string $location
 * @property string $type
 * @property string $created_at
 * @property string $updated_at
 */
class Warehouse extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'warehouse';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'location', 'type'], 'required'],
            [['type'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'location'], 'string', 'max' => 255],
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
            'location' => 'Տեղադիրք',
            'type' => 'Տեսակ',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
    public function getDefaultTitle(){
        return CustomfieldsBlocksTitle::findOne(['id'=>1]);
    }
}
