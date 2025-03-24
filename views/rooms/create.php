<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Rooms $model */

$this->title = 'Create Rooms';
$this->params['breadcrumbs'][] = ['label' => 'Rooms', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rooms-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
