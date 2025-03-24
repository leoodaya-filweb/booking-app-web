<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Profile';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="">
<?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>