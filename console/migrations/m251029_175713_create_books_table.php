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
        $this->createTable('{{%books}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull()->comment('Название книги'),
            'release_year' => $this->date()->comment('Дата выпуска'),
            'description' => $this->text()->comment('Описание книги'),
            'isbn' => $this->string()->unique()->comment('ISBN'),
            'cover_image_url' => $this->string()->comment('URL обложки'),
            'created_at' => $this->integer()->notNull()->comment('Дата создания'),
            'updated_at' => $this->integer()->comment('Дата обновления'),
            'deleted_at' => $this->integer()->defaultValue(null)->comment('Дата удаления'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $this->dropTable('{{%books}}');
    }
}
