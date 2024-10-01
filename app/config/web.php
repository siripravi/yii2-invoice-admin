<?php
/**
 * Don't edit this file.
 * Put your modifications to APPROOT_DIR . '/config.php'
 */

$config = [
    'id' => 'admin',
    'bootstrap' => ['log'],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => ['user/login'],
        ],
        'formatter' => [
            'class' => 'app\base\Formatter',
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
        ], // log
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'login' => 'user/login',
                'register' => 'user/register',
                'logout' => 'user/logout',
                'users' => 'user/index',
                'user/<id:[0-9]+>' => 'user/profile',
            ],
        ], // urlManager
        // Application menus.
        'menu' => [
            'class' => 'app\components\Menu',
            'title' => ['main-nav' => 'Main navigation'],
            'items' => [
                // Navigation menu.
                'main-nav' => [
                    [
                        'label' => 'Administer', 'icon' => 'user-secret',
                        'url' => '#',
                        'items' => [
                            [
                                'label' => 'Users',
                                'icon' => 'circle-o',
                                'url' => ['/user/index'],
                                'roles' => ['viewAnyUser']
                            ],
                            [
                                'label' => 'Settings',
                                'icon' => 'circle-o',
                                'url' => ['/site/settings'],
                                'roles' => function () {
                                    return \app\components\Param::getSectionPermissions();
                                },
                            ],
                        ],
                        'roles' => function () {
                            $roles = \app\components\Param::getSectionPermissions();
                            $roles[] = 'viewAnyUser';
                            return $roles;
                        },
                    ],
                    [
                        'label' => 'Development', 'icon' => 'building-o',
                        'url' => '#',
                        'items' => [
                            [
                                'label' => 'Gii', 'icon' => 'file-code-o',
                                'url' => ['/gii/default/index']
                            ],
                            [
                                'label' => 'Debug', 'icon' => 'dashboard',
                                'url' => ['/debug/default/index']
                            ],
                        ],
                        'guest' => false,
                        'visible' => YII_DEBUG,
                    ],
                    [
                        'label' => 'Login', 'icon' => 'sign-in',
                        'url' => ['/user/login'],
                        'guest' => true,
                    ],
                    [
                        'label' => 'Register', 'icon' => 'user-plus',
                        'url' => ['/user/register'],
                        'guest' => true,
                        'visible' => function () {
                            return ! \app\components\Param::value('User.disableUserRegister');
                        },
                    ],
                    [
                        'label' => 'Logout', 'icon' => 'sign-out',
                        'url' => ['/user/logout'],
                        'guest' => false,
                    ],
                ],
            ],
        ], // menu
    ], // component
    'params' => [
        // Warning! These parameters will override ones in config table.
    ], // params
    'modules' => [
        // Do not use this for modules in @modules directory. They are handled
        // by `./bin/yii module` command.
    ],
];

return yii\helpers\ArrayHelper::merge(require APPROOT_DIR . '/app/config/common.php', $config);
