<?php

use yii\db\Migration;

/**
 * Class m240131_105225_add_column_to_warehouse
 */
class m240131_105225_add_column_to_warehouse extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->addColumn('documents', 'to_warehouse', $this->integer(11)->defaultValue(null)->after('warehouse_id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240131_105225_add_column_to_warehouse cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240131_105225_add_column_to_warehouse cannot be reverted.\n";

        return false;
    }
    */
}
