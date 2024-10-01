<?php

use app\widgets\Tabs;

/** @var $this yii\web\View */
/** @var $editor modules\wiki\forms\Editor */
/** @var $historyProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Update page');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Wiki'), 'url' => ['page/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= Tabs::widget([
    'items' => [
        [
            'label' => Yii::t('app', 'Editor'),
            'content' => $this->render('_editor', ['editor' => $editor]),
        ],
        [
            'label' => Yii::t('app', 'History'),
            'content' => $this->render('_history', [
                'historyProvider' => $historyProvider,
            ]),
            'visible' => Yii::$app->user->can('viewWikiHistory'),
        ],
    ],
]) ?>
