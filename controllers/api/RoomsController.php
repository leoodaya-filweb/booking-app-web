<?php
namespace app\controllers\api;

use app\models\Bookings;
use app\models\Rooms;
use app\models\User;
use Yii;
use yii\rest\Controller;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\Cors;
use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;

class RoomsController extends Controller
{
  private $user = null;
  public $modelClass = 'app\models\Rooms';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['cors'] = [
            'class' => Cors::class,
            'cors' => [
                'Origin' => ['*'], // Allow all origins or specify your frontend domain
                'Access-Control-Allow-Credentials' => null,
                'Access-Control-Allow-Headers' => ['X-Custom-Header', 'Authorization', 'Content-Type'],
                'Access-Control-Allow-Methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
            ],
        ];
        return $behaviors;
    }
    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            $accessToken = Yii::$app->request->getHeaders()->get('Authorization');

            if ($accessToken) {
                $accessToken = str_replace('Bearer ', '', $accessToken);

                if ($this->verifyAccessToken($accessToken)) {
                    return true; 
                } else {
                    throw new UnauthorizedHttpException('Invalid or expired access token.');
                }
            } else {
                throw new UnauthorizedHttpException('Access token is required.');
            }
        }
        return false;
    }

    protected function verifyAccessToken($accessToken)
    {
        $this->user = User::findOne(['access_token' => $accessToken]);

        if ( $this->user ) {
            return true;
        }

        return false; 
    }
  
  
    public function actionIndex(){

        
        // $rooms = Rooms::find()->all(); 
        $rooms = Rooms::find()
        ->select(['rooms.*', '(SELECT COUNT(*) FROM reviews WHERE reviews.room_id = rooms.id) AS total_reviews', 
                '(SELECT IFNULL(AVG(reviews.rating), 0) FROM reviews WHERE reviews.room_id = rooms.id) AS average_rating'])
        ->joinWith('bookings') 
        ->groupBy('rooms.id')
        ->orderBy(['COUNT(bookings.room_id)' => SORT_DESC])
        ->asArray()
        ->all();
       
        return $this->asJson(['rooms' => $rooms]);
    }

    public function actionView($id) {
        $id = (int) $id;  // Ensure it's an integer
        error_log("Requested Room ID: " . $id); 
    
        $room = Rooms::find()
            ->select([
                'rooms.*',
                '(SELECT IFNULL(AVG(rating), 0) FROM reviews WHERE reviews.room_id = rooms.id) AS average_rating'
            ])
            ->where(['rooms.id' => $id])
            ->asArray()
            ->one();
    
        if (!$room) {
            error_log("Room not found for ID: " . $id);
            return $this->asJson(['error' => 'Room not found']); 
        }
    
        error_log("Returning Room Data: " . json_encode($room));
        return $this->asJson($room);
    }
    
    
    


    public function actionCreate()
    {
        $requestData = json_decode($this->request->rawBody, true);
        $model = new Rooms();
    
    
        if (Rooms::find()->where(['name' => $requestData['name']])->exists()) {
            return $this->asJson([
                'message' => 'Room is already taken!',
                'success' => false,
            ]);
        }
    
        if ($this->request->isPost) {
          
            try {
                if ($model->load($requestData, '') && $model->save()) {
                  
                    return $this->asJson([
                        'message' => 'Room created successfully!',
                        'success' => true,
                        'data' => $model,
                    ]);
                } else {
                    return $this->asJson([
                        'message' => 'Failed to create a room: ' . implode('; ', $model->getFirstErrors()),
                        'success' => false,
                    ]);
                }
            } catch (\Exception $e) {
                return $this->asJson([
                    'message' => 'An error occurred: ' . $e->getMessage(),
                    'success' => false,
                ]);
            }
        }
    
        return $this->asJson([
            'message' => 'Invalid request method.',
            'success' => false,
        ]);
    }
    

    public function actionUpdate($id)
    {
        $requestData = json_decode($this->request->rawBody, true);
        $model = Rooms::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException('Room not found.');
        }

        if ($this->request->isPost || $this->request->isPut) {
            if ($model->load($requestData, '') && $model->save()) {
                return $this->asJson([
                    'message' => 'Room updated successfully!',
                    'success' => true,
                    'data' => $model,
                ]);
            } else {
                return $this->asJson([
                    'message' => 'Failed to update room: ' . implode('; ', $model->getFirstErrors()),
                    'success' => false,
                ]);
            }
        }

        return $this->asJson([
            'message' => 'Invalid request method.',
            'success' => false,
        ]);
    }



    public function actionDelete($id){
      
        $model = Rooms::findOne($id);

        if(!$model){
            throw new NotFoundHttpException('Booking not found!');
        }

        try {
            $model->delete();

           
            return $this->asJson([
              'message' => 'Room deleted successfully!',
              'success' => true,
          ]);
        } catch (\Throwable $th) {
            return $this->asJson([
                'message' => $th->getCode(),
                'success' => false,
            ]);
        }
    }



}