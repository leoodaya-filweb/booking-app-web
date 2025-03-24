<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\User $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin([
        'action' => ['user/update-user', 'id' => $model->id],
        'method' => 'post',
        'id' => 'user-form',
    ]); ?>

    <?= $form->field($model, 'id')->hiddenInput()->label(false)    ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'role_id')->dropDownList([2 => 'User', 1 => 'Admin', 3 => 'Super Admin'], ['prompt' => 'Select Role'])->label('Role') ?>

   

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
