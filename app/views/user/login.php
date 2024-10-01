<?php

use app\widgets\Check;
use app\components\Param;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var $this yii\web\View */
/** @var $form yii\bootstrap5\ActiveForm */
/** @var $model app\forms\user\Login */
/** @var $disableUserRegister boolean */

$this->title = Yii::t('app', 'Sign In');

$fieldOptions = function ($icon) {
    return [
        'options' => ['class' => 'form-group has-feedback'],
        'inputTemplate' => "{input}<span class='glyphicon glyphicon-$icon form-control-feedback'></span>"
    ];
};
?>

<div class="login-box">
    <div class="login-logo">
        <a href="<?= Url::home(true) ?>"><b><?= Param::value('Site.siteName') ?></b></a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg"><?= Yii::t('app', 'Sign in to start your session') ?></p>

        <?php $form = ActiveForm::begin(['id' => 'login-form', 'enableClientValidation' => false]); ?>

        <?= $form
            ->field($model, 'email', $fieldOptions('envelope'))
            ->label(false)
            ->textInput(['placeholder' => $model->getAttributeLabel('email')]) ?>

        <?= $form
            ->field($model, 'password', $fieldOptions('lock'))
            ->label(false)
            ->passwordInput(['placeholder' => $model->getAttributeLabel('password')]) ?>

        <div class="row">
            <div class="col-xs-8">
                <?= $form->field($model, 'rememberMe')->widget(Check::className())->label(false) ?>
            </div>
            <!-- /.col -->
            <div class="col-xs-4">
                <?= Html::submitButton(Yii::t('app', 'Sign in'), ['class' => 'btn btn-primary btn-block btn-flat', 'name' => 'login-button']) ?>
            </div>
            <!-- /.col -->
        </div>

        <?php ActiveForm::end(); ?>

        <div class="social-auth-links text-center">
            <p>- OR -</p>
            <a href="#" class="btn btn-block btn-social btn-facebook btn-flat"><i class="fa fa-facebook"></i> Sign in
                using Facebook</a>
            <a href="#" class="btn btn-block btn-social btn-google-plus btn-flat"><i class="fa fa-google-plus"></i> Sign
                in using Google+</a>
        </div>
        <!-- /.social-auth-links -->

        <div class="account-links">
            <a href="<?= Url::to(['user/password-request']) ?>"><?= Yii::t('app', 'I forgot my password') ?></a><br>
            <?php if (!$disableUserRegister): ?>
            <a href="<?= Url::to(['user/register']) ?>" class="text-center"><?= Yii::t('app', 'Register a new account') ?></a>
            <?php endif ?>
        </div>

    </div>
    <!-- /.login-box-body -->
</div><!-- /.login-box -->
