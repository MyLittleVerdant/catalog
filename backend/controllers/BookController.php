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
use common\models\Book;
use backend\models\BookSearch;
use yii\web\Response;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

class BookController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow'   => true,
                        'actions' => ['index', 'view'],
                        'roles'   => ['?', '@'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs'  => [
                'class'   => VerbFilter::class,
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
            'searchModel'  => $searchModel,
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

    /**
     * @throws Exception
     * @throws \yii\base\Exception
     */
    public function actionCreate()
    {
        $model = new Book();

        if (!$model->load(Yii::$app->request->post()) || !$model->validate()) {
            return $this->render('create', [
                'model' => $model,
            ]);
        }

        $model->coverImageFile = UploadedFile::getInstance($model, 'coverImageFile');
        if ($model->coverImageFile) {
            $uploadDir = Yii::getAlias('@backend/web/uploads/books');
            FileHelper::createDirectory($uploadDir);
            $fileName = uniqid('cover_', true) . '.' . $model->coverImageFile->extension;
            $fullPath = $uploadDir . DIRECTORY_SEPARATOR . $fileName;
            if ($model->coverImageFile->saveAs($fullPath)) {
                $model->cover_image_url = '/uploads/books/' . $fileName;
            }
        }

        if ($model->save(false)) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @throws Exception
     * @throws NotFoundHttpException
     * @throws \yii\base\Exception
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (!$model->load(Yii::$app->request->post()) || !$model->validate()) {
            return $this->render('update', [
                'model' => $model,
            ]);
        }

        $model->coverImageFile = UploadedFile::getInstance($model, 'coverImageFile');
        if ($model->coverImageFile) {
            $uploadDir = Yii::getAlias('@backend/web/uploads/books');
            FileHelper::createDirectory($uploadDir);
            $fileName = uniqid('cover_', true) . '.' . $model->coverImageFile->extension;
            $fullPath = $uploadDir . DIRECTORY_SEPARATOR . $fileName;
            if ($model->coverImageFile->saveAs($fullPath)) {
                $model->cover_image_url = '/uploads/books/' . $fileName;
            }
        }

        if ($model->save(false)) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @throws Throwable
     * @throws StaleObjectException
     * @throws NotFoundHttpException
     */
    public function actionDelete($id): Response
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * @throws NotFoundHttpException
     */
    protected function findModel($id): Book
    {
        if (($model = Book::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Книга не найдена.');
    }
}


