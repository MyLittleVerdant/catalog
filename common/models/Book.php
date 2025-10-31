<?php

namespace common\models;

use common\listeners\BookNotificationListener;
use yii\base\InvalidConfigException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii2tech\ar\softdelete\SoftDeleteBehavior;
use common\components\SoftDeleteActiveQuery;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;

/**
 * This is the model class for table "books".
 *
 * @property int $id
 * @property string $title
 * @property string|null $release_year
 * @property string|null $description
 * @property string|null $isbn
 * @property string|null $cover_image_url
 * @property int $created_at
 * @property int|null $updated_at
 * @property int|null $deleted_at
 * @property Author[] $authors
 */
class Book extends ActiveRecord
{
    /** @var UploadedFile|null */
    public $coverImageFile;

    /** @var array Массив ID выбранных авторов для формы */
    public $authorIds = [];

    public static function tableName(): string
    {
        return '{{%books}}';
    }

    public function behaviors(): array
    {
        return [
            [
                'class'              => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value'              => new Expression('CURRENT_TIMESTAMP'),
            ],
            'softDeleteBehavior' => [
                'class'                     => SoftDeleteBehavior::class,
                'softDeleteAttributeValues' => [
                    'deleted_at' => new Expression('CURRENT_TIMESTAMP'),
                ],
                'restoreAttributeValues'    => [
                    'deleted_at' => null,
                ],
                // позволяет вызвать $model->delete(), и это будет soft delete
                'replaceRegularDelete'      => true,
            ],
        ];
    }

    public function rules(): array
    {
        return [
            [['title', 'isbn'], 'required'],
            [['description'], 'string'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['release_year'], 'safe'],
            [['title', 'isbn', 'cover_image_url'], 'string', 'max' => 255],
            [['isbn'], 'unique'],
            [['authorIds'], 'each', 'rule' => ['integer']],
            [
                ['coverImageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => ['png', 'jpg', 'jpeg', 'webp'],
                'maxSize'                                 => 5 * 1024 * 1024
            ],
        ];
    }

    public function afterFind(): void
    {
        parent::afterFind();
        // Загружаем связанные ID авторов для формы
        if (!$this->isNewRecord) {
            $this->authorIds = array_column($this->authors, 'id');
        }
    }

    public function init(): void
    {
        parent::init();
        
        // Подписываемся на события после создания и обновления
        $this->on(self::EVENT_AFTER_INSERT, [BookNotificationListener::class, 'handleBookSaved']);
        $this->on(self::EVENT_AFTER_UPDATE, [BookNotificationListener::class, 'handleBookSaved']);
    }

    public function attributeLabels(): array
    {
        return [
            'id'              => 'ID',
            'title'           => 'Название',
            'release_year'    => 'Дата выпуска',
            'description'     => 'Описание',
            'isbn'            => 'ISBN',
            'cover_image_url' => 'URL обложки',
            'coverImageFile'  => 'Изображение обложки',
            'created_at'      => 'Создано',
            'updated_at'      => 'Обновлено',
            'deleted_at'      => 'Удалено',
        ];
    }

    public static function find(): SoftDeleteActiveQuery
    {
        return new SoftDeleteActiveQuery(static::class);
    }

    /**
     * Связь many-to-many с авторами через pivot таблицу book_author
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getAuthors(): ActiveQuery
    {
        return $this->hasMany(Author::class, ['id' => 'author_id'])
            ->viaTable('{{%book_author}}', ['book_id' => 'id']);
    }
}


