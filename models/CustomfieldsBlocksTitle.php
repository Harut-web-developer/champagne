<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "customfields_blocks_title".
 *
 * @property int $id
 * @property string $title
 * @property string $page
 * @property int|null $block_type
 * @property int|null $order_number
 */
class CustomfieldsBlocksTitle extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'customfields_blocks_title';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'page'], 'required'],
            [['block_type', 'order_number'], 'integer'],
            [['title', 'page'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'page' => 'Page',
            'block_type' => 'Block Type',
            'order_number' => 'Order Number',
        ];
    }

}
