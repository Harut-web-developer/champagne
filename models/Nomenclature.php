<?php

namespace app\models;

use Yii;
use yii\data\Pagination;

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
    public static function order_search($params){
        $pageSize = 10;
        $query = Nomenclature::find();
        $query->select('nomenclature.id,nomenclature.image,nomenclature.name,nomenclature.price,
                            nomenclature.cost,products.id as products_id,products.count,')
            ->leftJoin('products','nomenclature.id = products.nomenclature_id');
        if(isset($params['paging'])){
            $page = $params['paging'];
        }
        else{
            $page = 1;
        }

        $offset = ($page-1) * $pageSize;
        if($offset){
            $query->offset($offset);
        }
        $query->limit($pageSize);

        $query->asArray();
        $query = $query->all();

        return $query;

    }
    public function getDefaultTitle(){
        return CustomfieldsBlocksTitle::findOne(['id'=>2]);
    }
}
