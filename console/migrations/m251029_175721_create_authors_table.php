<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%authors}}`.
 */
class m251029_175721_create_authors_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp():void
    {
        $this->createTable('{{%authors}}', [
            'id' => $this->primaryKey(),
            'full_name' => $this->string()->notNull()->comment('Полное имя автора'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown():void
    {
        $this->dropTable('{{%authors}}');
    }
}
