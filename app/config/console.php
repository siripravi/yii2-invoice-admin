<?php

Yii::setAlias('@tests', APPROOT_DIR . '/tests');

$config = [
    'id' => 'backend-console',
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
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
    'controllerMap' => [
        'migrate' => [
            'class' => 'yii\console\controllers\MigrateController',
            'templateFile' => '@app/views/templates/migration.php',
        ],
        'serve' => [
            'class' => 'yii\console\controllers\ServeController',
            'docroot' => APPROOT_DIR . '/web',
        ],
#        'fixture' => [ // Fixture generation command line.
#            'class' => 'yii\faker\FixtureController',
#        ],
    ],
];

return yii\helpers\ArrayHelper::merge(require APPROOT_DIR . '/app/config/common.php', $config);
