<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%book_author}}`.
 * Pivot таблица для связи many-to-many между books и authors.
 */
class m251029_175732_create_book_author_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp():void
    {
        $this->createTable('{{%book_author}}', [
            'id' => $this->primaryKey(),
            'book_id' => $this->integer()->notNull()->comment('ID книги'),
            'author_id' => $this->integer()->notNull()->comment('ID автора'),
            'created_at' => $this->integer()->notNull()->comment('Дата создания'),
            'updated_at' => $this->integer()->comment('Дата обновления'),
        ]);

        $this->addForeignKey(
            'fk-book_author-book_id',
            '{{%book_author}}',
            'book_id',
            '{{%books}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-book_author-author_id',
            '{{%book_author}}',
            'author_id',
            '{{%authors}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // Создаем составной уникальный индекс, чтобы предотвратить дублирование связей
        $this->createIndex(
            'idx-book_author-unique',
            '{{%book_author}}',
            ['book_id', 'author_id'],
            true
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown():void
    {
        // Удаляем внешние ключи перед удалением таблицы
        $this->dropForeignKey('fk-book_author-book_id', '{{%book_author}}');
        $this->dropForeignKey('fk-book_author-author_id', '{{%book_author}}');
        
        $this->dropTable('{{%book_author}}');
    }
}
