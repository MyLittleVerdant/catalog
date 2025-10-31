<?php

namespace common\listeners;

use common\models\Book;
use common\models\Subscription;
use common\components\SmspilotService;
use Yii;
use yii\base\Event;

/**
 * Обработчик событий для отправки уведомлений о книгах
 */
class BookNotificationListener
{
    /**
     * Обрабатывает событие создания/обновления книги
     *
     * @param Event $event Событие
     */
    public static function handleBookSaved(Event $event): void
    {
        /** @var Book $book */
        $book = $event->sender;

        // Определяем, это новая книга или обновление по имени события
        $isNew = $event->name === Book::EVENT_AFTER_INSERT;

        $authors = $book->authors;

        if (empty($authors)) {
            // У книги нет авторов - некому отправлять уведомления
            return;
        }

        $authorIds = array_column($authors, 'id');

        // Получаем всех подписчиков авторов этой книги
        $subscriptions = Subscription::find()
            ->where(['author_id' => $authorIds])
            ->all();

        foreach ($subscriptions as $subscription) {
            self::sendNotification($subscription, $book, $isNew);
        }
    }

    /**
     * Отправляет уведомление одному подписчику
     *
     * @param Subscription $subscription Подписка
     * @param Book $book Книга
     * @param bool $isNew Флаг новой книги
     * @return bool Успешность отправки
     */
    protected static function sendNotification(Subscription $subscription, Book $book, bool $isNew): bool
    {
        $authorName = $subscription->author->full_name ?? 'автора';
        $action = $isNew ? 'вышла новая книга' : 'обновлена книга';

        // Формируем текст SMS сообщения (максимум 160 символов рекомендуется для 1 SMS)
        $smsMessage = sprintf(
            "Уведомление: %s '%s' от %s. ISBN: %s",
            $action,
            $book->title,
            $authorName,
            $book->isbn ?? 'N/A'
        );

        // Обрезаем сообщение до 160 символов (стандартный лимит для 1 SMS)
        if (mb_strlen($smsMessage) > 160) {
            $smsMessage = mb_substr($smsMessage, 0, 157) . '...';
        }

        try {
            // Получаем компонент SMSPilot
            /** @var SmspilotService $smsService */
            $smsService = Yii::$app->get('smspilot', false);

            if (!$smsService) {
                Yii::warning("SMSPilot service is not configured", 'notification');
                return false;
            }

            // Отправляем SMS
            $result = $smsService->sendSms($subscription->phone, $smsMessage);

            if ($result['success']) {
                Yii::info(
                    "SMS notification sent to {$subscription->phone} about book '{$book->title}'",
                    'notification'
                );
                return true;
            }

            Yii::error(
                "Failed to send SMS to {$subscription->phone}: {$result['error']}",
                'notification'
            );
            return false;

        } catch (\Exception $e) {
            Yii::error(
                "Exception while sending SMS to {$subscription->phone}: {$e->getMessage()}",
                'notification'
            );
            return false;
        }
    }
}

