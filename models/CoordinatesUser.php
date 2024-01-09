<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "coordinates_user".
 *
 * @property int $id
 * @property float|null $latitude
 * @property float|null $longitude
 * @property int|null $user_id
 */
class CoordinatesUser extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'coordinates_user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['latitude', 'longitude'], 'number'],
            [['user_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'user_id' => 'User ID',
        ];
    }
}
