<?php


namespace app\controllers\api;

use app\models\User;
use Yii;
use yii\filters\Cors;
use yii\web\Controller;
use yii\web\UnauthorizedHttpException;

class UserController extends Controller{
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


  public function actionIndex()
  {
      $user_id = $this->user->getId();;
      
      if (!$user_id) {
          return $this->asJson(['succes' => false,'message' => 'User not authenticated']);
      }
  
      $user = User::findOne($user_id);
      
      if (!$user) {
          return $this->asJson(['succes' => false, 'message' => 'User not found']);
      }
  
      return $this->asJson($user);
  }
  
  
}
