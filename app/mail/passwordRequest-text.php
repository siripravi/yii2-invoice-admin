<?php

/** @var $this yii\web\View */
/** @var $user app\models\User */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['user/password-reset', 'token' => $user->reset_token]);
?>
Hello <?= $user->name ?>,
A request to reset the password for your account has been made at <?= Yii::$app->name ?>.
        
Follow the link below to reset your password:

<?= $resetLink ?>
