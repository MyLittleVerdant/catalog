<?php

/* @var $this yii\web\View */
/* @var $model common\models\Author */

$this->title = 'Редактировать автора: ' . $model->full_name;
$this->params['breadcrumbs'][] = ['label' => 'Авторы', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->full_name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактировать';
?>

<div class="author-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>

