<?php

use yii\db\Migration;

/**
 * Class m240212_075659_notifications_role_id
 */
class m240212_075659_notifications_role_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('notifications', 'role_id', $this->integer(11)->defaultValue(null)->after('id'));
        $this->addColumn('notifications', 'user_id', $this->integer(11)->defaultValue(null)->after('role_id'));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240212_075659_notifications_role_id cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240212_075659_notifications_role_id cannot be reverted.\n";

        return false;
    }
    */
}
