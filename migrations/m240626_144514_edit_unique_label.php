<?php

use yii\db\Migration;

/**
 * Class m240626_144514_edit_unique_label
 */
class m240626_144514_edit_unique_label extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex(
            'idx-customfields_blocks_inputs-label',
            'customfields_blocks_inputs',
            'label',
            true // unique
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240626_144514_edit_unique_label cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240626_144514_edit_unique_label cannot be reverted.\n";

        return false;
    }
    */
}
