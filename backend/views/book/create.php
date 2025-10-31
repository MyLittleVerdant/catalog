<?php

use common\models\Author;

/* @var $this yii\web\View */
/* @var $model common\models\Book */
/* @var $authors Author[] */

$this->title = 'Добавить книгу';
$this->params['breadcrumbs'][] = ['label' => 'Книги', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="book-create">
    <?= $this->render('_form', [
        'model' => $model,
        'authors' => $authors,
    ]) ?>
</div>


