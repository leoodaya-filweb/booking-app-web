<?php 

namespace app\controllers\api;

use app\models\Bookings;
use app\models\Reviews;
use app\models\Rooms;
use app\models\User;
use DateTime;
use Yii;
use yii\filters\Cors;
use yii\web\Controller;
use yii\web\UnauthorizedHttpException;

class ReviewsController extends Controller
{
  private $user = null;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['cors'] = [
            'class' => Cors::class,
            'cors' => [
                'Origin' => ['*'],
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
                    return true; // Token is valid, proceed with the action
                } else {
                    throw new UnauthorizedHttpException('Invalid or expired access token.');
                }
            } else {
                throw new UnauthorizedHttpException('Access token is required.');
            }
        }
        return false; 
    }

    // Method to verify the access token
    protected function verifyAccessToken($accessToken)
    {
       
        $this->user = User::findOne(['access_token' => $accessToken]);

        if ( $this->user ) {
        
            return true;
        }

        return false; 
    }
  
  
    public function actionIndex(){}

    public function actionView($id){
        $id = (int) $id;  // Ensure it's an integer
    
        $room = Rooms::find()
            ->select([
            'rooms.id',
            'rooms.name',
            '(SELECT IFNULL(AVG(rating), 0) FROM reviews WHERE reviews.room_id = rooms.id) AS average_rating',
            '(SELECT COUNT(*) FROM reviews WHERE reviews.room_id = rooms.id) AS total_reviews'
            ])
            ->with([
                'reviews' => function ($query) {
                    $query->select([
                        'id',
                        'booking_id',
                        'user_id',
                        'room_id',
                        'rating',
                        'comment',
                        'created_at',
                    ])->with([
                        'user' => function ($query) {
                            $query->select([
                                'id',   
                                'name',
                                
                            ]);
                        }
                    ]);
                }
            ])
            ->where(['rooms.id' => $id])
            ->asArray()
            ->one();
    
        if (!$room) {
            error_log("Room not found for ID: " . $id);
            return $this->asJson(['error' => 'Room not found']); 
        }
    
        return $this->asJson($room);
    }


    public function actionCreate()
    {
        $requestData = json_decode($this->request->rawBody, true);
    
        if ($requestData === null) {
            return $this->asJson([
                'message' => 'Invalid JSON request body.',
                'success' => false,
            ]);
        }
    
        Yii::error('Received Review Data: ' . json_encode($requestData), 'application');
    
        $requiredFields = ['booking_id', 'rating', 'comment'];
        foreach ($requiredFields as $field) {
            if (!isset($requestData[$field]) || empty($requestData[$field])) {
                return $this->asJson([
                    'message' => ucfirst($field) . ' is required.',
                    'success' => false,
                ]);
            }
        }
    
        $booking = Bookings::findOne($requestData['booking_id']);
    
        if (!$booking) {
            return $this->asJson([
                'message' => 'Invalid booking ID.',
                'success' => false,
            ]);
        }
    
        if (!isset($booking->room_id)) {
            return $this->asJson([
                'message' => 'Room ID is missing for this booking.',
                'success' => false,
            ]);
        }
    
        $model = new Reviews();
        $model->booking_id = $requestData['booking_id'];
        $model->user_id = $this->user->getId();
        $model->rating = $requestData['rating'];
        $model->room_id = $booking->room_id;
        $model->comment = $requestData['comment'];
    
        try {
            if ($model->load($requestData, '') && $model->save()) {
                $booking->is_reviewed = true;
                $booking->save(false);
    
                return $this->asJson([
                    'message' => 'Review successfully submitted!',
                    'success' => true,
                    'data' => $model,
                ]);
            } else {
                return $this->asJson([
                    'message' => 'Failed to submit review: ' . implode('; ', $model->getFirstErrors()),
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
    
}