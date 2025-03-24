<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Roles $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="roles-form mt-4">

    <?php $form = ActiveForm::begin(); ?>

    <h6 class="fw-bold">Name</h6>
    <?= $form->field($model, 'name')->textInput(['maxlength' => true])->label(false) ?>

   

    <?php if (!empty($permissions)): ?>
        <div class="">
            <hr>
         
        </div>
        <div class="form-group  d-flex gap-5">
           
            
            <?php 
            $groupedPermissions = [
                'Roles Permissions' => [],
                'Rooms Permissions' => [],
                'User Permissions' => [],
                'Permission Permissions' => [],
                'Bookings Permissions' => []
            ];

            foreach ($permissions as $permission) {
                if (strpos($permission->name, 'roles') !== false) {
                    $groupedPermissions['Roles Permissions'][] = $permission;
                } elseif (strpos($permission->name, 'booking') !== false) {
                    $groupedPermissions['Bookings Permissions'][] = $permission;
                }
                elseif (strpos($permission->name, 'user') !== false) {
                    $groupedPermissions['User Permissions'][] = $permission;
                }
                elseif (strpos($permission->name, 'room') !== false) {
                    $groupedPermissions['Rooms Permissions'][] = $permission;
                }
                elseif (strpos($permission->name, 'permissions') !== false) {
                    $groupedPermissions['Permission Permissions'][] = $permission;
                }
            }
            
            foreach ($groupedPermissions as $groupName => $groupPermissions): ?>
                
                
                <div class="row d-block">
                <h7 class="fw-bold"><?= Html::encode($groupName) ?></h7>
                <?php foreach ($groupPermissions as $permission): ?>
                    <div class="checkbox col ">
                        <label>
                            <?= Html::checkbox('RolePermissions[]', in_array($permission->id, array_column((array) $roles_permissions, 'permissions_id')), ['value' => $permission->id]) ?>
                            <?= Html::encode($permission->name) ?>
                        </label>
                    </div>
                <?php endforeach; ?>
                </div>
           
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No permissions available.</p>
    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
