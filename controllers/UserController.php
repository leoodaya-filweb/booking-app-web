<?php

namespace app\controllers;

use app\models\User;
use app\models\UserSearch;
use Yii;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use function PHPUnit\Framework\isEmpty;
use app\Components\AccessHelper;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
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
                    'only' => ['index', 'view', 'create', 'update', 'delete', 'profile', 'change-password'],
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
     * Lists all User models.
     *
     * @return string
     */
    public function actionIndex()
    {
        //CHECK PERMISSION
        $this->checkUserPermission('view_user');
        $search = Yii::$app->request->post('search');
        
        $query = User::find();
        if (!empty($search)) {
            $query->andFilterWhere(['like', 'name', $search])
                ->orFilterWhere(['like', 'username', $search])
                ->orFilterWhere(['like', 'id', $search]);
        } else {
            $search = '';
        }

        $pagination = new Pagination([
            'defaultPageSize' => 8,
            'totalCount' => $query->count(),
        ]);

        $users = $query->andWhere(['in', 'role_id', [2, 3]])
            ->orderBy('name asc')
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        return $this->render('index', [
            'users' => $users,
            'search' => $search,
            'pagination' => $pagination,
        ]);
    }

    /**
     * Displays a single User model.
     * @param int $id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id = null)
    {
        
        //CHECK PERMISSION
        $this->checkUserPermission('view_user');

        if (empty($id)) {
            $id = Yii::$app->user->id;
        }

        $model = $this->findModel($id);

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        //CHECK PERMISSION
        $this->checkUserPermission('create_user');

        $model = new User();
        
        if ($this->request->isPost) {
            
            

            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id = null)
    {
        //CHECK PERMISSION
        $this->checkUserPermission('edit_user');

        if (empty($id)) {
            $id = Yii::$app->user->identity->id;
        }

       
        $model = $this->findModel($id);
      
       
        if ($this->request->isPost) {

            
           

            $post = $this->request->post('User');
            $model->name = $post['name'];
            $model->username = $post['username'];

          
            try {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Profile updated successfully!');
                    return $this->render('view');
                }
            } catch (\Throwable $th) {
                Yii::$app->session->setFlash('error', $th->getMessage());
                return $this->redirect(Yii::$app->request->referrer ?: ['index']);
            }
        }

        return $this->render('update', [
            'model' => $model,
            
        ]);
    }



    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {

        //CHECK PERMISSION
        $this->checkUserPermission('delete_user');
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionChangePassword()
    {
        //CHECK PERMISSION
        
        
        $userid = \Yii::$app->user->identity->id;
        $model = $this->findModel($userid);

        

        if ($this->request->isPost) {
            $this->checkUserPermission('edit_user');
            $post = $this->request->post('User');
          
         
            if($post['password'] != $post['confirmPassword']){
                Yii::$app->session->setFlash('error', 'Password does not match!');
                return $this->redirect(Yii::$app->request->referrer ?: ['index']);
                print('Password does not match!') ;
            }
            
          
            
            $model->password = $model->setPassword($post['password']);
            $model->auth_key = Yii::$app->security->generateRandomString();
            try {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Password updated successfully!');
                    return $this->redirect(['change-password', 'model' => $model]);
                }
                
            } catch (\Throwable $th) {
                Yii::$app->session->setFlash('error', $th->getMessage());
                return $this->redirect(['change-password', 'model' => $model]);
            }
        }
        return $this->render('change-password', ['model' => $model]);
    }

    public function actionProfile(){
        
        //CHECK PERMISSION
        $this->checkUserPermission('edit_user');
        $userid = \Yii::$app->user->id;
        $model = $this->findModel($userid);
        return $this->render('profile', ['model' => $model]);
    }

    public function actionUpdateProfile()
    {
        $this->checkUserPermission('edit_user');
        $model = $this->findModel(Yii::$app->user->id);
        
        if ($this->request->isPost) {
            
             //CHECK PERMISSION
            
            $post = $this->request->post('User');
            $model->setName($post['name']);
            $model->setUsername($post['username']);
            try {
           
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Profile updated successfully!');
                    return $this->redirect(['profile',  'model' => $model,]);
                 } 
                
            } catch (\Throwable $th) {
                Yii::$app->session->setFlash('error', $th->getMessage());
                return $this->redirect(['profile', 'model' => $model]);
            }
        }
        
        


        return $this->render('profile', [
            'model' => $model,
            
        ]);
    }
    
    public function actionSignup()
    {
        $model = new User();

        if ($this->request->isPost) {
            
            
             //CHECK PERMISSION
            $this->checkUserPermission('create_user');

           
          
            
            $post = $this->request->post('User');
            $model->username = $post['username'];
            $model->name = $post['name'];
            $model->role_id = $post['role_id'];
            $model->setPassword($post['password_hash']);
            $model->generateAuthKey();

            try {
                if ($this->existing($model->username) == 1) {
                    Yii::$app->session->setFlash('error', 'Username is already taken.');
                    
                }else{
                    if ($model->save()) {
                        Yii::$app->session->setFlash('success', 'User Creation Successful!');
                        return $this->redirect(Yii::$app->request->referrer ?: ['index']);
                    }
                }
                
            } catch (\Throwable $th) {
                Yii::$app->session->setFlash('error', $th->getMessage());
            }
        }

        return $this->redirect(Yii::$app->request->referrer ?: ['index']);
    }

    public function actionUpdateUser($id)
    {
        
        $model = $this->findModel($id);

        if ($this->request->isPost) {
            
             //CHECK PERMISSION
             $this->checkUserPermission('edit_user');

            $post = $this->request->post('User');
            $model->name = $post['name'];
            $model->username = $post['username'];
            $model->role_id = $post['role_id'];

            try {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'User updated successfully!');
                    return $this->render('update', [
                        'model' => $model,
                        'id' => $id
                    ]);
                }
            } catch (\Throwable $th) {
                Yii::$app->session->setFlash('error', $th->getMessage());
                return $this->render('update', [
                    'model' => $model,
                    'id' => $id
                ]);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'id' => $id
        ]);
    }

    protected function existing($data)
    {
        return User::find()->where(['username' => $data])->exists();
    }

    private function checkUserPermission($permission){
        if (!AccessHelper::checkPermissions(Yii::$app->user->identity->getId(), $permission)) {
            
            throw new \yii\web\ForbiddenHttpException('You do not have permission.');
        }
    }
}
