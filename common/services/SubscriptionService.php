<?php

namespace common\services;

use common\components\SmspilotService;
use common\models\Book;
use common\models\Subscription;
use common\repositories\SubscriptionRepository;
use Exception;
use Yii;

/**
 * Сервис для работы с подписками
 */
class SubscriptionService
{
    public function __construct(
        private readonly SubscriptionRepository $subscriptionRepository,
        private readonly ?SmspilotService       $smsService = null
    )
    {
    }

    /**
     * Отправляет уведомления всем подписчикам авторов книги
     *
     * @param Book $book Книга
     * @param bool $isNew Флаг новой книги
     * @return int Количество успешно отправленных уведомлений
     */
    public function notifySubscribers(Book $book, bool $isNew): int
    {
        $authors = $book->authors;

        if (empty($authors)) {
            // У книги нет авторов - некому отправлять уведомления
            return 0;
        }

        $authorIds = array_column($authors, 'id');
        // Получим все подписки по списку ID авторов
        $subscriptions = $this->subscriptionRepository->getSubscriptionsByAuthorIds($authorIds);

        $sentCount = 0;
        foreach ($subscriptions as $subscription) {
            if ($this->sendNotification($subscription, $book, $isNew)) {
                $sentCount++;
            }
        }

        return $sentCount;
    }

    /**
     * Отправляет SMS уведомление подписчику о книге
     *
     * @param Subscription $subscription Подписка
     * @param Book $book Книга
     * @param bool $isNew Флаг новой книги
     * @return bool Успешность отправки
     */
    private function sendNotification(Subscription $subscription, Book $book, bool $isNew): bool
    {
        $authorName = $subscription->author->full_name ?? 'автора';
        $action = $isNew ? 'вышла новая книга' : 'обновлена книга';

        // Формируем текст SMS сообщения
        $smsMessage = sprintf(
            "Уведомление: %s '%s' от %s. ISBN: %s",
            $action,
            $book->title,
            $authorName,
            $book->isbn ?? 'N/A'
        );

        // Обрезаем сообщение до 160 символов
        if (mb_strlen($smsMessage) > 160) {
            $smsMessage = mb_substr($smsMessage, 0, 157) . '...';
        }

        try {
            // Получаем компонент SMSPilot, если не передан в конструкторе
            $smsService = $this->smsService ?? Yii::$app->get('smspilot', false);

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

        } catch (Exception $e) {
            Yii::error(
                "Exception while sending SMS to {$subscription->phone}: {$e->getMessage()}",
                'notification'
            );
            return false;
        }
    }
}

