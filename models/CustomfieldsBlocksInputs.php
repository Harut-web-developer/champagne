<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "customfields_blocks_inputs".
 *
 * @property int $id
 * @property int|null $iblock_id
 * @property string|null $label
 * @property string|null $type
 * @property string|null $status
 */
class CustomfieldsBlocksInputs extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'customfields_blocks_inputs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['iblock_id'], 'integer'],
            [['label'], 'string', 'max' => 255],
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
            'iblock_id' => 'Iblock ID',
            'label' => 'Label',
            'type' => 'Type',
        ];
    }
    public static function  createElement($element, $item_id, $values = true){
        if(empty($element)){
            return 'Cant create input';
        }
        $input_ = '<div class="new-field" data-field='.$element->id.'>';
        if($values) {
            $value = CustomfieldsBlocksInputValues::find()
                ->where(['input_id' => $element->id])
                ->andWhere(['item_id' => $item_id])
                ->one();
            $type_ = 'name';
        } else {
            $value = false;
            $type_ = 'data-name';
        }
        $field_value = '';
        if($value){
            $field_value = $value->value_;
        }
        $readonly = '';
        $style_display_none = '';
        $disabled = '';
        if (!is_null($item_id)) {
            $readonly = 'readonly';
            $style_display_none = 'style="display:none"';
            $disabled = 'disabled';
        }
        if ($field_value !== '' || $_SERVER['REQUEST_URI'] == '/documents/create-fields'  || $_SERVER['REQUEST_URI'] == '/documents/create'
            || $_SERVER['REQUEST_URI'] == '/warehouse/create-fields'  || $_SERVER['REQUEST_URI'] == '/warehouse/create'
            || $_SERVER['REQUEST_URI'] == '/nomenclature/create-fields'  || $_SERVER['REQUEST_URI'] == '/nomenclature/create'
            || $_SERVER['REQUEST_URI'] == '/users/create-fields'  || $_SERVER['REQUEST_URI'] == '/users/create'
            || $_SERVER['REQUEST_URI'] == '/clients/create-fields'  || $_SERVER['REQUEST_URI'] == '/clients/create'){
            switch ($element->type){
                case 0:
                    $input_ .= '<label>'.$element->label.'</label><input type="number" value="'.$field_value.'" '.$type_.'="CF['.$element->id.']" '. $readonly .'>';
                    break;
                case 1:
                    $input_ .= '<label>'.$element->label.'</label><input type="text" value="'.$field_value.'" '.$type_.'="CF['.$element->id.']" '. $readonly .'>';
                    break;
                case 2:
                    $items = CustomfieldsBlocksSelectOptions::find()->where(['select_id'=>$element->id])->all();
                    $item_options = '';
                    if(!empty($items)){
                        foreach ($items as $item_list => $item_simple){
                            if($field_value != $item_simple->value_) {
                                if (is_null($item_id)){
                                    $item_options .= '<option value="' . $item_simple->value_ . '">' . $item_simple->value_ . '</option>';
                                } else {
                                    $item_options .= '<option value="' . $item_simple->value_ . '"  '. $disabled .'>' . $item_simple->value_ . '</option>';
                                }
                            } else {
                                $item_options .= '<option selected value="' . $item_simple->value_ . '">' . $item_simple->value_ . '</option>';
                            }
                        }
                    }
                    $input_ .= '<label>'.$element->label.'</label><select type="number"  '.$type_.'="CF['.$element->id.']">'.$item_options.'</select>';
                    break;
                case 3:
                    $input_ .= '<label>'.$element->label.'</label><hr><img src="/'.$field_value.'" style="width:100px;"><input type="hidden" '.$type_.'="CF['.$element->id.']"> <input type="file" value="'.$field_value.'" '.$type_.'="CF['.$element->id.']" ' .$style_display_none . '>';
                    break;
                case 4:
                    $input_ .= '<label>'.$element->label.'</label><textarea  '.$type_.'="CF['.$element->id.']" '. $readonly .'>'.$field_value.'</textarea>';
                    break;
                case 5:
                    $input_ .= '<label>'.$element->label.'</label><input type="date" value="'.$field_value.'" '.$type_.'="CF['.$element->id.']" '. $readonly .'>';
                    break;
                case 6:
                    $input_ .= '<label>'.$element->label.'</label><input type="datetime-local" value="'.$field_value.'" '.$type_.'="CF['.$element->id.']" '. $readonly .'>';
                    break;
            }
            if (is_null($item_id)){
                $input_ .= '<span class="remove-field-new" ><i class="bx bx-trash"></i></span>';
            }
            $input_ .= '</div>';
            return $input_;
        }
    }
}
