<?php

namespace app\controllers;

use app\models\Rooms;
use app\models\RoomSearch;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\Components\AccessHelper;
use Yii;

/**
 * RoomsController implements the CRUD actions for Rooms model.
 */
class RoomsController extends Controller
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
            ]
        );
    }

    /**
     * Lists all Rooms models.
     *
     * @return string
     */
    public function actionIndex()
    {
         //check permissions
         $this->checkUserPermission('view_room');
         

        if(\Yii::$app->user->isGuest){
            \Yii::$app->session->setFlash('error', 'You must be logged in to access this page');
            return $this->redirect(['site/login']);
        }

        $search = $this->request->post('search');

        $query = Rooms::find();

        if(!empty($search)){
            $query->filterWhere(['like', 'name', $search])
                ->orFilterWhere(['like', 'price', $search])
                ->orFilterWhere(['like', 'status', $search]);

        }

        $pagination = new Pagination([
            'defaultPageSize' => 8,
            'totalCount' => $query->count()
        ]);

        $rooms = $query->orderBy('id asc')
                ->offset($pagination->offset)
                ->limit($pagination->limit)
                ->all();


        
       return $this->render('index',[
            'search' => $search,
            'pagination' => $pagination,
            'rooms' => $rooms,
       ]);
    }


    public function actionAvailableRooms()
    {
        //check permissions
        $this->checkUserPermission('view_room');
        

        \Yii::info('actionAvailableRooms called', __METHOD__);
        $searchModel = new RoomSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
    
        return $this->render('booked', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Displays a single Rooms model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        //check permissions
        $this->checkUserPermission('view_room');
       

        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Rooms model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        
        //check permissions
        $this->checkUserPermission('create_room');
        $model = new Rooms();

        if ($this->request->isPost) {
            
            
            $name = $this->request->post('Rooms')['name'];
          
            if ($this->existing($name) == 1) {
                Yii::$app->session->setFlash('error', 'Room with this name already existing.');
                
            }else{
                if ($model->load($this->request->post()) && $model->save()) {
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }
           
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Rooms model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        //check permissions
        $this->checkUserPermission('edit_room');
        
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Rooms model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        //check permissions
        $this->checkUserPermission('delete_room');


        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Rooms model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Rooms the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Rooms::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function existing($data)
    {
        return Rooms::find()->where(['name' => $data])->exists();
    }

    private function checkUserPermission($permission){
        if (!AccessHelper::checkPermissions(Yii::$app->user->identity->getId(), $permission)) {
            
            throw new \yii\web\ForbiddenHttpException('You do not have permission.');
        }
    }
}
