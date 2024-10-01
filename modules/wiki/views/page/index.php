<?php

use app\helpers\Icon;
use app\widgets\Box;
use yii\helpers\Html;

/** @var $this yii\web\View */
/** @var $rootPages modules\wiki\models\Wiki[] */

$this->title = Yii::t('app', 'Wiki');
$this->params['breadcrumbs'][] = $this->title;
?>

<?php Box::begin([]) ?>

<div class="btn-group">
    <?php if (Yii::$app->user->can('createWiki')): ?>
    <?= Html::a(Yii::t('app', 'Create'), ['page/create'], ['class' => 'btn btn-flat btn-default']) ?>
    <?php endif ?>
</div>

<?php if (!$rootPages): ?>
    <?= Yii::t('app', 'Page list is empty.') ?>
<?php endif ?>
<ul class="list-unstyled pages">
    <?php foreach ($rootPages as $wiki): ?>
    <li class="wiki-page">
        <?= Html::a(Icon::icon($wiki->getChildren()->count() ? 'fa fa-book' : 'fa fa-file-text') . Html::encode($wiki->title), ['page/view', 'id' => $wiki->id]) ?>
    </li>
    <?php endforeach ?>
</ul>
<?php Box::end() ?>
