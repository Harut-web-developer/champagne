<?php

use yii\db\Migration;

/**
 * Class m240211_131320_add_column_cost_by
 */
class m240211_131320_add_column_cost_by extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('order_items', 'cost_by', $this->integer(11)->defaultValue(null)->after('cost'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240211_131320_add_column_cost_by cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240211_131320_add_column_cost_by cannot be reverted.\n";

        return false;
    }
    */
}
