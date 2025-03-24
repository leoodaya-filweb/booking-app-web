<?php

namespace app\controllers;

use app\Components\AccessHelper;
use app\models\Permissions;
use app\models\PermissionsSearch;
use Yii;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PermissionsController implements the CRUD actions for Permissions model.
 */
class PermissionsController extends Controller
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
     * Lists all Permissions models.
     *
     * @return string
     */
    public function actionIndex()
    {
        //check permissions
        $this->checkUserPermission('view_permissions');

        $search = $this->request->post('search');

        $query = Permissions::find();

        if(!empty($search)){
            $query->andFilterWhere(['like', 'name', $search])
                ->orFilterWhere(['like', 'id', $search])
                ->orFilterWhere(['like', 'description', $search]);
        }

        $pagination =  New Pagination([
            'defaultPageSize' => 8,
            'totalCount' => $query->count(),
        ]);

        $permissions = $query->orderBy('id desc')
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();



       
        return $this->render('index', [
            'permissions' => $permissions,
            'search' => $search,
            'pagination' => $pagination,
            
        ]);

        
    }

    /**
     * Displays a single Permissions model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Permissions model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        //check permissions
        $this->checkUserPermission('create_permissions');

        $model = new Permissions();

        if ($this->request->isPost) {
            
            
            $name = $this->request->post('Permissions')['name'];
           
            
            if ($this->existing($name) == 1) {
                Yii::$app->session->setFlash('error', 'Permission name already existing.');
                
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
     * Updates an existing Permissions model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        //check permissions
        $this->checkUserPermission('edit_permissions');

        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Permissions model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        //check permissions
        $this->checkUserPermission('delete_permissions');
        
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Permissions model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Permissions the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Permissions::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function existing($data)
    {
        return Permissions::find()->where(['name' => $data])->exists();
    }


    private function checkUserPermission($permission){
        if (!AccessHelper::checkPermissions(Yii::$app->user->identity->getId(), $permission)) {
            
            throw new \yii\web\ForbiddenHttpException('You do not have permission.');
        }
    }
}
