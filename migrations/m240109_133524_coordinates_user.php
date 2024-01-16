<?php

use yii\db\Migration;

/**
 * Class m240109_133524_coordinates_user
 */
class m240109_133524_coordinates_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%coordinates_user}}', [
            'id' => $this->primaryKey(),
            'latitude' => $this->float(30)->defaultValue(null),
            'longitude' => $this->float(30)->defaultValue(null),
        ]);
    }

    /**
     * {@inheritdoc}
     */
//    public function safeDown()
//    {
//        echo "m240109_133524_coordinates_user cannot be reverted.\n";
//
//        return false;
//    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240109_133524_coordinates_user cannot be reverted.\n";

        return false;
    }
    */
}
