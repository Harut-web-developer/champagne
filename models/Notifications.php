<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "notifications".
 *
 * @property int $id
 * @property int $role_id
 * @property int $user_id
 * @property string $title
 * @property string $message
 * @property string $datetime
 * @property string $watched
 * @property int $status
 * @property string $sort_
 */
class Notifications extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'notifications';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'message', 'datetime'], 'required'],
            [['message'], 'string'],
            [['datetime'], 'safe'],
            [['title'], 'string', 'max' => 255],
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
            'message' => 'Message',
            'datetime' => 'Datetime',
        ];
    }

    public static function createNotifications($title,$text,$sort)
    {
        date_default_timezone_set('Asia/Yerevan');
        $session = Yii::$app->session;
        $role_id = $session['role_id'];
        $user_id = $session['user_id'];
        if ($session['role_id'] != '1') {
            $model = new Notifications();
            $model->role_id = $role_id;
            $model->user_id = $user_id;
            $model->title = $title;
            $model->message = $text;
            $model->datetime = date('Y-m-d H:i:s');
            $model->sort_ = $sort;
            $model->save();
        }
    }
}
