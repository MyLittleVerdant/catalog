<?php

use yii\helpers\Html;

/** @var yii\web\View $this */

$this->title = 'Главная страница';
?>
<div class="site-index">

    <div class="jumbotron text-center bg-transparent">
        <h1 class="display-4">Каталог книг</h1>

    </div>

    <div class="body-content">

        <div class="row">
            <div class="col-lg-4">
                <h2>Книги</h2>

                <p>Управление книгами: просмотр, создание, редактирование и удаление книг в каталоге.</p>

                <p><?= Html::a('Перейти к книгам &raquo;', ['/books'], ['class' => 'btn btn-outline-primary']) ?></p>
            </div>
            <div class="col-lg-4">
                <h2>Авторы</h2>

                <p>Управление авторами: просмотр, создание, редактирование и удаление авторов в каталоге.</p>

                <p><?= Html::a('Перейти к авторам &raquo;', ['/authors'], ['class' => 'btn btn-outline-primary']) ?></p>
            </div>
            <div class="col-lg-4">
                <h2>Отчеты</h2>

                <p>Топ 10 авторов по количеству выпущенных книг за выбранный год.</p>

                <p><?= Html::a('Перейти к отчету &raquo;', ['/reports/authors-top'], ['class' => 'btn btn-outline-primary']) ?></p>
            </div>
        </div>

    </div>
</div>
