<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "clients_groups".
 *
 * @property int $id
 * @property int $groups_id
 * @property string $clients_id
 * @property string|null $status
 * @property string $created_at
 * @property string $updated_at
 */
class ClientsGroups extends \yii\db\ActiveRecord
{
    /**
     * @var mixed|null
     */

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'clients_groups';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['groups_id', 'clients_id', 'created_at', 'updated_at'], 'required'],
            [['status'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['groups_id'], 'integer'],
            [['clients_id'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'groups_id' => 'Խմբի անվանում',
            'clients_id' => 'Հաճախորդներ',
            'status' => 'Status',
            'created_at' => 'Ստեղծվել է',
            'updated_at' => 'Թարմացվել է',
        ];
    }
}
