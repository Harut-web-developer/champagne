<?php

use yii\db\Migration;

/**
 * Class m240208_143018_add_column_count_by
 */
class m240208_143018_add_column_count_by extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('order_items', 'count_by', $this->integer(11)->defaultValue(null)->after('count'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240208_143018_add_column_count_by cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240208_143018_add_column_count_by cannot be reverted.\n";

        return false;
    }
    */
}
