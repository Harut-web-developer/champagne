<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "log".
 *
 * @property int $id
 * @property int $user_id
 * @property string $description
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
            [['action', 'description'], 'string', 'max' => 255],
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
            'description' => 'Նկարագրություն',
            'action' => 'Գործողություն',
            'create_date' => 'Ստեղծման ամսաթիվ',
        ];
    }
    public static function afterSaves($isset_model, $newattributes, $oldattributes, $url, $premission){
        $old = 0;
        $changes = "";
        if ($isset_model == 'Update') {
            foreach ($newattributes as $name => $value) {
                if (!empty($oldattributes)) {
                    $old = $oldattributes[$name];

                } else {
                    $old = '';

                }
                if ($value != $old) {

                    $changes = $changes . $name . ' ('.$old.') => ('.$value.'), ' . "\n";
                }
            }
            $changes = $premission['name'] . "\n" . $changes;
            $log=new Log();
            $log->user_id = $_SESSION['user_id'];
            $log->action = $url;
            $log->description = $changes;
            $log->create_date = date('Y-m-d H:i:s');
            $log->save(false);
        }
        else if ($isset_model == 'Create'){
            foreach ($newattributes as $name => $value) {

                $changes .=  ' ('.$name.') => ('.$value.'), ' . "\n";
            }
            $changes = $premission['name'] . "\n" . $changes;
            $log=new Log();
            $log->user_id = $_SESSION['user_id'];
            $log->action = $url;
            $log->description = $changes;
            $log->create_date= date('Y-m-d H:i:s');
            $log->save(false);
        } else if ($isset_model == 'Delete'){
            $log=new Log();
            $log->user_id = $_SESSION['user_id'];
            $log->action = $url;
            $log->description = $premission['name']  . "\n" . '('.'Անուն'.') => '  . $oldattributes;
            $log->create_date= date('Y-m-d H:i:s');
//            var_dump($log);
//            die;
            $log->save(false);
        }
    }

    public function getUserName(){
        return $this->hasOne(Users::className(),['id' => 'user_id']);
    }

}
