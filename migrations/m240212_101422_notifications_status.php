<?php

use yii\db\Migration;

/**
 * Class m240212_101422_notifications_status
 */
class m240212_101422_notifications_status extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->db->createCommand("ALTER TABLE notifications ADD COLUMN `status` ENUM('1', '0') NOT NULL DEFAULT '1'")->execute();
//        $this->addColumn('notifications', 'status', $this->enum(['1', '0'])->defaultValue('1'));
    }


    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240212_101422_notifications_status cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240212_101422_notifications_status cannot be reverted.\n";

        return false;
    }
    */
}
