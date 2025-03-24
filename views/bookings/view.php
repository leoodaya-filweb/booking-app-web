<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Bookings $model */

$this->title = "Book Number ". $model->id;
if(Yii::$app->user->identity->role_id <= 2){
    $this->params['breadcrumbs'][] = ['label' => 'Bookings', 'url' => ['index']];
}else{
    $this->params['breadcrumbs'][] = ['label' => 'My Bookings', 'url' => ['my-bookings']];
}
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="bookings-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
      
    
    <?php if( $model->status != "completed"  && $model->status != "cancelled"): ?>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
       <!-- <?= 
            Html::a('Complete', ['complete', 'id' => $model->id], [
                'class' => 'btn btn-success',
                'data' => [
                    'confirm' => 'Are you sure you want to change the status to complete?',
                    'method' => 'post',
                ],
            ]) 
        ?> -->
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                'method' => 'post',
                ],
            ]) 
        ?>   

        
    <?php endif; ?>
    <?php if( (Yii::$app->user->identity->role_id == 3) && $model->status != "completed" && $model->status != "cancelled"): ?>
        
        <?= 
            Html::a('Cancel', ['cancel', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to cancel this booking?',
                    'method' => 'post',
                ],
            ])
        ?>
    <?php endif; ?>


    
        
    </php>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'user_id',
                'label' => 'Booker Name',
                'value' => function($model) {
                    return $model->user->name ?? ''; 
                },
            ],
            [
                'attribute' => 'room_id',
                'label' => 'Room',
                'value' => function($model) {
                    return $model->room->name; 
                },
            ],
            [
                'attribute' => 'price',
                'value' => function($model) {
                    return '₱ '.number_format($model->price,2);
                },
            ],
            [
                'attribute' => 'checkin',
                'value' => function($model) {
                    return Yii::$app->formatter->asDate($model->checkin, 'php:M-d-Y h:i:s A');
                },
            ],
            [
                'attribute' => 'checkout',
                'value' => function($model) {
                    return Yii::$app->formatter->asDate($model->checkout, 'php:M-d-Y h:i:s A');
                },
            ],
            [
                'attribute' => 'status',
                'value' => function($model) {
                    return ucfirst($model->status);
                },
            ],
        ],
    ]) ?>

</div>
