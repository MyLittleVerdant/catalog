<?php

namespace common\services;

use common\models\Author;
use common\models\Book;
use Yii;
use yii\base\Exception;
use yii\helpers\FileHelper;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * Сервис для работы с книгами
 */
class BookService
{
    /**
     * Находит книгу по ID или выбрасывает исключение
     *
     * @param int|string $id ID книги
     * @return Book
     * @throws NotFoundHttpException
     */
    public function findBook(int|string $id): Book
    {
        $book = Book::findOne($id);
        if ($book === null) {
            throw new NotFoundHttpException('Книга не найдена.');
        }
        return $book;
    }

    /**
     * Сохраняет книгу и обрабатывает связанные операции
     *
     * @param Book $book Книга
     * @param bool $isNew Является ли книга новой
     * @return bool Успешно ли сохранена
     * @throws Exception
     */
    public function saveBook(Book $book, bool $isNew): bool
    {
        // Обрабатываем загрузку обложки
        $this->handleCoverImageUpload($book);

        // Сохраняем книгу
        if (!$book->save(false)) {
            return false;
        }

        // Сохраняем связи с авторами
        $this->saveAuthors($book);

        // Перезагружаем связи и вызываем событие после сохранения связей
        $book->refresh();
        $eventName = $isNew ? Book::EVENT_AFTER_INSERT : Book::EVENT_AFTER_UPDATE;
        $book->trigger($eventName);

        return true;
    }

    /**
     * Сохраняет связи книги с авторами
     *
     * @param Book $book Книга
     */
    private function saveAuthors(Book $book): void
    {
        // Удаляем все старые связи
        $book->unlinkAll('authors', true);

        // Создаем новые связи
        if (!empty($book->authorIds)) {
            foreach ($book->authorIds as $authorId) {
                $author = Author::findOne($authorId);
                if ($author) {
                    $book->link('authors', $author);
                }
            }
        }
    }

    /**
     * Обрабатывает загрузку обложки книги
     *
     * @param Book $book Книга
     * @return void Успешно ли загружена обложка
     * @throws Exception
     */
    private function handleCoverImageUpload(Book $book): void
    {
        $book->coverImageFile = UploadedFile::getInstance($book, 'coverImageFile');

        if (!$book->coverImageFile) {
            return;
        }

        $uploadDir = Yii::getAlias('@backend/web/uploads/books');
        FileHelper::createDirectory($uploadDir);

        $fileName = uniqid('cover_', true) . '.' . $book->coverImageFile->extension;
        $fullPath = $uploadDir . DIRECTORY_SEPARATOR . $fileName;

        if ($book->coverImageFile->saveAs($fullPath)) {
            $book->cover_image_url = '/uploads/books/' . $fileName;
        }

    }
}

