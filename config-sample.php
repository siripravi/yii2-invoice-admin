<?php
defined('APPROOT_DIR') or die();

/**
 * Local site config.
 * Copy this file to config.php and edit.
 */

// uncomment the following two lines for enable development tool and debug info.
//defined('YII_DEBUG') or define('YII_DEBUG', true);
//defined('YII_ENV') or define('YII_ENV', 'dev');

return [
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - 
            // this is required by cookie validation
            'cookieValidationKey' => '',
        ],
        // Database configuration.
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=yii2basic',
            // SQLite3 example:
            // 'dsn' => 'sqlite:@runtime/data/db.sq3',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
        ],
    ],
    // Configure your modules here:
    'modules' => [
    //    'debug' => [
    //        'allowedIPs' => ['192.168.1.*'],
    //    ],
    //    'gii' => [
    //        'allowedIPs' => ['192.168.1.*'],
    //    ],
    ],
    'params' => [
        // Application parameters.
    ],
];
