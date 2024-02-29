<?php

use yii\db\Migration;

/**
 * Class m240229_063149_notification_status
 */
class m240229_063149_notification_status extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('notifications', 'status', $this->string(255)->defaultValue(','));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240229_063149_notification_status cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240229_063149_notification_status cannot be reverted.\n";

        return false;
    }
    */
}
