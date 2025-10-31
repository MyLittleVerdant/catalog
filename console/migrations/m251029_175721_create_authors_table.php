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
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%authors}}', [
            'id' => $this->primaryKey(),
            'full_name' => $this->string()->notNull()->comment('Полное имя автора'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->notNull()->comment('Дата создания'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->append('ON UPDATE CURRENT_TIMESTAMP')->notNull()->comment('Дата обновления'),
            'deleted_at' => $this->timestamp()->null()->defaultValue(null)->comment('Дата удаления'),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown():void
    {
        $this->dropTable('{{%authors}}');
    }
}
