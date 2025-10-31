<?php

namespace common\repositories;

use common\models\Subscription;

/**
 * Репозиторий для работы с подписками в БД
 */
class SubscriptionRepository
{
    /**
     * Получает все подписки по списку ID авторов
     *
     * @param array $authorIds Массив ID авторов
     * @return Subscription[]
     */
    public function getSubscriptionsByAuthorIds(array $authorIds): array
    {
        if (empty($authorIds)) {
            return [];
        }

        return Subscription::find()
            ->where(['author_id' => $authorIds])
            ->all();
    }
}

