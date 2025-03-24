<?php

use app\models\Rooms;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\RoomSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Rooms';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rooms-booked">

    <h1><?= Html::encode($this->title) ?></h1>


    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <!-- Topbar Search -->
    
    <div class="row">
        
        <?php foreach ($dataProvider->getModels() as $model): ?>
            <div class="col-md-4">
                <div class="card mb-4">
                    <img src="<?= Html::encode($model->image_path) ?>" class="card-img-top" alt="<?= Html::encode($model->name) ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?= Html::encode($model->name) ?></h5>
                        <p class="card-text">Price: <?= Html::encode($model->price) ?></p>
                        <p class="card-text">Status: <?= Html::encode($model->status) ?></p>
                        <a href="<?= Url::to(['rooms/view', 'id' => $model->id]) ?>" class="btn btn-primary">View</a>
                        
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</div>
