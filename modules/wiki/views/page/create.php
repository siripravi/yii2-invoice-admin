<?php

use app\widgets\Box;

/** @var $this yii\web\View */
/** @var $editor modules\wiki\forms\Editor */

$this->title = Yii::t('app', 'Create a new page');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Wiki'), 'url' => ['page/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php Box::begin([
    'label' => $this->title,
]) ?>
<?= $this->render('_editor', ['editor' => $editor]) ?>
<?php Box::end() ?>
