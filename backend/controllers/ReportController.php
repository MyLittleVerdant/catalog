<?php

namespace backend\controllers;

use Yii;
use yii\data\ArrayDataProvider;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\web\Controller;

class ReportController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['authors-top'],
                        'allow' => true,
                        'roles' => ['?', '@'],
                    ],
                ],
            ],
        ];
    }

    public function actionAuthorsTop(): string
    {
        $year = Yii::$app->request->get('year', date('Y'));
        
        // Валидация года
        $year = (int)$year;
        if ($year < 1900 || $year > 2100) {
            $year = date('Y');
        }

        $dataProvider = $this->buildDataProvider($year);

        return $this->render('authors-top', [
            'year' => $year,
            'dataProvider' => $dataProvider,
        ]);
    }

    protected function buildDataProvider(int $year): ArrayDataProvider
    {
        $data = [];

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

