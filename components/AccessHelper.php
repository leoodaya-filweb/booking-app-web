<?php

namespace App\Components;

class AccessHelper
{
  /**
   * Check if the user has access to a specific resource.
   *
   * @param int $userId
   * @param string $resource
   * @return bool
   */
  public static function checkPermissions(int $userId, string $permission): bool
  {
    $checkPermission = (new \yii\db\Query())
      ->select(['roles_permission.*'])
      ->from('roles_permission')
      ->innerJoin('roles', 'roles_permission.role_id = roles.id')
      ->innerJoin('permissions', 'roles_permission.permissions_id = permissions.id')
      ->innerJoin('user', 'user.role_id = roles.id')
      ->where(['user.id' => $userId, 'permissions.name' => $permission])
      ->exists();

    if ($checkPermission) {
      return true;
    }

    return false;
  }
}