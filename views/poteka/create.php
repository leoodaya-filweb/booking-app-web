<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\PotekaWeather $model */

$this->title = 'Create Poteka Weather';
$this->params['breadcrumbs'][] = ['label' => 'Poteka Weathers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="poteka-weather-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
