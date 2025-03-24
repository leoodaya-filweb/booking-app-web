<?php

namespace app\controllers;

use app\models\Bookings;
use app\models\EntryForm;
use app\models\SignupForm;
use Yii;
use yii\base\Model;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Rooms;
use app\models\User;
use yii\helpers\BaseUrl;
use yii\httpclient\Client;


class SiteController extends Controller
{
    protected $userid = 'GNSIAD-WMIS';
    protected  $password = 'DeXDqs7Q';
    protected $baseUrl = "https://api.potekanet.com/";
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
    
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            if(Yii::$app->user->identity->role_id == 1){
                return $this->redirect('dashboard');
            }else{
                return $this->goBack();
            }
           
        }
    
        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

   

    public function actionRectangle()
    {
        $client = new Client();
        
        $encodedAuth = base64_encode("$this->userid:$this->password");
    
        $params = [
            'swPoint' => '12.378408,118.358942',
            'nePoint' => '18.787537,126.230646',
            "element"  => "humi,temp,weather,solar,press_s",
            "startDate" => gmdate('Y-m-d\TH:i:s+00:00', strtotime('-1 day')),
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
    
        // return $response->getContent();

        $data = json_decode($response->getContent(), true);
    
        $groupedData = [];

        $chartData = [
            'labels' => [], 
            'temperature' => [],
            'humidity' => []
        ];
    
        if (isset($data['poteka'])) {
            foreach ($data['poteka'] as $station) {
                $stationName = $station['stationInfo']['stationName'];
    
                $tempData = [];
                $humiData = [];
                $weatherData = [];
    
                foreach ($station['element'] as $element) {
                    foreach ($element['dataList'] as $entry) {
                        $dateTime = $entry['datatime'];
    
                        if (!in_array($dateTime, $chartData['labels'])) {
                            // $chartData['labels'][] = date('F j, Y - g:i A', strtotime($dateTime));
                            $chartData['labels'][] = $dateTime;
                        }
                        
                        if ($element['elementName'] === 'temp') {
                            $tempData[$dateTime] = $entry['value'];
                            $chartData['temperature'][] = $entry['value'];
                        } elseif ($element['elementName'] === 'humi') {
                            $humiData[$dateTime] = $entry['value'];
                            $chartData['humidity'][] = $entry['value'];
                        } elseif ($element['elementName'] === 'weather') {
                            $weatherData[$dateTime] = $entry['value'];
                        }
                    }
                }
    
               
                foreach ($tempData as $dateTime => $tempValue) {
                    $groupedData[] = [
                        'stationName' => $stationName,
                        'datetime' => $dateTime,
                        'temperature' => $tempValue,
                        'humidity' => $humiData[$dateTime] ?? null, 
                        'weather' => $weatherData[$dateTime] ?? 'N/A', 
                    ];
                }
            }
        }
    
        $groupedData = array_reverse($groupedData);
        // Pagination
        $pagination = new Pagination([
            'defaultPageSize' => 10,
            'totalCount' => count($groupedData),
        ]);
    
        $paginatedItems = array_slice($groupedData, $pagination->offset, $pagination->limit);
    
        return $this->render('api', [
            'items' => $paginatedItems,
            'chartData' => $chartData,
            'pagination' => $pagination
        ]);
    }
    

    public function actionSay($message = 'Hello')
    {
        return $this->render('say', ['message' => $message]);
    }

    public function actionEntry()
    {
        $model = new EntryForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            // valid data received in $model

            // do something meaningful here about $model ...

            return $this->render('entry-confirm', ['model' => $model]);
        } else {
            // either the page is initially displayed or there is some validation error
            return $this->render('entry', ['model' => $model]);
        }
    }

    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            Yii::$app->session->setFlash('success', 'Thank you for registration. You can now login.');
            return $this->redirect(['login']);
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    public function actionDashboard(){

        if(Yii::$app->user->identity->role_id >=3){
            
            throw new \yii\web\ForbiddenHttpException('You do not have permission.');
        }

        $thisMonthEarnings = Bookings::find()
            ->where(['status' => 'completed'])
            ->andWhere(['MONTH(checkin)' => date('m')])
            ->sum('price');
        $thisYearEarnings = Bookings::find()
            ->where(['status' => 'completed'])
            ->andWhere(['YEAR(checkin)' => date('Y')])
            ->sum('price');
        $completedBookings = Bookings::find()
            ->where(['status' => 'completed'])
            
            ->count('id');
        $availableRooms = Rooms::find()
            ->where(['status' => 'Available'])
            ->count('id');
        $monthlyEarnings = Bookings::find()
            ->select(['MONTH(checkin) as month', 'SUM(price) as earnings'])
            ->where(['status' => 'completed'])
            ->andWhere(['YEAR(checkin)' => date('Y')])
            ->groupBy('MONTH(checkin)')
            ->orderBy('MONTH(checkin)')
            ->asArray()
            ->all();
        
        $topRooms = Bookings::find()
            ->select(['rooms.name as room_name', 'COUNT(bookings.room_id) as bookings'])
            ->innerJoin('rooms', 'bookings.room_id = rooms.id')
            ->where(['bookings.status' => 'completed'])
            ->groupBy(['rooms.name'])
            ->orderBy(['bookings' => SORT_DESC])
            ->asArray()
            ->all();
        
        $registeredUsers = User::find()
            ->where(['role_id' => 3])
            ->count('id');
           
        
        return $this->render('dashboard',[
            'thisMonthEarnings' => $thisMonthEarnings,
            'thisYearEarnings' => $thisYearEarnings,
            'completedBookings' => $completedBookings,
            'availableRooms' => $availableRooms,
            'monthlyEarnings' => $monthlyEarnings,
            'topRooms' => $topRooms,
            'registeredUsers' => $registeredUsers,
        ]);
    }


}
