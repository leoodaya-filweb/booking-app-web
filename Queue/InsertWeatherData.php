<?php
namespace app\queue;

use app\models\PotekaWeather;
use Yii;
use yii\base\BaseObject;
use yii\httpclient\Client;
class InsertWeatherData extends BaseObject implements \yii\queue\JobInterface
{
  public function execute($queue){

    $client = new Client();
        
    $encodedAuth = base64_encode("$this->userid:$this->password");

    $params = [
        'swPoint' => '12.378408,118.358942',
        'nePoint' => '18.787537,126.230646',
        "element"  => "humi,temp,weather",
        "startDate" => gmdate('Y-m-d\TH:i:s+00:00', strtotime('-2 day')),
        "endDate"   => "latest",
    ];
    
    $apiUrl = rtrim($this->baseUrl, '/') . '/v1/area/real/ja/rectangle';
    $url = $apiUrl . '?' . http_build_query($params);

    $response = $client->createRequest()
        ->setMethod('GET')
        ->setUrl($url) 
        ->addHeaders([
            'X-POTEKA-Authorization' => $encodedAuth, 
            'Accept' => 'application/json'
        ])
        ->send();

    $data = json_decode($response->getContent(), true);




    if (isset($data['poteka'])) {
        foreach ($data['poteka'] as $station) {
            $stationName = $station['stationInfo']['stationName'];

            $tempData = [];
            $humiData = [];
            $weatherData = [];

            foreach ($station['element'] as $element) {
                foreach ($element['dataList'] as $entry) {
                    $dateTime = $entry['datatime'];

                    if ($element['elementName'] === 'temp') {
                        $tempData[$dateTime] = $entry['value'];
                    } elseif ($element['elementName'] === 'humi') {
                        $humiData[$dateTime] = $entry['value'];
                    } elseif ($element['elementName'] === 'weather') {
                        $weatherData[$dateTime] = $entry['value'];
                    }
                }
            }
           
            foreach ($tempData as $dateTime => $tempValue) {

                $existingRecord =  PotekaWeather::findOne(['datatime' => $dateTime]);

                if(!$existingRecord){
                    $model = new PotekaWeather();

                    $model->station_name = $stationName;
                    $model->datatime = $dateTime;
                    $model->temperature = $tempValue;
                    $model->humidity = $humiData[$dateTime] ?? null;
                    $model->weather = $weatherData[$dateTime] ?? 'N/A';


                    if (!$model->save()) {
                        Yii::$app->session->setFlash("Failed to save weather data: " . json_encode($model->errors));
                    }
                    
                }

               
            }
        }
    }
  
  }
}