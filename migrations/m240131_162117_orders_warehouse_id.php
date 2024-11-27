<?php

use yii\db\Migration;

/**
 * Class m240131_162117_orders_warehouse_id
 */
class m240131_162117_orders_warehouse_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('order_items', 'warehouse_id', $this->integer(11)->defaultValue(null)->after('order_id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240131_162117_orders_warehouse_id cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240131_162117_orders_warehouse_id cannot be reverted.\n";

        return false;
    }
    */
}
