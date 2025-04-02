<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\PotekaWeather $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="poteka-weather-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'station_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'datatime')->textInput() ?>

    <?= $form->field($model, 'temperature')->textInput() ?>

    <?= $form->field($model, 'humidity')->textInput() ?>

    <?= $form->field($model, 'weather')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
