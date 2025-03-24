<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Roles $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Roles', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="roles-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'name',
                'label' => 'Role Name'
            ],
        ],
    ]) ?>

<h3>Permissions</h3>

<?php if (!empty($roles_permissions)): ?>
    <div class="form-group d-flex flex-wrap gap-5">
        <?php 
        $groupedPermissions = [
            'Roles Permissions' => [],
            'Rooms Permissions' => [],
            'User Permissions' => [],
            'Permission Permissions' => [],
            'Bookings Permissions' => []
        ];

        foreach ($roles_permissions as $permission) {
            if (strpos($permission->permissions->name, 'roles') !== false) {
                $groupedPermissions['Roles Permissions'][] = $permission->permissions->name;
            } 
            elseif (strpos($permission->permissions->name, 'booking') !== false) {
                $groupedPermissions['Bookings Permissions'][] = $permission->permissions->name;
            }
            elseif (strpos($permission->permissions->name, 'user') !== false) {
                $groupedPermissions['User Permissions'][] = $permission->permissions->name;
            }
            elseif (strpos($permission->permissions->name, 'room') !== false) {
                $groupedPermissions['Rooms Permissions'][] = $permission->permissions->name;
            }
            elseif (strpos($permission->permissions->name, 'permissions') !== false) {
                $groupedPermissions['Permission Permissions'][] = $permission->permissions->name;
            }
        }

        foreach ($groupedPermissions as $groupName => $groupPermissions): ?>
            <div class="d-block flex-fill">
                <h7 class="fw-bold"><?= Html::encode($groupName) ?></h7>
                <ul class="row d-block">
                    <?php foreach ($groupPermissions as $permission): ?>
                        <li class="checkbox col-12 col-md-6">
                            <label>
                                <?= Html::encode($permission) ?>
                            </label>
                        </li>

                       
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p>No permissions available.</p>
<?php endif; ?>
</div>