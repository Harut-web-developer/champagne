<?php

use yii\db\Migration;

/**
 * Class m240202_124050_add_column_for_price_with_aah
 */
class m240202_124050_add_column_for_price_with_aah extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('document_items', 'price_with_aah', $this->float()->defaultValue(null)->after('price'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240202_124050_add_column_for_price_with_aah cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240202_124050_add_column_for_price_with_aah cannot be reverted.\n";

        return false;
    }
    */
}
