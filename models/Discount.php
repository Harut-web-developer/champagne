<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "discount".
 *
 * @property int $id
 * @property string $type
 * @property int $discount
 * @property string $start_date
 * @property string $end_date
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 */
class Discount extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'discount';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'discount','discount_option'], 'required'],
            [['type', 'status','discount_filter_type'], 'string'],
            [['discount','min','max'], 'integer'],
            [['start_date', 'end_date','discount_option', 'created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Տեսակ',
            'discount' => 'Տոկոս',
            'start_date' => 'Զեղչի սկիզբը',
            'end_date' => 'Զեղչի ավարտ',
            'discount_check' => 'Ստուգում',
            'discount_option' => 'Զեղչի ձև',
            'discount_filter_type' => 'Ֆիլտրել',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
