<?php

namespace app\models;
namespace app\models;

use yii\helpers\Html;
//use PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Report\Html;
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
    public static function getValue($item_id, $fild_name)
    {

        $input_id = CustomfieldsBlocksInputs::findOne(['label' => $fild_name]);
        if ($input_id === null) {
            return '';
        }
        $value = CustomfieldsBlocksInputValues::findOne(['input_id' => $input_id->id, 'item_id' => $item_id]);
        if ($value === null) {
            return '';
        }
        if ($input_id->type === '3' && $value->value_ !== 'null') {
            $imageUrl = '/' . $value->value_;
            return Html::img($imageUrl, ['style' => 'width:100px;']);
        }
        if (!empty($value)) {
            return $value->value_;
        }
        return '';
    }


}
