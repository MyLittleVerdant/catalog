<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Author;

/* @var $this yii\web\View */
/* @var $model common\models\Book */
/* @var $form yii\widgets\ActiveForm */
/* @var $authors Author[] */
?>

<div class="book-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'release_year')->input('date') ?>

    <?= $form->field($model, 'isbn')->textInput(['maxlength' => true]) ?>

    <?php if (!empty($model->cover_image_url)): ?>
        <div style="margin-bottom: 10px;">
            <img src="<?= Html::encode($model->cover_image_url) ?>" alt="Обложка" style="max-height: 120px; border: 1px solid #ddd; padding: 2px;">
        </div>
    <?php endif; ?>
    <?= $form->field($model, 'coverImageFile')->fileInput() ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'authorIds')->dropDownList(
        \yii\helpers\ArrayHelper::map($authors, 'id', 'full_name'),
        [
            'multiple' => true,
            'size' => 10,
            'class' => 'form-select',
            'style' => 'height: auto;',
        ]
    )->label('Авторы (используйте Ctrl/Cmd для выбора нескольких)') ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>


