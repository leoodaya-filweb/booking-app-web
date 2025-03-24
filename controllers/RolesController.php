<?php

namespace app\controllers;

use app\Components\AccessHelper;
use app\models\Permissions;
use app\models\Roles;
use app\models\RolesPermission;
use app\models\RolesSearch;
use Yii;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * RolesController implements the CRUD actions for Roles model.
 */
class RolesController extends Controller
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
                    'only' => ['index', 'view', 'create', 'update', 'delete'],
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
     * Lists all Roles models.
     *
     * @return string
     */
    public function actionIndex()
    {
        //check permissions
        $this->checkUserPermission('view_roles');


        $search = Yii::$app->request->post('search');
        
       
        $query = Roles::find();
        if (!empty($search)) {
            $query->andFilterWhere(['like', 'name', $search])
                ->orFilterWhere(['like', 'id', $search]);
        }

        $pagination = new Pagination([
            'defaultPageSize' => 8,
            'totalCount' => $query->count(),
        ]);

        $roles = $query->orderBy('id asc')
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        return $this->render('index', [
            'roles' => $roles,
            'search' => $search,
            'pagination' => $pagination,
        ]);
    }

    /**
     * Displays a single Roles model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        //check permissions
        $this->checkUserPermission('view_roles');
        
        $roles_permissions = RolesPermission::findAll(['role_id' => $id]);

       
        return $this->render('view', [
            'model' => $this->findModel($id),
            'roles_permissions' => $roles_permissions
        ]);
    }

    /**
     * Creates a new Roles model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        //check permissions
        $this->checkUserPermission('create_roles');
        
        $model = new Roles();

        if ($this->request->isPost) {
            $name = $this->request->post('Roles')['name'];
            if ($this->existing($name)) {
                Yii::$app->session->setFlash('error', 'Role name is already existing.');
                
            }
            else{
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
     * Updates an existing Roles model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        
        //check permissions
        $this->checkUserPermission('edit_roles');
        
        $model = $this->findModel($id);
        $roles_permissions = RolesPermission::findAll(['role_id' => $id]);
        $permissions = Permissions::find()->all();

        
        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            // Delete existing RolePermissions
            RolesPermission::deleteAll(['role_id' => $id]);
    
            // Save new RolePermissions
            $rolePermissions = Yii::$app->request->post('RolePermissions', []);
            foreach ($rolePermissions as $permissionId) {
                $rolePermission = new RolesPermission();
                $rolePermission->role_id = $model->id;
                $rolePermission->permissions_id = $permissionId;
                $rolePermission->save();
            }
    
            Yii::$app->session->setFlash('success', 'Role information successfully updated.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

       

        
        return $this->render('update', [
            'model' => $model,
            'roles_permissions' =>$roles_permissions,
            'permissions' =>  $permissions,
        ]);
    }

    /**
     * Deletes an existing Roles model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        //check permissions
        $this->checkUserPermission('delete_roles');
        
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Roles model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Roles the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Roles::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    protected function existing($data)
    {
        return Roles::find()->where(['name' => $data])->exists();
    }


    private function checkUserPermission($permission){
        if (!AccessHelper::checkPermissions(Yii::$app->user->identity->getId(), $permission)) {
            
            throw new \yii\web\ForbiddenHttpException('You do not have permission.');
        }
    }
}
