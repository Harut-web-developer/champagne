<?php

use yii\db\Migration;

/**
 * Class m240117_073125_coordinates_user
 */
class m240117_073125_coordinates_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('coordinates_user', 'created_at', $this->timestamp()->defaultValue(null)->notNull()->after('longitude'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240117_073125_coordinates_user cannot be reverted.\n";
        $this->dropColumn('coordinates_user','created_at');
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240117_073125_coordinates_user cannot be reverted.\n";

        return false;
    }
    */
}
