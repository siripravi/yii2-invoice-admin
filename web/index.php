<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.alexei@gmail.com>
 * @copyright 2016
 * @version $Id$
 */

define('APPROOT_DIR', dirname(__DIR__));

$localConfig = [];
if (file_exists(APPROOT_DIR . '/config.php')) {
    $localConfig = require(APPROOT_DIR . '/config.php');
}
else {
    die('config.php is missing.');
}

require(APPROOT_DIR . '/vendor/autoload.php');
require(APPROOT_DIR . '/vendor/yiisoft/yii2/Yii.php');

$config = yii\helpers\ArrayHelper::merge(
    require(APPROOT_DIR . '/app/config/web.php'),
    $localConfig
);

$app = new app\base\WebApplication($config);
$app->run();
