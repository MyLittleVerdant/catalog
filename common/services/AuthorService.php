<?php

namespace common\services;

use common\models\Author;
use common\models\Subscription;
use yii\db\Exception;
use yii\web\NotFoundHttpException;

/**
 * Сервис для работы с авторами
 */
class AuthorService
{
    /**
     * Подписывает пользователя на уведомления о новых книгах автора
     * 
     * @param int $authorId ID автора
     * @param string $phone Телефон подписчика
     * @return array Результат операции ['success' => bool, 'message' => string, 'errors' => array|null]
     * @throws Exception
     */
    public function subscribe(int $authorId, string $phone): array
    {
        // Проверяем существование автора
        $author = Author::findOne($authorId);
        if (!$author) {
            return [
                'success' => false,
                'message' => 'Автор с указанным ID не найден',
            ];
        }

        // Валидируем телефон
        $phone = trim($phone);
        if (empty($phone)) {
            return [
                'success' => false,
                'message' => 'Телефон обязателен для заполнения',
            ];
        }

        // Проверяем, нет ли уже подписки с таким телефоном для этого автора
        $existingSubscription = Subscription::find()
            ->where(['author_id' => $authorId, 'phone' => $phone])
            ->one();

        if ($existingSubscription) {
            return [
                'success' => false,
                'message' => 'Вы уже подписаны на этого автора',
            ];
        }

        // Создаем новую подписку
        $subscription = new Subscription();
        $subscription->author_id = $authorId;
        $subscription->phone = $phone;

        if ($subscription->save()) {
            return [
                'success' => true,
                'message' => 'Вы успешно подписались на уведомления о новых книгах',
            ];
        }

        return [
            'success' => false,
            'message' => 'Ошибка при сохранении подписки',
            'errors' => $subscription->errors,
        ];
    }

    /**
     * Находит автора по ID или выбрасывает исключение
     * 
     * @param int|string $id ID автора
     * @return Author
     * @throws NotFoundHttpException
     */
    public function findAuthor(int|string $id): Author
    {
        $author = Author::findOne($id);
        if ($author === null) {
            throw new NotFoundHttpException('Автор не найден.');
        }
        return $author;
    }
}

