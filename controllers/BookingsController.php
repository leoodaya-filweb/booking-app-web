<?php

namespace app\controllers;

use app\Components\AccessHelper;
use app\models\Bookings;
use app\models\Rooms;
use app\models\BookingsSearch;
use app\models\RoomSearch;
use app\models\User;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\Pagination;

/**
 * BookingsController implements the CRUD actions for Bookings model.
 */
class BookingsController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
                'access' => [
                    'class' => \yii\filters\AccessControl::className(),
                    'only' => ['index', 'view', 'create', 'update', 'delete', 'my-bookings'],
                    'rules' => [
                        [
                            'allow' => true,
                            'roles' => ['@'],
                        ],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Bookings models.
     *
     * @return string
     */
    public function actionIndex()
    {
        //check permissions
        $this->checkUserPermission('view_booking');

        $userid = \Yii::$app->user->id;

        $search = null;
        if (isset($_POST['search'])) {
            $search = $_POST['search'];
        }

        if ($userid == null) {
            \yii::$app->session->setFlash('error', 'Please login to view your bookings!');
            return $this->redirect(['site/login']);
        }

        $query = Bookings::find();
        if (!empty($search)) {
            $query->joinWith(['user','room'])
            ->andFilterWhere(['like', 'bookings.id', $search])
            ->andFilterWhere(['like', 'user.name', $search])
            ->andFilterWhere(['like', 'rooms.name', $search])
            ->orFilterWhere(['like', 'bookings.status', $search])
            ->orFilterWhere(['like', 'user_type', $search])
            ->orFilterWhere(['like', 'customer_name', $search])
            ->orFilterWhere(['like', 'checkin', $search])
            ->orFilterWhere(['like', 'checkout', $search]);
        }

        $pagination = new Pagination([
            'defaultPageSize' => 8,
            'totalCount' => $query->count(),
        ]);

        $bookings = $query->orderBy('id DESC')
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        return $this->render('bookings', [
            'bookings' => $bookings,
            'pagination' => $pagination,
            'search' => $search,
        ]);
    }


    /**
     * Displays a single Bookings model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
         
        //check permissions
        $this->checkUserPermission('view_booking');

        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Bookings model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate($room_id)
    {
       
        if (Yii::$app->user->isGuest) {
            \yii::$app->session->setFlash('error', 'Please login to book a room!');
            return $this->redirect(['site/login']);
        }

        //check permissions
        $this->checkUserPermission('create_booking');
        $model = new Bookings();
        $room = Rooms::findOne($room_id);

        if (!$room) {
            throw new NotFoundHttpException('The requested room does not exist.');
        }

        if(Yii::$app->user->identity->role_id <=2){
            $userId = null;
        }else{
            $userId = \Yii::$app->user->id; 
            // Get the ID of the logged-in user
        }
        
        $model->user_id = $userId; // Assign the user ID to the booking model

        
        if ($this->request->isPost) {
            
            

            $existingBooking =  Rooms::find()->where(['id'=>$room_id])->andWhere(['status'=>'occupied'])->one();
            if($existingBooking){
                \yii::$app->session->setFlash('error', 'Room is already Booked!');
                return $this->redirect(['bookings']);
                
            }
            try {
                if ($model->load($this->request->post()) && $model->save()) {
                    // Update room status to occupied
                    $room->status = 'occupied';
                    $room->save();

                    Yii::$app->session->setFlash('success', 'Room booked successfully!');
                    return $this->redirect(['bookings/bookings', 'room_id' => $room_id]);
                } else {
                    $errors = $model->getErrors();
                    $errorMessages = [];
                    foreach ($errors as $error) {
                        $errorMessages[] = implode(', ', $error);
                    }
                    Yii::$app->session->setFlash('error', 'Failed to book a room: ' . implode('; ', $errorMessages));
                    return $this->redirect(['create', 'room_id' => $room_id]);
                }
            } catch (\Exception $e) {
                \Yii::$app->session->setFlash('error', 'An error occurred: ' . $e->getMessage());
                return $this->redirect(['create', 'room_id' => $room_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
            'room' => $room,
        ]);
    }

    /**
     * Updates an existing Bookings model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
         
        //check permissions
        $this->checkUserPermission('edit_booking');

        $model = $this->findModel($id);
        $room = Rooms::findOne(['id'=> $model->room_id]);

        
       
        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {

            
            if($this->request->post('Bookings')['status'] === "cancelled" || $this->request->post('Bookings')['status'] === "completed"){
                $room->status = 'Available';
                $room->save();
            }
            

            Yii::$app->session->setFlash('success', 'Booking updated successfully!');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'room' => $room
        ]);
    }

    /**
     * Deletes an existing Bookings model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        //check permissions
        $this->checkUserPermission('delete_booking');

        $model = $this->findModel($id);
        $room = Rooms::findOne(['id' => $model->room_id]);

        $model->delete();
        $room->status = "Available";
        $room->save();

        if (Yii::$app->user->identity->role_id <= 2) {
            Yii::$app->session->setFlash('success', 'Booking deleted successfully!');
            return $this->redirect(['index']);
        } else {
            Yii::$app->session->setFlash('success', 'Booking deleted successfully!');

            return $this->redirect(['my-bookings']);
        }
    }

    /**
     * Finds the Bookings model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Bookings the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Bookings::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionMyBookings()
    {
         
        if(Yii::$app->user->identity->role_id <=2){
            
            throw new \yii\web\ForbiddenHttpException('You do not have permission.');
        }
        //check permissions
        $this->checkUserPermission('view_booking');

        $userid = \Yii::$app->user->id;

        $search = null;
        if (isset($_POST['search'])) {
            $search = $_POST['search'];
        }
        if($userid == null){
            \yii::$app->session->setFlash('error', 'Please login to view your bookings!');
            return $this->redirect(['site/login']);
        }

        $query = Bookings::find();
        if (!empty($search)) {
            $query->joinWith(['user','room'])
            ->andFilterWhere(['like', 'rooms.name', $search])
            ->orFilterWhere(['like', 'status', $search])
            ->orFilterWhere(['like', 'checkin', $search])
            ->orFilterWhere(['like', 'checkout', $search]);
        }
        $query = Bookings::find()->where(['user_id' => $userid]);

        $pagination = new Pagination([
            'defaultPageSize' => 10,
            'totalCount' => $query->count(),
        ]);

        $bookings = $query->orderBy('id DESC')
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        return $this->render('my-bookings', [
            'bookings' => $bookings,
            'pagination' => $pagination,
            'search' => $search
        ]);
    }

    public function actionCancel($id)
    {
         
        //check permissions
        $this->checkUserPermission('edit_booking');


        $model = $this->findModel($id);
        $room = Rooms::findOne($model->room_id);

        if($model->status == 'cancelled'){
            
            \yii::$app->session->setFlash('error', 'Booking is already cancelled!');
            return $this->redirect(['bookings/my-bookings']);
        }

        $model->status = 'cancelled';
        $model->save();

        // Update room status to available
        $room->status = 'Available';
        $room->save();

        \yii::$app->session->setFlash('success', 'Booking cancelled successfully!');
        return $this->redirect(['bookings/view', 'id' => $model->id]);
    }
    public function actionComplete($id)
    {
         //check permissions
         $this->checkUserPermission('edit_booking');

        $model = $this->findModel($id);
        $room = Rooms::findOne($model->room_id);

        if($model->status == 'cancelled'){
            
            \yii::$app->session->setFlash('error', 'Booking is already cancelled!');
            return $this->redirect(['bookings/view', 'id' => $model->id]);
        }

        $model->status = 'completed';
        $model->save();

        // Update room status to available
        $room->status = 'Available';
        $room->save();

        \yii::$app->session->setFlash('success', 'Booking cancelled successfully!');
        return $this->redirect(['bookings/view', 'id' => $model->id]);
    }


    public function actionBookings()
    {

         //check permissions
         

        $searchModel = new RoomSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
       
        if (isset($_POST['search'])) {
            $search = $_POST['search'];
            $dataProvider->query->andFilterWhere(['like', 'name', $search])
                ->orFilterWhere(['like', 'id', $search])
                ->orFilterWhere(['like', 'price', $search])
                ->orFilterWhere(['like', 'status', $search]);
        }

        
        $search = $this->request->post('search');
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'search' => $search,
        ]);
    }

    private function checkUserPermission($permission){
        if (!AccessHelper::checkPermissions(Yii::$app->user->identity->getId(), $permission)) {
            
            throw new \yii\web\ForbiddenHttpException('You do not have permission.');
        }
    }

}
