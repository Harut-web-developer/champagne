<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "document_items".
 *
 * @property int $id
 * @property int $document_id
 * @property int $nomenclature_id
 * @property int $count
 * @property float $price
 * @property string $AAH
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 */
class DocumentItems extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'document_items';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['document_id', 'nomenclature_id', 'count', 'price', 'AAH', 'created_at', 'updated_at'], 'required'],
            [['document_id', 'nomenclature_id', 'count'], 'integer'],
            [['price'], 'number'],
            [['AAH', 'status'], 'string'],
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
            'document_id' => 'Document ID',
            'nomenclature_id' => 'Nomenclature ID',
            'count' => 'Count',
            'price' => 'Price',
            'AAH' => 'Aah',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
