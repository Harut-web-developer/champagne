<?php

use yii\db\Migration;

/**
 * Class m240202_152810_add_col_coordinates_user_route_id
 */
class m240202_152810_add_col_coordinates_user_route_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('coordinates_user', 'route_id', $this->integer()->defaultValue(null)->after('user_id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240202_152810_add_col_coordinates_user_route_id cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240202_152810_add_col_coordinates_user_route_id cannot be reverted.\n";

        return false;
    }
    */
}
