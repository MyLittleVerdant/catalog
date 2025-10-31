<?php

use yii\grid\ActionColumn;
use yii\grid\SerialColumn;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\AuthorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Авторы';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="author-index">
    <p>
        <?= Html::a('Добавить автора', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => SerialColumn::class],
            'id',
            'full_name',
            [
                'attribute' => 'created_at',
                'format' => ['datetime', 'php:Y-m-d H:i'],
            ],
            [
                'class' => ActionColumn::class,
                'visibleButtons' => [
                    'update' => !Yii::$app->user->isGuest,
                    'delete' => !Yii::$app->user->isGuest,
                    'view' => true,
                ],
            ],
        ],
    ]); ?>
</div>

