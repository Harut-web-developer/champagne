<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "customfields_blocks_select_options".
 *
 * @property int $id
 * @property int|null $select_id
 * @property string|null $value_
 */
class CustomfieldsBlocksSelectOptions extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'customfields_blocks_select_options';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['select_id'], 'integer'],
            [['value_'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'select_id' => 'Select ID',
            'value_' => 'Value',
        ];
    }
}
