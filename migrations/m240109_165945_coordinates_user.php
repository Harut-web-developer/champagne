<?php

use yii\db\Migration;

/**
 * Class m240109_165945_coordinates_user
 */
class m240109_165945_coordinates_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('coordinates_user', 'user_id', $this->integer(11)->after('id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240109_165945_coordinates_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240109_165945_coordinates_user cannot be reverted.\n";

        return false;
    }
    */
}
