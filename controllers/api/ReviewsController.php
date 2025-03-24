<?php 

namespace app\controllers\api;

use app\models\Bookings;
use app\models\Reviews;
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
  
  
    public function actionIndex(){


        
    }

    public function actionView($id){


       
    }


    public function actionCreate()
    {
        $requestData = json_decode($this->request->rawBody, true);
        $model = new Reviews();
        $booking =  Bookings::findOne($requestData['booking_id']);
        
        
        $userId = $this->user->getId();
    
        if ($this->request->isPost) {
            $model->booking_id = $requestData['booking_id'];
            $model->user_id = $userId;
            $model->rating = $requestData['rating'];
            $model->room_id = $booking->room_id;
            $model->comment = $requestData['comment'];

            try {
                if ($model->load($requestData, '') && $model->save()) {
                    $booking->is_reviewed = true;
                    $booking->save(false);  // Use false to avoid unnecessary validation during save
    
                    return $this->asJson([
                        'message' => 'Reviews successfully submitted!',
                        'success' => true,
                        'data' => $model,
                    ]);
                } else {
                    return $this->asJson([
                        'message' => 'Failed to submit reviews: ' . implode('; ', $model->getFirstErrors()),
                        'success' => false,
                    ]);
                }
            } catch (\Exception $e) {
                // Catch and return any exceptions
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
}