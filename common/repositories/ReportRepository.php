<?php

namespace common\repositories;

use yii\data\ArrayDataProvider;
use yii\db\Query;

/**
 * Репозиторий для работы с отчетами в БД
 */
class ReportRepository
{
    /**
     * Получает топ 10 авторов по количеству выпущенных книг за год
     * 
     * @param int $year Год
     * @return ArrayDataProvider
     */
    public function getAuthorsTop(int $year): ArrayDataProvider
    {
        $query = new Query()
            ->select([
                'author.id',
                'author.full_name',
                'COUNT(DISTINCT book.id) as books_count',
            ])
            ->from('{{%authors}} as author')
            ->innerJoin('{{%book_author}} as ba', 'author.id = ba.author_id')
            ->innerJoin('{{%books}} as book', 'ba.book_id = book.id')
            ->where([
                'and',
                ['>=', 'book.release_year', $year . '-01-01'],
                ['<=', 'book.release_year', $year . '-12-31'],
                ['book.deleted_at' => null],
                ['author.deleted_at' => null],
            ])
            ->groupBy(['author.id', 'author.full_name'])
            ->orderBy(['books_count' => SORT_DESC])
            ->limit(10);

        $results = $query->all();

        $data = [];
        foreach ($results as $index => $row) {
            $data[] = [
                'rank' => $index + 1,
                'id' => $row['id'],
                'full_name' => $row['full_name'],
                'books_count' => (int)$row['books_count'],
            ];
        }

        return new ArrayDataProvider([
            'allModels' => $data,
            'pagination' => false,
        ]);
    }
}

