<?php

use yii\db\Migration;

/**
 * Class m240201_145607_add_column_warehouse_id_for_users_table
 */
class m240201_145607_add_column_warehouse_id_for_users_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('users', 'warehouse_id', $this->integer(11)->defaultValue(null)->after('role_id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240201_145607_add_column_warehouse_id_for_users_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240201_145607_add_column_warehouse_id_for_users_table cannot be reverted.\n";

        return false;
    }
    */
}
