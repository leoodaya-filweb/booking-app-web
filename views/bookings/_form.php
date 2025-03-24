<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use function PHPUnit\Framework\isEmpty;

/** @var yii\web\View $this */
/** @var app\models\Bookings $model */
/** @var yii\widgets\ActiveForm $form */
?>

<p>Please fill out the following fields to book a room:</p>

<div class="bookings-form">


    <div class="card">
        <div class="card-header">
            <h5 class="fw-bold">Room Details    </h5>
        </div>
        
        <div class="card-body">
            <div class="row justify-content-center">
                <div class="col-1">
                    <img src="<?= Html::encode($room->image_path)?>" alt="Image photo" height="100px" width="100px">
                </div>
                <div class="col">
                    <h5><?= Html::encode($room->name)?></h5>
                    <p class="m-0">Price Per Night: ₱ <span><?= Html::encode(number_format($room->price,2))?></span></p>
                    <p class="m-0">Status: <span class="px-2 rounded-pill <?= $room->status == 'Available' ? 'bg-success text-light' : 'bg-danger text-light' ?>"><?= Html::encode(ucfirst($room->status))?></span></p>
                </div>
            </div>
        </div>
    </div>
    <?php $form = ActiveForm::begin(); ?>

    
    


    

    <?= $form->field($model, 'user_id')->hiddenInput(['value' => $model->user_id ?? '', 'readonly' => true])->label(false) ?>

    <?= $form->field($model, 'room_id')->hiddenInput(['value' => $room->id ?? ''])->label(false) ?>

    <?= $form->field($model, 'room_name')->hiddenInput(['value' => $room->name ?? '', 'readonly' => true])->label(false) ?>

    <?= $form->field($model, 'user_type')->hiddenInput(['value' => Yii::$app->user->identity->role_id <= 2 ? 'guest'  : 'user', 'readonly' => true])->label(false) ?>

    <?= $form->field($model, 'customer_name')->textInput(['value' => !empty($model->customer_name) ? $model->customer_name : (!empty($model->user_id) ? $model->user->name : (Yii::$app->user->identity->role_id <= 2 ? '' : Yii::$app->user->identity->name))]) ?>

    <?= $form->field($model, 'price_per_night')->hiddenInput(['value' => number_format($room->price,2) ?? '', 'readonly' => true])->label(false) ?>

    <?= $form->field($model, 'checkin')->input('datetime-local', ['id' => 'checkin']) ?>

    <?= $form->field($model, 'checkout')->input('datetime-local', ['id' => 'checkout']) ?>

    <?= $form->field($model, 'price')->textInput(['id' => 'total_price', 'readonly' => true])->label('Total Price') ?>

    <?php if ((Yii::$app->user->identity->role_id == 1 || Yii::$app->user->identity->role_id == 2) || !isEmpty($model->status)): ?>
        <?= $form->field($model, 'status')->dropDownList([
            'Booked' => 'Booked',
            'cancelled' => 'Cancelled',
            'completed' => 'Completed'
        ], [
            'prompt' => 'Select Status',
            'options' => [$model->status => ['Selected' => true]],
            'value' => $model->status ?? 'Booked',
            'disabled' => in_array($model->status, ['completed', 'cancelled'])
        ]) ?>
    <?php else: ?>
        <?= $form->field($model, 'status')->hiddenInput(['value' => 'Booked', 'readonly' => true])->label(false) ?>
    <?php endif; ?>



    


    <div class="form-group">
        <?= Html::submitButton('Save Booking', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkinInput = document.getElementById('checkin');
    const checkoutInput = document.getElementById('checkout');
    const totalPriceInput = document.getElementById('total_price');
    const pricePerNight = <?= $room->price ?? 0 ?>;

    function calculateTotalPrice() {
        const checkinDate = new Date(checkinInput.value);
        const checkoutDate = new Date(checkoutInput.value);
        if (checkinDate && checkoutDate && checkinDate < checkoutDate) {
            const timeDifference = checkoutDate - checkinDate;
            const daysDifference = timeDifference / (1000 * 3600 * 24);
            const totalPrice = daysDifference * pricePerNight;
            totalPriceInput.value = totalPrice.toFixed(2);
        } else {
            totalPriceInput.value = '';
        }
    }

    checkinInput.addEventListener('change', calculateTotalPrice);
    checkoutInput.addEventListener('change', calculateTotalPrice);
});
</script>
