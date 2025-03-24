<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\User $model */

$this->title = 'Update Profile';
if(Yii::$app->user->identity->role_id == 1 || Yii::$app->user->identity->role_id == 3){
    $this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
}else{
    $this->params['breadcrumbs'][] = ['label' => 'Profile', 'url' => ['view']];
}
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php
        if(Yii::$app->user->identity->role_id == 1 || Yii::$app->user->identity->role_id == 3){
            echo $this->render('_update-user-form', [
                'model' => $model,
                
            ]);
        }else{
            echo $this->render('_form', [
                'model' => $model,
            ]);
        }
   ?>

</div>
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<script>
    $(document).ready(function() {
        $('#user-form').on('submit', function(e) {
            e.preventDefault();
            var form = this;
            var formData = new FormData(form);
            $.ajax({
                url: form.action,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    if (data.success) {
                        console.log('Success');
                    } else {
                        console.log('Error');
                    }
                },
                error: function() {
                    console.log('Error1');
                }
            });
        });
    });

</script> -->