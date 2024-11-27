<?php

use yii\db\Migration;

/**
 * Class m240621_140316_add_customfields_blocks_inputs_status_column
 */
class m240621_140316_add_customfields_blocks_inputs_status_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            'customfields_blocks_inputs',
            'status',
            "ENUM('1', '0') COLLATE utf8mb4_general_ci DEFAULT '1'"
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240621_140316_add_customfields_blocks_inputs_status_column cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240621_140316_add_customfields_blocks_inputs_status_column cannot be reverted.\n";

        return false;
    }
    */
}
