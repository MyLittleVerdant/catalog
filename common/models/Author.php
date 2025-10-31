<?php

namespace common\models;

use yii\base\InvalidConfigException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii2tech\ar\softdelete\SoftDeleteBehavior;
use common\components\SoftDeleteActiveQuery;

/**
 * This is the model class for table "authors".
 *
 * @property int $id
 * @property string $full_name
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 * @property Book[] $books
 */
class Author extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%authors}}';
    }

    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('CURRENT_TIMESTAMP'),
            ],
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::class,
                'softDeleteAttributeValues' => [
                    'deleted_at' => new Expression('CURRENT_TIMESTAMP'),
                ],
                'restoreAttributeValues' => [
                    'deleted_at' => null,
                ],
                'replaceRegularDelete' => true,
            ],
        ];
    }

    public function rules(): array
    {
        return [
            [['full_name'], 'required'],
            [['full_name'], 'string', 'max' => 255],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'full_name' => 'Полное имя',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
            'deleted_at' => 'Удалено',
        ];
    }

    public static function find(): SoftDeleteActiveQuery
    {
        return new SoftDeleteActiveQuery(static::class);
    }

    /**
     * Связь many-to-many с книгами через pivot таблицу book_author
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getBooks(): ActiveQuery
    {
        return $this->hasMany(Book::class, ['id' => 'book_id'])
            ->viaTable('{{%book_author}}', ['author_id' => 'id']);
    }
}

