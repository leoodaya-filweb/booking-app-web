<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Bookings $model */

$this->title = 'Create Bookings';
if(Yii::$app->user->identity->role_id <= 2){
    $this->params['breadcrumbs'][] = ['label' => 'Users Bookings', 'url' => ['index']];
    $this->params['breadcrumbs'][] = ['label' => 'Rooms', 'url' => ['bookings']];
}
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bookings-create">

    <h1><?= Html::encode($this->title) ?></h1>
   


        
    <?= $this->render('_form', [
        'model' => $model,
        'room' => $room,
    ]) ?>

</div>
