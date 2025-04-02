<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\PotekaWeather $model */

$this->title = 'Update Poteka Weather: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Poteka Weathers', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="poteka-weather-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
