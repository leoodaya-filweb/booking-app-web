<?php

namespace app\models;

use Yii;
use yii\rbac\Permission;

/**
 * This is the model class for table "roles_permission".
 *
 * @property int $id
 * @property int|null $user_id
 * @property int $role_id
 * @property int $permissions_id
 */
class RolesPermission extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'roles_permission';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[ 'role_id', 'permissions_id'], 'integer'],
            [['role_id', 'permissions_id'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
           
            'role_id' => 'Role ID',
            'permissions_id' => 'Permissions ID',
        ];
    }

    public function getPermissions(){

        return $this->hasOne(Permissions::class, ['id' => 'permissions_id']);
    }

}
