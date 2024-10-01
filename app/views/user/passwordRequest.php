<?php

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\Param;

/** @var $this yii\web\View */
/** @var $form yii\bootstrap5\ActiveForm */
/** @var $model app\forms\user\PasswordRequest */

$this->title = Yii::t('app', 'Request password');
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
        <p class="login-box-msg password-request"><?= Yii::t('app', 'If you have forgotten your password, you can reset it here. When you fill in your registered email address, you will be sent instructions on how to reset your password.') ?></p>

        <?php $form = ActiveForm::begin(['id' => 'login-form', 'enableClientValidation' => false]); ?>

        <?= $form
            ->field($model, 'email', $fieldOptions('envelope'))
            ->label(false)
            ->textInput(['placeholder' => $model->getAttributeLabel('email')]) ?>

        <div class="row">
            <div class="col-xs-4">
                <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary btn-block btn-flat', 'name' => 'submit-button']) ?>
            </div>
            <!-- /.col -->
        </div>

        <?php ActiveForm::end(); ?>
        
    </div>
    <!-- /.login-box-body -->
</div><!-- /.login-box -->
