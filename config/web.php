<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'yarn',
    'name' => 'Вяжем-пряжем',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'language' => 'ru',
    'timeZone' => 'Europe/Samara',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'zUMqX1VBxFT4T1aHa1b52aomOQqCFjP8',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
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
            ],
        ],
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@app/messages',
                    //'sourceLanguage' => 'en-US',
                ],
            ],
        ],
        'formatter' => [
            'dateFormat' => 'php:Y-m-d',
            'timeFormat' => 'php:H:i:s',
            'datetimeFormat' => 'php:Y-m-d H:i:s',
            'decimalSeparator' => '.',
            'thousandSeparator' => ' ',
        ],
    ],
    'modules' => [
       'gridview' =>  [
            'class' => '\kartik\grid\Module'
        ],
        'datecontrol' => [
            'class' => 'kartik\datecontrol\Module',
            // format settings for displaying each date attribute
            'displaySettings' => [
                'date' => 'php:d.m.Y',
                'time' => 'php:H:i:s',
                'datetime' => 'php:d.m.Y H:i:s',
            ],
            // format settings for saving each date attribute
            'saveSettings' => [
                'date' => 'php:Y-m-d',
                'time' => 'php:H:i:s',
                'datetime' => 'php:Y-m-d H:i:s',
            ],
            //'displayTimezone' => 'Europe/Samara',
            //'saveTimezone' => 'UTC',
            // automatically use kartik\widgets for each of the above formats
            'autoWidget' => true,
            // use ajax conversion for processing dates from display format to save format.
            'ajaxConversion' => true,
            // default settings for each widget from kartik\widgets used when autoWidget is true
            'autoWidgetSettings' => [
                'date' => [ 'type' => 2, 'pluginOptions' => ['autoclose' => true]],
                'time' => [ 'pluginOptions' => ['autoclose' => true]],
                'datetime' => [ 'pluginOptions' => ['autoclose' => true]],
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
        'generators' => [
            'venec-gii-model' => ['class' => 'app\modules\gii\model\Generator'],
            'venec-gii-crud'  => ['class' => 'app\modules\gii\crud\Generator'],
        ],
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
