<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%books}}`.
 */
class m251029_175713_create_books_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%books}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull()->comment('Название книги'),
            'release_year' => $this->date()->comment('Дата выпуска'),
            'description' => $this->text()->comment('Описание книги'),
            'isbn' => $this->string()->unique()->notNull()->comment('ISBN'),
            'cover_image_url' => $this->string()->comment('URL обложки'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->notNull()->comment('Дата создания'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->append('ON UPDATE CURRENT_TIMESTAMP')->notNull()->comment('Дата обновления'),
            'deleted_at' => $this->timestamp()->null()->defaultValue(null)->comment('Дата удаления'),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $this->dropTable('{{%books}}');
    }
}
