<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Author */

$this->title = $model->full_name;
$this->params['breadcrumbs'][] = ['label' => 'Авторы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="author-view">
    <p>
        <?php if (!Yii::$app->user->isGuest): ?>
            <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Вы уверены, что хотите удалить этого автора?',
                    'method' => 'post',
                ],
            ]) ?>
        <?php endif; ?>
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#subscribeModal">
            Подписаться на новые книги
        </button>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'full_name',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>
</div>

<!-- Modal для подписки (доступно для всех) -->
<div class="modal fade" id="subscribeModal" tabindex="-1" aria-labelledby="subscribeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="subscribeModalLabel">Подписка на новые книги</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Получайте уведомления о новых книгах автора <strong><?= Html::encode($model->full_name) ?></strong></p>
                <form id="subscribeForm">
                    <div class="mb-3">
                        <label for="phone" class="form-label">Телефон</label>
                        <input type="tel" class="form-control" id="phone" name="phone" placeholder="+7 (999) 123-45-67" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <input type="hidden" name="author_id" value="<?= $model->id ?>">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary" id="subscribeBtn">Подписаться</button>
            </div>
        </div>
    </div>
</div>

<?php
$url = \yii\helpers\Url::to(['author/subscribe', 'id' => $model->id]);
$csrfToken = Yii::$app->request->csrfToken;
$this->registerJs(<<<JS
$(document).ready(function() {
    $('#subscribeBtn').on('click', function() {
        var phoneInput = $('#phone');
        var feedback = phoneInput.next('.invalid-feedback');
        var phone = phoneInput.val().trim();
        
        // Очистка предыдущих ошибок
        phoneInput.removeClass('is-invalid');
        feedback.text('');
        
        if (!phone) {
            phoneInput.addClass('is-invalid');
            feedback.text('Пожалуйста, введите телефон');
            return;
        }
        
        // Блокируем кнопку на время запроса
        var btn = $(this);
        btn.prop('disabled', true).text('Отправка...');
        
        $.ajax({
            url: '$url',
            type: 'POST',
            data: {
                phone: phone,
                _csrf: '$csrfToken'
            },
            success: function(response) {
                if (response.success) {
                    // Показываем сообщение об успехе
                    $('.modal-body').html('<div class="alert alert-success">' + response.message + '</div>');
                    $('.modal-footer').hide();
                    
                    // Закрываем модальное окно через 2 секунды
                    setTimeout(function() {
                        $('#subscribeModal').modal('hide');
                        location.reload();
                    }, 2000);
                } else {
                    phoneInput.addClass('is-invalid');
                    feedback.text(response.message || 'Ошибка при подписке');
                    btn.prop('disabled', false).text('Подписаться');
                }
            },
            error: function(xhr) {
                var message = 'Произошла ошибка при отправке запроса';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                phoneInput.addClass('is-invalid');
                feedback.text(message);
                btn.prop('disabled', false).text('Подписаться');
            }
        });
    });
    
    // Сброс формы при закрытии модального окна
    $('#subscribeModal').on('hidden.bs.modal', function () {
        $('#subscribeForm')[0].reset();
        $('#phone').removeClass('is-invalid');
        $('#subscribeBtn').prop('disabled', false).text('Подписаться');
        $('.modal-footer').show();
        $('.modal-body').html('<p>Получайте уведомления о новых книгах автора <strong><?= Html::encode($model->full_name) ?></strong></p>' +
            '<form id="subscribeForm">' +
            '<div class="mb-3">' +
            '<label for="phone" class="form-label">Телефон</label>' +
            '<input type="tel" class="form-control" id="phone" name="phone" placeholder="+7 (999) 123-45-67" required>' +
            '<div class="invalid-feedback"></div>' +
            '</div>' +
            '<input type="hidden" name="author_id" value="<?= $model->id ?>">' +
            '</form>');
    });
});
JS
);
?>

