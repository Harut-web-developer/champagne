<?php

use yii\db\Migration;

/**
 * Class m240620_122306_add_column_clients_table
 */
class m240620_122306_add_column_clients_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('clients', 'debt_limit', $this->integer(11)->after('sort_'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240620_122306_add_column_clients_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240620_122306_add_column_clients_table cannot be reverted.\n";

        return false;
    }
    */
}
