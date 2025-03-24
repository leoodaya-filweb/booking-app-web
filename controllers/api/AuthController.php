<?php
namespace app\controllers\api;

use app\models\LoginForm;
use app\models\User;
use Yii;
use yii\rest\Controller;
use yii\web\Response;
use function PHPUnit\Framework\isEmpty;

class AuthController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        
        // Enable CORS for the API
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::class,
            'cors' => [
                // Allow from any origin
                'Origin' => ['*'],
                'Access-Control-Allow-Methods' => ['GET', 'POST', 'OPTIONS'],
                'Access-Control-Allow-Headers' => ['Authorization', 'Content-Type'],
            ],
        ];

        return $behaviors;
    }
    // Action to handle login
    public function actionLogin()
    {
        // Parse the request body as JSON
        $requestData = json_decode($this->request->rawBody, true);

        // Validate if the necessary data is provided
        if (!isset($requestData['email']) || !isset($requestData['password'])) {
            return $this->asJson([
                'message' => 'Email and password are required.',
                'success' => false,
            ]);
        }


        // Try to log the user in
        try {
            $user = User::findByUsername($requestData['email']);

            if(!empty($user) && $user->validatePassword($requestData['password'])){

                $user->access_token = $user->generateAccesToken();
                $user->save(false);
                Yii::$app->user->login($user); // This will set the user identity

                return $this->asJson([
                    'message' => 'Login Success',
                    'success' => true,
                    'status' => 200,
                    'access_token' =>  $user->access_token,

                ]);
            }else{
                return $this->asJson([
                    'message' => 'Incorrect Password, plase try again',
                    'success' => false,
                    'status' => 400,
                    
                ]);
            }
           
        } catch (\Throwable $th) {
            // If there's an error, return the exception message
            return $this->asJson([
                'message' => $th->getMessage(),
                'success' => false,
                'status' => $th->getCode()
            ]);
        }
    }
}
