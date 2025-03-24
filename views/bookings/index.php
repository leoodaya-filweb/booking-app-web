<?php

use app\models\Bookings;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\BookingsSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Booked a Room';
if (isset(Yii::$app->user->identity) && Yii::$app->user->identity->role_id <= 2) {
    $this->params['breadcrumbs'][] = ['label' => 'Users Bookings', 'url' => ['index']];
    $this->params['breadcrumbs'][] = 'Rooms';
} else {
    $this->params['breadcrumbs'][] = $this->title;
}

?>
<div class="bookings-index">

<h1><?= Html::encode($this->title) ?></h1>

<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

<div class="d-flex align-items-center mb-2">
    <?= Html::beginForm(['bookings'], 'POST', ['class' => '  mw-100 navbar-search align-items-end']) ?>
   
        <div class="input-group">
            <?= Html::textInput('search', $search,[

                'class' => 'form-control bg-light small rounded-full',
                'placeholder' => 'Search for...',
                'aria-label' => 'Search',
                'aria-describedby' => 'basic-addon2'
            ])?>
          
            <div class="input-group-append">
                <?= Html::submitButton('<i class="fas fa-search fa-sm"></i>',['class' => 'btn btn-primary', 'type'=> 'button'])?>
                
            </div>
        </div>
    <?= Html::endForm() ?>
</div>
<div class="row">
    
    <?php 
        if(empty($dataProvider->getModels()))
        {
            echo '<div class="col-12 text-center">No rooms found.</div>';
        }
        foreach ($dataProvider->getModels() as $model): ?>
            <div class="col-md-4" data-aos="fade-up">
                <div class="card mb-4">
                    <img src="<?= Html::encode($model->image_path) ?>" class="card-img-top" alt="<?= Html::encode($model->name) ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?= Html::encode($model->name) ?></h5>
                        <p class="card-text">Price: ₱ <?= Html::encode(number_format($model->price,2)) ?></p>
                        <p class="card-text ">Status: <span class="<?= $model->status == "Available" ? 'text-success' : 'text-danger' ?>"><?= Html::encode(ucfirst($model->status)) ?></span></p>
                        <a href="<?= Url::to(['bookings/create', 'room_id' => $model->id]) ?>" class="btn btn-primary">Booked</a>
                    </div>
                </div>
            </div>
    <?php endforeach; ?>
</div>


</div>
