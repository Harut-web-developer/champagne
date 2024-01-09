<?php
use yii\db\Schema;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%news}}`.
 */
class m240105_151050_create_news_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%news}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->defaultValue('gago'),
        ]);
    }

}
