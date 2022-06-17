<?php

use app\models\email\MailPreferences;
use app\models\management\BasePreferences;
use yii\debug\Module;
use yii\log\FileTarget;
use yii\caching\FileCache;
use app\models\auth\User;
use yii\rbac\DbManager;
use yii\swiftmailer\Mailer;
use yii\web\ErrorAction;
use yii\web\UrlNormalizer;

// try connect to DB, if error- open setup
$db = require __DIR__ . '/db.php';

/** @var BasePreferences $appPreferences */
$appPreferences = require __DIR__ . '/appPreferences.php';
/** @var MailPreferences $emailPreferences */
$emailPreferences = require __DIR__ . '/emailPreferences.php';
$params = require __DIR__ . '/params.php';
$urlRules = require __DIR__ . '/rules.php';
$config = [
    'id' => 'cottage-management-system',
    'name' => $appPreferences->sntName,
    'language' => 'ru-RU',
    'sourceLanguage' => 'ru-RU',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '41Wr9H9bU01eoHEamIXGoKREB3tYtaLg',
        ],
        'cache' => [
            'class' => FileCache::class,
        ],
        'user' => [
            'identityClass' => User::class,
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-template', 'httpOnly' => true],
        ],
        'authManager' => [
            'class' => DbManager::class,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => Mailer::class,
            'useFileTransport' => false,
            'messageConfig' => [
                'charset' => 'UTF-8',
                'from' => [$emailPreferences->senderEmail => $emailPreferences->senderName],
            ],
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => $emailPreferences->senderServer,
                'username' => $emailPreferences->senderLogin,
                'password' => $emailPreferences->senderPass,
                'port' => '587',
                'encryption' => 'tls',
                'streamOptions' => [
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    ],
                ],
            ],
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'normalizer' => [
                'class' => UrlNormalizer::class,
                'action' => UrlNormalizer::ACTION_REDIRECT_TEMPORARY, // используем временный редирект вместо постоянного
            ],
            'rules' => $urlRules,
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => Module::class,
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => \yii\gii\Module::class,
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
