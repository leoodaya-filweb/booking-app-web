<?php
use yii\helpers\Html;
$this->title = 'Change Password';
$this->params['breadcrumbs'][] = $this->title;


?>

<div class="user-change-password">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_change-password-form', [
        'model' => $model,
    ]) ?>
</div>