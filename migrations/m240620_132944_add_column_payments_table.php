<?php

use yii\db\Migration;

/**
 * Class m240620_132944_add_column_payments_table
 */
class m240620_132944_add_column_payments_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('payments', 'client_debt_price', $this->float()->defaultValue(null)->after('payment_sum'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240620_132944_add_column_payments_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240620_132944_add_column_payments_table cannot be reverted.\n";

        return false;
    }
    */
}
