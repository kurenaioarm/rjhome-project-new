<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-RJDataExport',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'language' => 'th',
    'timeZone' => 'Asia/Bangkok',
    'homeUrl' => '/RJDataExport',
    'controllerNamespace' => 'RJDataExport\controllers',
//    'aliases' => [
//        '@bower' => '@vendor/bower-asset',
//        '@npm'   => '@vendor/npm-asset',
//    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-appRJDataExport',
            'csrfCookie' => [
                'httpOnly' => true,
                'path' => '/RJDataExport',
            ],
            'baseUrl' => '/RJDataExport',
            'enableCsrfValidation'=>false,
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-RJDataExport', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the RJDataExport
            'name' => 'advanced-RJDataExport',
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
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => true,
            'rules' => [
            ],
        ],
//        'urlManager' => [
//            'enablePrettyUrl' => true,
//            'showScriptName' => false,
//            'rules' => [
//                'blog_all' => 'blog/post/index',
//                '<controller:\w+>/<id:\d+>' => '<controller>',
//                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
//                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
//            ],
//        ],

    ],
    'params' => $params,
];
