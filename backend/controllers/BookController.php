<?php

namespace backend\controllers;

use common\models\Author;
use Throwable;
use Yii;
use yii\db\Exception;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use common\models\Book;
use common\services\BookService;
use backend\models\BookSearch;
use yii\web\Response;

class BookController extends Controller
{
    public function __construct(
        $id,
        $module,
        private readonly BookService $bookService,
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
                        'allow' => true,
                        'actions' => ['index', 'view'],
                        'roles' => ['?', '@'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex(): string
    {
        $searchModel = new BookSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionView(int $id): string
    {
        return $this->render('view', [
            'model' => $this->bookService->findBook($id),
        ]);
    }

    public function actionCreate(): Response|string
    {
        $model = new Book();
        $authors = Author::find()->orderBy('full_name')->all();

        if (!$model->load(Yii::$app->request->post()) || !$model->validate()) {
            return $this->render('create', [
                'model' => $model,
                'authors' => $authors,
            ]);
        }

        if ($this->bookService->saveBook($model, true)) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'authors' => Author::find()->orderBy('full_name')->all(),
        ]);
    }

    /**
     * @throws NotFoundHttpException
     * @throws \yii\base\Exception
     */
    public function actionUpdate(int $id): Response|string
    {
        $model = $this->bookService->findBook($id);

        if (!$model->load(Yii::$app->request->post()) || !$model->validate()) {
            return $this->render('update', [
                'model' => $model,
                'authors' => Author::find()->orderBy('full_name')->all(),
            ]);
        }

        if ($this->bookService->saveBook($model, false)) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'authors' => Author::find()->orderBy('full_name')->all(),
        ]);
    }

    /**
     * @throws Throwable
     * @throws StaleObjectException
     * @throws NotFoundHttpException
     */
    public function actionDelete(int $id): Response
    {
        $this->bookService->findBook($id)->delete();
        return $this->redirect(['index']);
    }
}


