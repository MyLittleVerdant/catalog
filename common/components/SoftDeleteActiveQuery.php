<?php

namespace common\components;

use yii\db\ActiveQuery;
use yii2tech\ar\softdelete\SoftDeleteQueryBehavior;

class SoftDeleteActiveQuery extends ActiveQuery
{
    public function behaviors(): array
    {
        return [
            'softDeleteQuery' => [
                'class' => SoftDeleteQueryBehavior::class,
            ],
        ];
    }

    public function init(): void
    {
        parent::init();
        $this->notDeleted();
    }
}


