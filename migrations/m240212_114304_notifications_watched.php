<?php

use yii\db\Migration;

/**
 * Class m240212_114304_notifications_watched
 */
class m240212_114304_notifications_watched extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('notifications', 'watched', $this->string(255));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240212_114304_notifications_watched cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240212_114304_notifications_watched cannot be reverted.\n";

        return false;
    }
    */
}
