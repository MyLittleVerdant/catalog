<?php

namespace common\listeners;

use common\models\Book;
use common\services\SubscriptionService;
use yii\base\Event;

/**
 * Обработчик событий для отправки уведомлений о книгах
 */
class BookNotificationListener
{
    public function __construct(
        private readonly SubscriptionService $subscriptionService
    )
    {
    }

    /**
     * Обрабатывает событие создания/обновления книги
     *
     * @param Event $event Событие
     */
    public function handleBookSaved(Event $event): void
    {
        /** @var Book $book */
        $book = $event->sender;

        // Определяем, это новая книга или обновление по имени события
        $isNew = $event->name === Book::EVENT_AFTER_INSERT;

        // Используем сервис для отправки уведомлений
        $this->subscriptionService->notifySubscribers($book, $isNew);
    }
}

