<?php

use yii\db\Migration;

/**
 * Class m240218_201608_add_column_for_refuse
 */
class m240218_201608_add_column_for_refuse extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('document_items', 'refuse_product_id', $this->integer(11)->defaultValue(null)->after('nomenclature_id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240218_201608_add_column_for_refuse cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240218_201608_add_column_for_refuse cannot be reverted.\n";

        return false;
    }
    */
}
