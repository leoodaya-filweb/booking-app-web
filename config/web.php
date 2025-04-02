<?php

use app\models\Rooms;

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log','queue'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'queue' => [
            'class' => \yii\queue\db\Queue::class,
            'db' => 'db', // DB connection component or its config
            'tableName' => '{{%queue}}', // Table name
            'channel' => 'default', // Queue channel key
            'mutex' => \yii\mutex\MysqlMutex::class, // Mutex used to sync queries
        ],
        'timezone' => 'Asia/Manila', // Set your desired time zone
        'formatter' => [
            'defaultTimeZone' => 'UTC', // Keep UTC for consistency in backend processing
            'timeZone' => 'Asia/Manila', // Set local time zone
            'dateFormat' => 'php:F j, Y',
            'timeFormat' => 'php:g:i A',
            'datetimeFormat' => 'php:F j, Y - g:i A',
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'enableCsrfValidation' => false, 
            'cookieValidationKey' => 'lXspcO2zQy17jEaY4OMURun49p8R_RhC',    
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => [
                'name' => '_identity',  // Cookie name
                'httpOnly' => true,  // Ensure cookies are only sent over HTTP
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        
        
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'signup' => 'site/signup',
                'booking' => 'bookings/index',
                'rooms' => 'rooms/index',
                'login' => 'site/login',
                'my-bookings' => 'bookings/my-bookings',
                'bookings' => 'bookings/bookings',
                'profile' => 'user/profile',
                'change-password' => 'user/change-password',
                'dashboard' => 'site/dashboard',
                'create-room' => 'rooms/create',
                'users' => 'user/index',
                'home' => 'site/index',
                'booked-room' => 'bookings/create',
                'update-room' => 'rooms/update',
                'view-room' => 'rooms/view',
                'update-profile' => 'user/update',
                'view-user' => 'user/view',
                'view-booking' => 'bookings/view',
                'update-booking' => 'bookings/update',
                'create-user' => 'user/create',
                'user-roles' => 'roles/index',
                'create-role' => 'roles/create',
                'update-role' => 'roles/update',
                'view-role' => 'roles/view',
                'permissions' => 'permissions/index',
                'create-permission' => 'permissions/create',
                'update-permission' => 'permissions/update',
                'view-permission' => 'permissions/view',
                'assign-permission' => 'permissions/assign',
                'assign-role' => 'roles/assign',
                'assign-user' => 'user/assign',
                'update-user' => 'user/update-user',
                'complete-bookings' => 'bookings/complete',
                'cancel-bookings' => 'bookings/cancel',
                'user-delete' => 'user/delete',
                'roles-delete' => 'roles/delete',
                'api-poteka-rectange' => 'site/rectangle',
                'api-poteka-poteka' => 'site/poteka',
                'api-poteka-camara' => 'site/camara',
                'poteka-create' => 'poteka-weather/create',
                ['class' => 'yii\rest\UrlRule', 'controller' => 'api/bookings'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'api/rooms'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'api/user'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'api/reviews'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'api/auth'],


            ],
        ],
        
    ],
    
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}


return $config;
