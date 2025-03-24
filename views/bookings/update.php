<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Bookings $model */

$this->title = 'Update Bookings: ' . $model->id;
if(Yii::$app->user->identity->role_id <= 2)
{
    $this->params['breadcrumbs'][] = ['label' => 'Bookings', 'url' => ['index']];
}
else{
    $this->params['breadcrumbs'][] = ['label' => 'My Bookings', 'url' => ['my-bookings']];
}
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="bookings-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'room' => $room
    ]) ?>

</div>
