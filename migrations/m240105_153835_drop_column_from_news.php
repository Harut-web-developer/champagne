<?php

use yii\db\Migration;

/**
 * Class m240105_153835_drop_column_from_news
 */
class m240105_153835_drop_column_from_news extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
     $this->dropColumn('news', 'name');

     $this->alterColumn('news', 'count', $this->text());
    }
}
