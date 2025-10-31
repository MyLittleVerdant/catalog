<?php

use yii\grid\GridView;
use yii\grid\SerialColumn;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $year int */
/* @var $dataProvider yii\data\ArrayDataProvider */

$this->title = 'Топ 10 авторов по количеству книг';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="report-authors-top">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="report-form" style="margin-bottom: 20px;">
        <form method="get" action="<?= \yii\helpers\Url::to(['report/authors-top']) ?>" class="form-inline">
            <div class="row">
                <div class="col-md-4">
                    <label for="year" style="margin-right: 10px;">Год:</label>
                    <input 
                        type="number" 
                        id="year" 
                        name="year" 
                        value="<?= Html::encode($year) ?>"
                        min="1900" 
                        max="2100" 
                        class="form-control" 
                        style="display: inline-block; width: 120px;"
                        required
                    >
                </div>
                <div class="col-md-4">
                    <?= Html::submitButton('Показать отчет', ['class' => 'btn btn-primary']) ?>
                </div>
            </div>
        </form>
    </div>

    <?php if ($dataProvider !== null && $dataProvider->getCount() > 0): ?>
        <div class="report-results">
            <h3>Результаты за <?= Html::encode($year) ?> год:</h3>
            
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    [
                        'class' => SerialColumn::class,
                        'header' => 'Место',
                    ],
                    [
                        'attribute' => 'rank',
                        'label' => 'Ранг',
                        'headerOptions' => ['style' => 'display:none'],
                        'contentOptions' => ['style' => 'display:none'],
                    ],
                    [
                        'attribute' => 'full_name',
                        'label' => 'Автор',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return Html::a(
                                Html::encode($model['full_name']),
                                ['/author/view', 'id' => $model['id']],
                                ['target' => '_blank']
                            );
                        },
                    ],
                    [
                        'attribute' => 'books_count',
                        'label' => 'Количество книг',
                        'contentOptions' => ['style' => 'text-align: center; font-weight: bold;'],
                    ],
                ],
                'tableOptions' => ['class' => 'table table-striped table-bordered'],
            ]); ?>
        </div>
    <?php elseif ($dataProvider !== null && $dataProvider->getCount() === 0): ?>
        <div class="alert alert-info">
            За выбранный год (<?= Html::encode($year) ?>) не найдено книг с указанными авторами.
        </div>
    <?php endif; ?>
</div>

