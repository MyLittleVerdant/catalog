<?php

/* @var $this yii\web\View */
/* @var $model common\models\Book */

$this->title = 'Добавить книгу';
$this->params['breadcrumbs'][] = ['label' => 'Книги', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="book-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>


