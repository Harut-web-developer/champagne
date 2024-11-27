<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "manager_deliver_condition".
 *
 * @property int $id
 * @property int|null $manager_id
 * @property int $deliver_id
 * @property int $route_id
 * @property string $status
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class ManagerDeliverCondition extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'manager_deliver_condition';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['manager_id', 'deliver_id', 'route_id'], 'integer'],
            [['manager_id', 'deliver_id', 'route_id'], 'required'],
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
            'manager_id' => 'Մենեջեր',
            'route_id' => 'Երթուղի',
            'deliver_id' => 'Առաքիչ',
            'created_at' => 'Ստեղծվել է',
            'updated_at' => 'Թարմացվել է',
        ];
    }
    public function getManagerName(){
        return $this->hasOne(Users::className(), ['id'=>'manager_id']);
    }
    public function getDeliverName(){
        return $this->hasOne(Users::className(), ['id'=>'deliver_id']);
    }
    public function getDeliverName_(){
        $man = ManagerDeliverCondition::find()
            ->select('users.id, users.name')
            ->leftJoin('users', 'users.id = manager_deliver_condition.manager_id')
            ->where(['manager_deliver_condition.id' => $_GET['id']])
            ->andWhere(['manager_deliver_condition.status' => '1'])
            ->andWhere(['users.status' => '1'])
            ->one();
        if ($man != '') {
            $man_del = ManagerDeliverCondition::find()
                ->select('users.id, users.name')
                ->leftJoin('users', 'users.id = manager_deliver_condition.deliver_id')
                ->where(['manager_deliver_condition.manager_id' => $man['id']])
                ->andWhere(['manager_deliver_condition.status' => '1'])
                ->andWhere(['users.status' => '1'])
                ->asArray()
                ->all();
            return $man_del;
        }
    }
}
