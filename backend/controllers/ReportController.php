<?php

namespace backend\controllers;

use common\services\ReportService;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

class ReportController extends Controller
{
    public function __construct(
        $id,
        $module,
        private readonly ReportService $reportService,
        $config = []
    )
    {
        parent::__construct($id, $module, $config);
    }

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
        $year = $this->reportService->validateYear($year);
        $dataProvider = $this->reportService->getAuthorsTop($year);

        return $this->render('authors-top', [
            'year' => $year,
            'dataProvider' => $dataProvider,
        ]);
    }
}

