<?php

use yii\helpers\Url;

/** @var $this yii\web\View */
/** @var $user app\models\User */
/** @var $register app\forms\user\Register */
/** @var $loginUrl string */

$loginUrl = Url::to(Yii::$app->user->loginUrl, true);
?>
Hello <?= $user->name ?>,
A site administrator at <?= Yii::$app->name ?> has created an account for you.

You will be able to log in at <?= $loginUrl ?> using:

Email: <?= $user->email ?>

Password: <?= $register->password ?>
