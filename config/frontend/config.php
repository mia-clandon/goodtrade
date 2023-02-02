<?php

use Amirax\SeoTools\Meta;
use common\models\User;
use frontend\modules\cabinet\Module;
use yii\caching\FileCache;
use yii\db\Connection;
use yii\i18n\DbMessageSource;
use yii\log\FileTarget;
use yii\rbac\DbManager;
use yii\swiftmailer\Mailer;
use yii\web\{AssetConverter, JqueryAsset, UrlManager};

return [
    'id' => 'frontend',
    'language' => 'ru-RU',
    'basePath' => Yii::getAlias('@frontend'),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'aliases' => [
        '@vendor' => Yii::getAlias('@vendor'),
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'components' => [
        'db' => [
            'class' => Connection::class,
            'dsn' => env('DB_DSN'),
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
            'charset' => env('DB_CHARSET'),
        ],
        'manticore' => [
            'class' => Connection::class,
            'dsn' => env('MANTICORE_DSN'),
            'charset' => env('MANTICORE_CHARSET'),
            'username' => env('MANTICORE_USERNAME'),
            'password' => env('MANTICORE_PASSWORD'),
        ],
        'cache' => [
            'class' => FileCache::class,
            'cachePath' => env('CACHE_FOLDER'),
        ],
        'mailer' => [
            'class' => Mailer::class,
            'viewPath' => env('TRANSPORT_VIEW_PATH'),
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => env('TRANSPORT_HOST'),
                'username' => env('TRANSPORT_USERNAME'),
                'password' => env('TRANSPORT_PASSWORD'),
                'port' => env('TRANSPORT_PORT'),
                'encryption' => env('TRANSPORT_ENCRYPTION'),
            ],
        ],
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => DbMessageSource::class,
                    'sourceMessageTable' => '{{%translate_source_message}}',
                    'messageTable' => '{{%translate_message}}',
                ],
            ],
        ],
        'request' => [
            'baseUrl' => env('BASE_URL'),
            'cookieValidationKey' => env('COOKIE_VALIDATION_KEY'),
        ],
        'authManager' => [
            'class' => DbManager::class,
        ],
        'formatter' => [
            'dateFormat' => 'dd.MM.yyyy',
        ],
        'seo' => [
            'class' => Meta::class,
        ],
        'urlManager' => [
            'class' => UrlManager::class,
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => array_merge(require __DIR__ . '/rules.php', require __DIR__ . '/../rules.php'),
        ],
        'assetManager' => [
            'converter' => [
                'class' => AssetConverter::class,
            ],
            'bundles' => [
                JqueryAsset::class => [
                    'sourcePath' => null,
                    'js' => [
                        'jquery.js' => '/js/libs/jquery-3.2.1.min.js',
                    ],
                ],
            ],
        ],
        'user' => [
            'identityClass' => User::class,
            'enableAutoLogin' => true,
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
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
    ],
    'params' => [
        'adminEmail' => env('ADMIN_EMAIL'),
        'supportEmail' => env('SUPPORT_EMAIL'),
        'defaultUserPassword' => '123123',
        'form' => [
            'template' => [
                'caching' => true,
                'cache_time' => 3600,
                'debugging' => true,
            ]
        ],
        'storage' => require __DIR__ . '/../storage.php',
        'user.passwordResetTokenExpire' => 3600,
    ],
    'modules' => [
        'api' => [
            'class' => \frontend\modules\api\Module::class,
        ],
        'image' => [
            'class' => \common\modules\image\Module::class,
        ],
        'cabinet' => [
            'class' => Module::class,
        ],
    ],
];