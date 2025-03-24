<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\User $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="user-create-form">

    <?php $form = ActiveForm::begin( [
        'action' => ['signup'],
        'method' => 'post',
        'id' => 'user-form',
    ]   ); ?>

 
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'password_hash')->passwordInput(['maxlength' => true])->label('Password') ?>

    <?= $form->field($model, 'role_id')->dropDownList([2 => 'User', 1 => 'Admin', 3 => 'Super Admin'], ['prompt' => 'Select Role'])->label('Role') ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
