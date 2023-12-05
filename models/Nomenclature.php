<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "nomenclature".
 *
 * @property int $id
 * @property string|null $image
 * @property string $name
 * @property float $price
 * @property string $created_at
 * @property string $updated_at
 */
class Nomenclature extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nomenclature';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name','cost','price' ], 'required'],
            [['price','cost'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['image'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, xlsx'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'image' => 'Նկար',
            'name' => 'Անուն',
            'cost' => 'Ինքնարժեք',
            'price' => 'Գին',
            'created_at' => 'Ստեղծվել է',
            'updated_at' => 'Թարմացվել է',
        ];
    }
    public function getDefaultTitle(){
        return CustomfieldsBlocksTitle::findOne(['id'=>2]);
    }
}
