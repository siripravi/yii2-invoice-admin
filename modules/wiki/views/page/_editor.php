<?php

use app\widgets\ActiveForm;
use modules\wiki\widgets\MarkdownEditor;

/** @var $this yii\web\View */
/** @var $editor modules\wiki\forms\Editor */
/** @var $form app\widgets\ActiveForm */
/** @var $wiki modules\wiki\models\Wiki */
/** @var $cancelUrl array */

$wiki = $editor->getWiki();
if ($wiki->id) {
    $cancelUrl = ['page/view', 'id' => $wiki->id];
} elseif ($wiki->parent_id) {
    $cancelUrl = ['page/view', 'id' => $wiki->parent_id];
} else {
    $cancelUrl = ['page/index'];
}
?>

<?php $form = ActiveForm::begin() ?>

    <?= $form->field($editor, 'title') ?>
    <?= $form->field($editor, 'content')->widget(MarkdownEditor::className(), [
        'previewUrl' => ['page/markdown-preview'],
    ]) ?>
    <?= $form->field($editor, 'summary')->textInput([
        'placeholder' => Yii::t('app', 'What did you change ?'),
    ]) ?>

<?php ActiveForm::endWithActions([
    'cancel' => [
        'url' => $cancelUrl,
    ],
]) ?>
