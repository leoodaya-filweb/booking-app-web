<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\rbac\Role;

class User extends ActiveRecord implements \yii\web\IdentityInterface
{
     /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user'; // Adjust this to your actual table name
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    public static function validateAccessToken($token){
        $user = static::findOne(['access_token' => $token]);

        if($user){
            return true;
        }else{
            return false;
        }
    }

    public static function generateAccesToken(){
       return \Yii::$app->security->generateRandomString();
    }


    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return \Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public function setPassword($password)
    {
        $this->password_hash = \Yii::$app->security->generatePasswordHash($password);
    }

    public function generateAuthKey()
    {
        $this->auth_key = \Yii::$app->security->generateRandomString();
    }

    

    public function getIsAdmin()
    {
        return $this->role_id == 1;
    }

   public function getbookings()
    {
        return $this->hasMany(Bookings::class, ['user_id' => 'id']);
    }

    public function getRole()
    {
        return $this->hasOne(Roles::class, ['id' => 'role_id']);
    }

    public function getReviews(){
        return $this->hasMany(Reviews::class, ['user_id' => 'id' ]);
    }
}
