<?php

use app\widgets\ActiveForm;
use app\widgets\Check;
use yii\helpers\ArrayHelper;

/** @var $this yii\web\View */
/** @var $form app\widgets\ActiveForm */
/** @var $model app\forms\Profile */
?>
<?php $form = ActiveForm::begin([
    'id' => 'user-profile-admin-form',
    'layout' => 'horizontal',
    'fieldConfig' => [
        'horizontalCssClasses' => [
            'label' => 'col-sm-2',
            'wrapper' => 'col-sm-10',
        ],
    ],
    'action' => ['profile', 'id' => $model->getUser()->id, 'tab' => 'admin'],
]) ?>

    <?= $form->field($model, 'roles')->widget(Check::className(), [
        'items' => ArrayHelper::map(Yii::$app->authManager->getRoles(), 'name', 'name'),
        'options' => ['class' => 'checkbox-list-vert'],
    ]) ?>

    <?= $form->field($model, 'status')->widget(Check::className(), [
        'type' => Check::TYPE_RADIO,
        'items' => $model->getUser()->getStatusLabels(),
        'options' => ['class' => 'checkbox-list-vert'],
    ]) ?>

<?php ActiveForm::endWithActions([
    'cancel' => false,
]) ?>
