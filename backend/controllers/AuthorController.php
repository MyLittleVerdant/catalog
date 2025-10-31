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
use common\models\Subscription;
use backend\models\AuthorSearch;

class AuthorController extends Controller
{
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
    public function actionView($id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
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
    public function actionUpdate($id): Response|string
    {
        $model = $this->findModel($id);

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
    public function actionDelete($id): Response
    {
        $this->findModel($id)->delete();
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

        // Проверяем существование автора
        $author = Author::findOne($id);
        if (!$author) {
            return $this->asJson([
                'success' => false,
                'message' => 'Автор с указанным ID не найден',
            ]);
        }

        $phone = Yii::$app->request->post('phone');

        if (empty($phone)) {
            return $this->asJson([
                'success' => false,
                'message' => 'Телефон обязателен для заполнения',
            ]);
        }

        // Проверяем, нет ли уже подписки с таким телефоном для этого автора
        $existingSubscription = Subscription::find()
            ->where(['author_id' => $id, 'phone' => $phone])
            ->one();

        if ($existingSubscription) {
            return $this->asJson([
                'success' => false,
                'message' => 'Вы уже подписаны на этого автора',
            ]);
        }

        $subscription = new Subscription();
        $subscription->author_id = $id;
        $subscription->phone = $phone;

        if ($subscription->save()) {
            return $this->asJson([
                'success' => true,
                'message' => 'Вы успешно подписались на уведомления о новых книгах',
            ]);
        }

        return $this->asJson([
            'success' => false,
            'message' => 'Ошибка при сохранении подписки',
            'errors' => $subscription->errors,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    protected function findModel($id): Author
    {
        if (($model = Author::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Автор не найден.');
    }
}

