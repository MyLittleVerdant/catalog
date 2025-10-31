<?php

namespace common\services;

use common\repositories\ReportRepository;
use yii\data\ArrayDataProvider;

/**
 * Сервис для работы с отчетами
 */
class ReportService
{
    public function __construct(
        private readonly ReportRepository $reportRepository
    )
    {
    }

    /**
     * Валидирует год и возвращает корректное значение
     *
     * @param mixed $year Год
     * @return int Валидный год
     */
    public function validateYear(mixed $year): int
    {
        $year = (int)$year;
        if ($year < 1900 || $year > 2100) {
            return (int)date('Y');
        }
        return $year;
    }

    /**
     * Получает топ 10 авторов по количеству выпущенных книг за год
     *
     * @param int $year Год
     * @return ArrayDataProvider
     */
    public function getAuthorsTop(int $year): ArrayDataProvider
    {
        return $this->reportRepository->getAuthorsTop($year);
    }
}

