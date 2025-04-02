<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "rooms".
 *
 * @property int $id
 * @property string $name
 * @property float $price
 * @property string $image_path
 * @property string $status
 */
class Rooms extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rooms';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'price','bed', 'image_path', 'status'], 'required'],
            [['price','bed'], 'number'],
            [['image_path'], 'string'],
            [['description'], 'string'],
            [['name'], 'string', 'max' => 250],
            [['status'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'price' => 'Price',
            'bed' => 'Number of Beds',
            'description' => 'Description',
            'image_path' => 'Image Path',
            'status' => 'Status',
        ];
    }

    public function getReviews()
    {
        return $this->hasMany(Reviews::class, ['room_id' => 'id']);
    }


    public function getTotalReviews()
    {
        return $this->hasMany(Reviews::class, ['room_id' => 'id'])->count();
    }

    public function getAverageRating()
    {
        return $this->hasMany(Reviews::class, ['room_id' => 'id'])->average('rating');
    }

    public function getBookings()
    {
        return $this->hasMany(Bookings::class, ['room_id' => 'id']);
    }



}
