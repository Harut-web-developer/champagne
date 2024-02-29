<?php

use yii\db\Migration;

/**
 * Class m240229_140708_add_column_for_wastrel
 */
class m240229_140708_add_column_for_wastrel extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('document_items', 'wastrel', $this->integer(11)->defaultValue(null)->after('count'));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240229_140708_add_column_for_wastrel cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240229_140708_add_column_for_wastrel cannot be reverted.\n";

        return false;
    }
    */
}
