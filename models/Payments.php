<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "payments".
 *
 * @property int $id
 * @property int $client_id
 * @property float $payment_sum
 * @property string $pay_date
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 */
class Payments extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payments';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['client_id', 'payment_sum', 'pay_date','rate_id','rate_value'], 'required'],
            [['client_id','payment_sum','rate_id','rate_value'], 'integer'],
            [['pay_date', 'created_at', 'updated_at'], 'safe'],
            [['status','comment'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'client_id' => 'Հաճախորդ',
            'payment_sum' => 'Վճարման գումար',
            'pay_date' => 'Վճարման օր',
            'rate_id' => 'Փոխարժեք',
            'rate_value' => 'Փոխարժեքի չափ',
            'status' => 'Status',
            'created_at' => 'Ստեղծվել է',
            'updated_at' => 'Թարմացվել է',
        ];
    }
    public function getClientName(){
        return $this->hasOne(Clients::className(),['id'=>'client_id']);
    }
    public function getRateName(){
        return $this->hasOne(Rates::className(),['id'=>'rate_id']);
    }
}
