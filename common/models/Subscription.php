<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "new_book_subscription".
 *
 * @property int $id
 * @property int $author_id
 * @property string $phone
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Author $author
 */
class Subscription extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%new_book_subscription}}';
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
        ];
    }

    public function rules(): array
    {
        return [
            [['author_id', 'phone'], 'required'],
            [['author_id'], 'integer'],
            [['phone'], 'string', 'max' => 20],
            [['phone'], 'match', 'pattern' => '/^[\d\s\-\+\(\)]+$/', 'message' => 'Некорректный формат телефона'],
            [['created_at', 'updated_at'], 'safe'],
            [
                ['author_id'], 'exist',
                'skipOnError' => true,
                'targetClass' => Author::class,
                'targetAttribute' => ['author_id' => 'id']
            ],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'author_id' => 'Автор',
            'phone' => 'Телефон',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getAuthor(): ActiveQuery
    {
        return $this->hasOne(Author::class, ['id' => 'author_id']);
    }
}

