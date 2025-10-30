<?php

/* @var $this yii\web\View */
/* @var $model common\models\Book */

$this->title = 'Редактировать книгу: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Книги', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактировать';
?>

<div class="book-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>


