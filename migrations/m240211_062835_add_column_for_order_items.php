<?php

use yii\db\Migration;

/**
 * Class m240211_062835_add_column_for_order_items
 */
class m240211_062835_add_column_for_order_items extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('order_items', 'price_by', $this->integer(11)->defaultValue(null)->after('price'));
        $this->addColumn('order_items', 'price_before_discount_by', $this->integer(11)->defaultValue(null)->after('price_before_discount'));
        $this->addColumn('order_items', 'discount_by', $this->integer(11)->defaultValue(null)->after('discount'));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240211_062835_add_column_for_order_items cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240211_062835_add_column_for_order_items cannot be reverted.\n";

        return false;
    }
    */
}
