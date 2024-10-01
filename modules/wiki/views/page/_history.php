<?php

use app\widgets\Pjax;
use app\widgets\Timeline;
use modules\wiki\assets\DiffAsset;
use modules\wiki\helpers\DiffHelper;
use yii\helpers\Html;

/** @var $this yii\web\View */
/** @var $historyProvider yii\data\ActiveDataProvider */
DiffAsset::register($this);
?>

<?php Pjax::begin() ?>
<?= Timeline::widget([
    'dataProvider' => $historyProvider,
    'dateValue' => function ($model) {
        return Yii::$app->formatter->asDate($model->created_at);
    },
    'timeView' => function ($model) {
        return Yii::$app->formatter->asRelativeTime($model->created_at);
    },
    'itemHeaderView' => function ($model) {
        return Yii::$app->formatter->asUserlink($model->user) . ' ' . Html::tag('span', Html::encode($model->summary), ['class' => 'text-muted summary-change']);
    },
    'itemView' => function ($model) {
        return Html::tag('pre', DiffHelper::diff($model));
    },
    'itemFooterView' => function ($model) {
        return Html::a(Yii::t('app', 'Edit'), ['page/update', 'id' => $model->wiki_id, 'rev' => $model->id], ['class' => 'btn btn-default btn-flat btn-xs', 'data-pjax' => 0]);
    },
]) ?>
<?php Pjax::end() ?>
