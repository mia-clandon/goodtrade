<?php

use yii\caching\FileCache;
use yii\console\controllers\MigrateController;
use yii\db\Connection;

return [
    'id' => 'trade-console',
    'basePath' => Yii::getAlias('@console'),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'console\controllers',
    'controllerMap' => [
        'migrate' => [
            'class' => MigrateController::class,
            'migrationNamespaces' => [
                'console\migrations',
            ],
            'migrationPath' => null, // allows to disable not namespaced migration completely
        ],
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
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
    ],
    'params' => [],
];