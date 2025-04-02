<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\PotekaWeatherSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="poteka-weather-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'station_name') ?>

    <?= $form->field($model, 'datatime') ?>

    <?= $form->field($model, 'temperature') ?>

    <?= $form->field($model, 'humidity') ?>

    <?php // echo $form->field($model, 'weather') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
