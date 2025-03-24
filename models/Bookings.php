<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bookings".
 *
 * @property int $id
 * @property int $user_id
 * @property int $room_id
 * @property float $price
 * @property string $checkin
 * @property string $checkout
 * @property string $status
 */
class Bookings extends \yii\db\ActiveRecord
{

    public function extraFields()
    {
        return ['room'];
    }
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bookings';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[ 'user_type', 'customer_name', 'room_id', 'price', 'checkin', 'checkout', 'status'], 'required'],
            [['user_id', 'room_id'], 'integer'],
            [['price'], 'number'],
            [['checkin', 'checkout'], 'safe'],
            [['status','user_type', 'customer_name'], 'string', 'max' => 250],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'user_type' => 'User Type',
            'customer_name' => 'Name',
            'room_id' => 'Room ID',
            'price' => 'Price',
            'checkin' => 'Checkin',
            'checkout' => 'Checkout',
            'status' => 'Status',
        ];

    }



    public static function findIdentity($id)
    {
        return static::findOne($id);
    }
    public function getRoom()
    {
        return $this->hasOne(Rooms::className(), ['id' => 'room_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    

}
