<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "log".
 *
 * @property int $id
 * @property int $user_id
 * @property string $action
 * @property string $create_date
 */
class Log extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'action', 'create_date'], 'required'],
            [['user_id'], 'integer'],
            [['create_date'], 'safe'],
            [['action'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Օգտագործող',
            'action' => 'Գործողություն',
            'create_date' => 'Ստեղծման ամսաթիվ',
        ];
    }

    public static function afterSaves($isset_model, $newattributes, $oldattributes){
        echo "<pre>";
        $log=new Log();
        $sub_page = [];
        if ($isset_model) {
            foreach ($newattributes as $name => $value) {
                if (!empty($oldattributes)) {
                    $old = $oldattributes[$name];

                } else {
                    $old = '';

                }
                if ($value != $old) {
                    $changes = $name . ' ('.$old.') => ('.$value.'), ';
                    $log->user_id = $_SESSION['user_id'];
                    $log->action = 'CHANGE';
                    $log->create_date = date('Y-m-d H:i:s');
                    var_dump($changes);
                    var_dump($_SESSION['user_id']);
                }
            }
            $log->save();
            die;

        } else {
            $log=new Log();
            $log->user_id = $_SESSION['user_id'];
            $log->action = 'CREATE';
            $log->status = 1;
            $log->create_date= date('Y-m-d H:i:s');
            $log->save();
        }
        exit();
        return $log;
    }

}
