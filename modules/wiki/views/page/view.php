<?php

use app\helpers\Icon;
use app\widgets\Box;
use yii\bootstrap\ButtonDropdown;
use yii\helpers\Html;

/** @var $this yii\web\View */
/** @var $wiki modules\wiki\models\Wiki */

$this->title = $wiki->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Wiki'), 'url' => ['page/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php Box::begin([]) ?>
    <div class="btn-group">
        <?= Html::a(Icon::icon('fa fa-home'), ['page/index'], ['class' => 'btn btn-flat btn-default']) ?>
        <?php if (!empty($wiki->parent_id)): ?>
        <?= Html::a(Icon::icon('fa fa-chevron-left'), ['page/view', 'id' => $wiki->parent_id], ['class' => 'btn btn-flat btn-default']) ?>
        <?php endif ?>
        <?php if ($wiki->getChildren()->count()): ?>
            <?= ButtonDropdown::widget([
                'label' => Yii::t('app', 'Pages'),
                'tagName' => 'a',
                'options' => ['class' => ['btn btn-flat btn-default']],
                'dropdown' => [
                    'items' => array_map(function ($child) {
                        return [
                            'label' => $child->title,
                            'url' => ['page/view', 'id' => $child->id],
                        ];
                    }, $wiki->children),
                ],
            ]) ?>
        <?php endif ?>
        <?= ButtonDropdown::widget([
                'label' => Yii::t('app', 'Actions'),
                'tagName' => 'a',
                'options' => ['class' => ['btn btn-flat btn-default']],
                'dropdown' => [
                    'items' => [
                        [
                            'label' => Yii::t('app', 'Edit'),
                            'url' => ['page/update', 'id' => $wiki->id],
                            'visible' => Yii::$app->user->can('updateWiki', ['wiki' => $wiki]),
                        ],
                        [
                            'label' => Yii::t('app', 'Create child page'),
                            'url' => ['page/create', 'id' => $wiki->id],
                            'visible' => Yii::$app->user->can('createWiki'),
                        ],
                        [
                            'label' => Yii::t('app', 'View raw'),
                            'url' => ['page/raw', 'id' => $wiki->id],
                            'visible' => Yii::$app->user->can('viewWiki'),
                        ],
                        [
                            'label' => Yii::t('app', 'Delete'),
                            'url' => ['page/delete', 'id' => $wiki->id],
                            'visible' => Yii::$app->user->can('deleteWiki', ['wiki' => $wiki]),
                        ],
                    ],
                ],
            ]) ?>
    </div>
    <div class="wiki-summary">
        <span class="summary-info text-muted text-sm">
            <?= Yii::t('app', 'Added by {creator}, last edit by {editor} {time}', [
                'creator' => Yii::$app->formatter->asUserlink($wiki->user),
                'editor' => Yii::$app->formatter->asUserlink($wiki->historyLatest->user),
                'time' => Yii::$app->formatter->asRelativeTime($wiki->historyLatest->created_at),
            ]) ?>
        </span>
    </div>
    <?= Yii::$app->formatter->asMarkdown($wiki->historyLatest->content) ?>
<?php Box::end() ?>

<?php if ($wiki->getChildren()->count()): ?>
<?php Box::begin([
    'label' => Yii::t('app', 'Child pages'),
]) ?>
<ul class="list-unstyled">
    <?php foreach ($wiki->children as $child): ?>
    <li><?= Html::a(Icon::icon('fa fa-file-text', Html::encode($child->title)), ['page/view', 'id' => $child->id]) ?></li>
    <?php endforeach ?>
</ul>
<?php Box::end() ?>
<?php endif ?>
