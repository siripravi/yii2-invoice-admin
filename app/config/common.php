<?php
/**
 * Common configuration between console and web.
 */

// Should be defined in web/index.php but ensure that is.
defined('APPROOT_DIR') or define('APPROOT_DIR', dirname(dirname(__DIR__)));

Yii::setAlias('@modules', APPROOT_DIR . '/modules');

// Don't polute global namespace, use anonymous function.
return call_user_func(function () {
    $config = [
        'basePath' => APPROOT_DIR . '/app',
        'vendorPath' => APPROOT_DIR . '/vendor',
        'runtimePath' => APPROOT_DIR . '/runtime',
        'components' => [
            'authManager' => [
                'class' => 'yii\rbac\DbManager',
            ],
        ],
    ];

    if (YII_ENV_DEV) {
        // configuration adjustments for 'dev' environment
        $config['bootstrap'][] = 'debug';
        $config['modules']['debug'] = [
            'class' => 'yii\debug\Module',
        ];

        $config['bootstrap'][] = 'gii';
        $config['modules']['gii'] = [
            'class' => 'yii\gii\Module',
            'newFileMode' => 0664,
            'newDirMode' => 0775,
            'generators' => [
                'module' => [
                    'class' => 'app\base\gii\ModuleGenerator',
                    'templates' => [
                        'app-module' => '@app/views/templates/gii-module',
                    ],
                ],
            ],
        ];

        // Link assets instead of copy them (useful for development environment).
        $config['components']['assetManager']['linkAssets'] = true;
    }
    
    return $config;
});
