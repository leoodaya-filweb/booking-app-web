<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "poteka_weather".
 *
 * @property int $id
 * @property string $station_name
 * @property string $datatime
 * @property float $temperature
 * @property float $humidity
 * @property string $weather
 */
class PotekaWeather extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'poteka_weather';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['station_name', 'datatime', 'temperature', 'humidity', 'weather'], 'required'],
            [['datatime'], 'safe'],
            [['datatime'], 'unique'],
            [['temperature', 'humidity'], 'number'],
            [['station_name'], 'string', 'max' => 250],
            [['weather'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'station_name' => 'Station Name',
            'datatime' => 'Datatime',
            'temperature' => 'Temperature',
            'humidity' => 'Humidity',
            'weather' => 'Weather',
        ];
    }

}
