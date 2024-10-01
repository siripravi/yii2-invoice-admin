<?php

use app\helpers\Icon;
use app\widgets\ActiveForm;
use yii\helpers\Html;

/** @var $this yii\web\View */
/** @var $title string Confirmation title. */
/** @var $message string Confirmation message. */
/** @var $encodeMessage boolean Whether encode message. */
/** @var $button array Action button. */
/** @var $params array Form additional parameters. */
/** @var $actionUrl string|array Form POST action. */
/** @var $cancelUrl string|array Cancel url. */
/** @var $icon string Icon css class. */
/** @var $background string Background css class. */

$this->title = $title;
?>

<div class="small-box <?= $background ?>">
    <div class="inner">
        <h3><?= $this->title ?></h3>
        <p><?= $encodeMessage ? Html::encode($message) : $message ?>
        </p>
    </div>
    <div class="icon">
        <?= Icon::icon($icon) ?>
    </div>
</div>

<?php $form = ActiveForm::begin([
    'action' => $actionUrl,
]) ?>

    <?php foreach ($params as $param => $value): ?>
        <?php if (is_array($value)):
            foreach ($value as $item):
                echo Html::hiddenInput($param . '[]', $item);
            endforeach;
        else:
            echo Html::hiddenInput($param, $value);
        endif ?>
    <?php endforeach ?>

<?php ActiveForm::endWithActions([
    'save' => $button,
    'cancel' => [
        'url' => $cancelUrl,
    ],
]) ?>
