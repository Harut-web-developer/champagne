<?php

use yii\db\Migration;

/**
 * Class m240208_142000_route_manager
 */
class m240208_142000_route_manager extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('manager_deliver_condition', 'route_id', $this->integer(11)->defaultValue(null)->after('deliver_id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240208_142000_route_manager cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240208_142000_route_manager cannot be reverted.\n";

        return false;
    }
    */
}
