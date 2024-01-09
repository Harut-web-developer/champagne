<?php
use yii\db\Schema;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%news}}`.
 */
class m240105_153638_hhh extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('news', 'count', $this->bigInteger() );
    }

}
