<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%new_book_subscription}}`.
 */
class m251031_121543_create_new_book_subscription_table extends Migration
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

        $this->createTable('{{%new_book_subscription}}', [
            'id' => $this->primaryKey(),
            'author_id' => $this->integer()->notNull()->comment('ID автора'),
            'phone' => $this->string(20)->notNull()->comment('Телефон'),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP')->comment('Дата создания'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP')->append('ON UPDATE CURRENT_TIMESTAMP')->comment('Дата обновления'),
        ], $tableOptions);

        $this->addForeignKey(
            'fk-subscription-author_id',
            '{{%new_book_subscription}}',
            'author_id',
            '{{%authors}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // Уникальный индекс на комбинацию author_id + phone
        // чтобы нельзя было подписаться несколько раз на одного автора с одним телефоном
        $this->createIndex(
            'idx-subscription-author-phone-unique',
            '{{%new_book_subscription}}',
            ['author_id', 'phone'],
            true
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $this->dropIndex('idx-subscription-author-phone-unique', '{{%new_book_subscription}}');
        $this->dropForeignKey('fk-subscription-author_id', '{{%new_book_subscription}}');
        $this->dropTable('{{%new_book_subscription}}');
    }
}

