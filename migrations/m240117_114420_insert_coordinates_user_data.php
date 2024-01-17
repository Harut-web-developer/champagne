<?php

use yii\db\Migration;

/**
 * Class m240117_114420_insert_coordinates_user_data
 */
class m240117_114420_insert_coordinates_user_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('coordinates_user', 'visit', $this->integer(30)->defaultValue(0)->after('longitude'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240117_114420_insert_coordinates_user_data cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240117_114420_insert_coordinates_user_data cannot be reverted.\n";

        return false;
    }
    */
}
