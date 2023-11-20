<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "customfields_blocks_input_values".
 *
 * @property int $id
 * @property int|null $input_id
 * @property string|null $value_
 * @property string|null $type
 */
class CustomfieldsBlocksInputValues extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'customfields_blocks_input_values';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['input_id'], 'integer'],
            [['value_'], 'string'],
            [['type'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'input_id' => 'Input ID',
            'value_' => 'Value',
            'type' => 'Type',
        ];
    }
}
