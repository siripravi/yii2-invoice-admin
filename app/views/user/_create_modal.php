<?php

use app\widgets\ActiveForm;
use app\widgets\Check;

/** @var $register app\forms\user\Register */
?>

<?php $form = ActiveForm::begin([
    'pjax' => true,
    'enableAjaxValidation' => true,
]) ?>
    <?= $form->field($register, 'name') ?>
    <?= $form->field($register, 'email') ?>
    <?= $form->field($register, 'password')->passwordInput() ?>
    <?= $form->field($register, 'password_repeat')->passwordInput() ?>
    <?= $form->field($register, 'sendmail')
            ->widget(Check::className())
            ->label(false) ?>
<?php ActiveForm::endWithActions([
    'cancel' => [
        'options' => ['data-dismiss' => 'modal'],
    ],
]) ?>