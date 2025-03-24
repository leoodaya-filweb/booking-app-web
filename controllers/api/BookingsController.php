<?php
namespace app\controllers\api;

use app\models\Bookings;
use app\models\Rooms;
use app\models\User;
use DateTime;
use Yii;
use yii\rest\Controller;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\Cors;
use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;

class BookingsController extends Controller
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


        $user_id = $this->user->getId();


        $topRentedRooms = Rooms::find()
            ->select(['rooms.*', '(SELECT COUNT(*) FROM reviews WHERE reviews.room_id = rooms.id) AS total_reviews', 
                    '(SELECT IFNULL(AVG(reviews.rating), 0) FROM reviews WHERE reviews.room_id = rooms.id) AS average_rating'])
            ->joinWith('bookings') 
            ->groupBy('rooms.id')
            ->orderBy(['COUNT(bookings.room_id)' => SORT_DESC])
            ->limit(5)
            ->asArray()
            ->all();



    

        $pastBookings = Bookings::find()
            ->where(['user_id' => $user_id, 'status' => ['completed','cancelled']])
            ->with('room')
            ->orderBy('checkin DESC')
            ->all();    
        
        // Manually format active bookings to include room details
        $pastBookingData = array_map(function($booking) {
            return [
                'id' => $booking->id,
                'user_id' => $booking->user_id,
                'user_type' => $booking->user_type,
                'customer_name' => $booking->customer_name,
                'room_id' => $booking->room_id,
                'price' => $booking->price,
                'checkin' => $booking->checkin,
                'checkout' => $booking->checkout,
                'status' => $booking->status,
                'is_reviewed' => $booking->is_reviewed,
                'room' => $booking->room ? [
                    'id' => $booking->room->id,
                    'name' => $booking->room->name,
                    'image' => $booking->room->image_path,
                ] : null
            ];
        }, $pastBookings);

        $activeBookings = Bookings::find()
            ->where(['user_id' => $user_id, 'status' => 'Booked'])
            ->with('room')
            ->orderBy('checkin DESC')
            ->limit(5)
            ->all();    
        
        // Manually format active bookings to include room details
        $activeBookingsData = array_map(function($activeBookings) {
            return [
                'id' => $activeBookings->id,
                'user_id' => $activeBookings->user_id,
                'user_type' => $activeBookings->user_type,
                'customer_name' => $activeBookings->customer_name,
                'room_id' => $activeBookings->room_id,
                'price' => $activeBookings->price,
                'checkin' => $activeBookings->checkin,
                'checkout' => $activeBookings->checkout,
                'status' => $activeBookings->status,
                'room' => $activeBookings->room ? [
                    'id' => $activeBookings->room->id,
                    'name' => $activeBookings->room->name,
                    'image' => $activeBookings->room->image_path,
                ] : null
            ];
        }, $activeBookings);
        
        return $this->asJson([
            'user_name' =>  $this->user->name,
            'topRooms' => $topRentedRooms,
            'active_bookings' => $activeBookingsData,
            'past_bookings' => $pastBookingData
        ]);
    }

    public function actionView($id){


        $user_id = $this->user->getId();
       
    
        $bookingDetails = Bookings::find()
        ->where(['user_id' => $user_id, 'id' => $id])
        ->with('room')
        ->one();    

        // Check if booking exists
        if ($bookingDetails) {

            $date1 = new DateTime( $bookingDetails->checkin);
            $date2 = new DateTime( $bookingDetails->checkout);
            $diff = $date1->diff($date2);
            $bookingData = [
                'id' => $bookingDetails->id,
                'user_id' => $bookingDetails->user_id,
                'user_type' => $bookingDetails->user_type,
                'customer_name' => $bookingDetails->customer_name,
                'room_id' => $bookingDetails->room_id,
                'price' => $bookingDetails->price,
                'checkin' => $bookingDetails->checkin,
                'checkout' => $bookingDetails->checkout,
                'days' => $diff->days,
                'status' => $bookingDetails->status,
                'room' => $bookingDetails->room ? [
                    'id' => $bookingDetails->room->id,
                    'name' => $bookingDetails->room->name,
                    'image' => $bookingDetails->room->image_path,
                    'price'=> $bookingDetails->room->price
                ] : null
            ];
        } else {
            $bookingData = null; // Handle case when no booking is found
        }

        return $bookingData;
    }


    public function actionCreate()
    {
        $requestData = json_decode($this->request->rawBody, true);
        $model = new Bookings();
        
        $room_id = $requestData['room_id'];
        $room = Rooms::findOne($room_id);
        
        if (!$room) {
            throw new NotFoundHttpException('The requested room does not exist.', );
        }
    
        $userId = $this->user->getId();
    
        $checkin =  $requestData['checkin'];
        $checkout =  $requestData['checkout'];
        
    
        if (strtotime($checkin) >= strtotime($checkout)) {
            return $this->asJson([
                'message' => 'Check-out date must be after the check-in date.',
                'success' => false,
            ]);
        }
    
        if (Rooms::find()->where(['id' => $room_id, 'status' => 'occupied'])->exists()) {
            return $this->asJson([
                'message' => 'Room is already Booked!',
                'success' => false,
            ]);
        }
    
        if ($this->request->isPost) {
            $model->user_id = $userId;
            $model->room_id = $room_id;
            $model->user_type = "user";
            $model->customer_name = $this->user->name;
            $model->status = "Booked";
            $model->checkin = $checkin;
            $model->checkout = $checkout;
            $model->price = $this->request->post('price');
    
            try {
                if ($model->load($requestData, '') && $model->save()) {
                    $room->status = 'occupied';
                    $room->save(false);  // Use false to avoid unnecessary validation during save
    
                    return $this->asJson([
                        'message' => 'Room booked successfully!',
                        'success' => true,
                        'data' => $model,
                    ]);
                } else {
                    return $this->asJson([
                        'message' => 'Failed to book a room: ' . implode('; ', $model->getFirstErrors()),
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
    

    public function actionUpdate($id)
    {
        $requestData = json_decode($this->request->rawBody, true);
        $model = Bookings::findOne($id);

        // Check if booking exists
        if (!$model) {
            throw new NotFoundHttpException('Booking not found.');
        }

        if ($this->request->isPost || $this->request->isPut) {
            if ($model->load($requestData, '') && $model->save()) {
                return $this->asJson([
                    'message' => 'Booking updated successfully!',
                    'success' => true,
                    'data' => $model,
                ]);
            } else {
                return $this->asJson([
                    'message' => 'Failed to update booking: ' . implode('; ', $model->getFirstErrors()),
                    'success' => false,
                ]);
            }
        }

        return $this->asJson([
            'message' => 'Invalid request method.',
            'success' => false,
        ]);
    }


    public function actionCancel($id)
    {
         
     

        $model = Bookings::findOne($id);

        $room = Rooms::findOne($model->room_id);

        if($model->status == 'cancelled'){
            
            return $this->asJson([
                'message' => 'Booking is already cancelled!',
                'success' => false,
            ]);
           
        }

        $model->status = 'cancelled';
        $model->save();

        // Update room status to available
        $room->status = 'Available';
        $room->save();

        return $this->asJson([
            'message' => 'Booking cancelled successfully!',
            'success' => true,
        ]);

    }



    public function actionDelete($id){
      
        $model = Bookings::findOne($id);

        if(!$model){
            throw new NotFoundHttpException('Booking not found!');
        }

        try {
            $model->delete();

            $room = Rooms::findOne($model->room_id);
            if ($room) {
                $room->status = 'available';
                $room->save(false);
            }

            return $this->asJson([
              'message' => 'Booking deleted successfully!',
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