<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var $this yii\web\View */
/** @var $user app\models\User */
/** @var $register app\forms\user\Register */
/** @var $loginUrl string */

$loginUrl = Url::to(Yii::$app->user->loginUrl, true);
?>
<div class="body">
    <p>Hello <?= Html::encode($user->name) ?>,</p>
    
    <p>A site administrator at <strong><?= Yii::$app->name ?></strong> has created an account for you.</p>
    
    <p>You will be able to log in at <?= Html::a($loginUrl, $loginUrl) ?> using:</p>
    <ul>
        <li>Email: <strong><?= $user->email ?></strong></li>
        <li>Password: <strong><?= $register->password ?></strong></li>
    </ul>
</div>
