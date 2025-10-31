<?php

namespace backend\controllers;

use Throwable;
use Yii;
use yii\db\Exception;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use common\models\Author;
use common\services\AuthorService;
use backend\models\AuthorSearch;

class AuthorController extends Controller
{
    public function __construct(
        $id,
        $module,
        private ?AuthorService $authorService = null,
        $config = []
    ) {
        $this->authorService ??= new AuthorService();
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
                        'actions' => ['index', 'view', 'subscribe'],
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
                    'subscribe' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex(): string
    {
        $searchModel = new AuthorSearch();
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
            'model' => $this->authorService->findAuthor($id),
        ]);
    }

    public function actionCreate(): Response|string
    {
        $model = new Author();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @throws Exception
     * @throws NotFoundHttpException
     */
    public function actionUpdate(int $id): Response|string
    {
        $model = $this->authorService->findAuthor($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @throws StaleObjectException
     * @throws Throwable
     * @throws NotFoundHttpException
     */
    public function actionDelete(int $id): Response
    {
        $this->authorService->findAuthor($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * Подписка на уведомления о новых книгах автора
     * @param int $id ID автора
     * @return Response
     * @throws Exception
     */
    public function actionSubscribe(int $id): Response
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $phone = Yii::$app->request->post('phone');
        $result = $this->authorService->subscribe($id, $phone);

        return $this->asJson($result);
    }

}

