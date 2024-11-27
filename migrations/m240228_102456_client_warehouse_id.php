<?php

use yii\db\Migration;

/**
 * Class m240228_102456_client_warehouse_id
 */
class m240228_102456_client_warehouse_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('clients', 'client_warehouse_id', $this->integer(11)->defaultValue(null)->after('route_id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240228_102456_client_warehouse_id cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240228_102456_client_warehouse_id cannot be reverted.\n";

        return false;
    }
    */
}
