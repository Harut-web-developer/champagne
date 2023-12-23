<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "products".
 *
 * @property int $id
 * @property int $warehouse_id
 * @property int $nomenclature_id
 * @property int $count
 * @property float $price
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 */
class Products extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'products';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['warehouse_id', 'nomenclature_id', 'count', 'price'], 'required'],
            [['warehouse_id', 'nomenclature_id','document_id'], 'integer'],
            [['price', 'count'], 'number'],
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
            'warehouse_id' => 'Պահեստ',
            'nomenclature_id' => 'Անվանակարգ',
            'count' => 'Քանակ',
            'price' => 'Գին',
            'status' => 'Status',
            'created_at' => 'Ստեղծվել է',
            'updated_at' => 'Թարմացվել է',
        ];
    }
    public function getWarehouseName(){
        return $this->hasOne(Warehouse::className(), ['id'=>'warehouse_id']);
    }
    public function getNomenclatureName(){
        return $this->hasOne(Nomenclature::className(), ['id'=>'nomenclature_id']);
    }
    public function getDefaultTitle(){
        return CustomfieldsBlocksTitle::findOne(['id'=>10]);
    }
}
