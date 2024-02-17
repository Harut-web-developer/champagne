<?php

use yii\db\Migration;

/**
 * Class m240215_110352_add_column_for_products_table
 */
class m240215_110352_add_column_for_products_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('products', 'count_balance', $this->integer(11)->defaultValue(null)->after('count'));
        $this->addColumn('products', 'parent_id', $this->integer(11)->defaultValue(null)->after('AAH'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240215_110352_add_column_for_products_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240215_110352_add_column_for_products_table cannot be reverted.\n";

        return false;
    }
    */
}
