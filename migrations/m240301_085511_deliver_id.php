<?php

use yii\db\Migration;

/**
 * Class m240301_085511_deliver_id
 */
class m240301_085511_deliver_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('documents', 'deliver_id', $this->integer(11)->defaultValue(null)->after('user_id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240301_085511_deliver_id cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240301_085511_deliver_id cannot be reverted.\n";

        return false;
    }
    */
}
