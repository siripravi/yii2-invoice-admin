<?php

use app\widgets\ActiveForm;
use app\widgets\Check;
use app\widgets\Select2;
use yii\helpers\Html;

/** @var $this yii\web\View */
/** @var $delete modules\wiki\forms\DeleteWiki */
/** @var $form app\widgets\ActiveForm */

$this->title = $delete->getWiki()->title;
?>
<div class="callout callout-danger">
    <h4><?= Yii::t('app', 'Are you sure to delete page ?') ?></h4>
    <p><?= Yii::t('app', 'This operation cannot be undo.') ?></p>
</div>

<?php $form = ActiveForm::begin() ?>

<?php if ($delete->isChildrenExists()): ?>
    <p class="lead"><?= Yii::t('app', 'These pages also affected: ') ?></p>
    <ul>
    <?php foreach ($delete->getChildren() as $child): ?>
        <li><?= Html::a(e($child->title), ['page/view', 'id' => $child->id]) ?></li>
    <?php endforeach ?>
    </ul>
    <p class="lead"><?= Yii::t('app', 'Please select what to do with these pages: ') ?></p>
    <?= $form->field($delete, 'mode')->widget(Check::className(), [
        'type' => Check::TYPE_RADIO,
        'options' => ['class' => 'checkbox-list-vert'],
        'items' => $delete->getChoices(),
    ]) ?>
    <?= $form->field($delete, 'parentId')->widget(Select2::className(), [
        'hideSearch' => false,
        'remote' => ['wiki-suggest', 'ign' => $delete->getWiki()->id],
    ])->label(false) ?>
    <hr>
<?php endif ?>

<?php ActiveForm::endWithActions([
    'save' => [
        'label' => Yii::t('app', 'DELETE'),
        'options' => ['class' => 'btn btn-flat bg-red'],
    ],
    'cancel' => [
        'url' => ['page/view', 'id' => $delete->getWiki()->id],
    ],
]) ?>
