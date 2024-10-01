<?php

use app\widgets\Box;
use app\widgets\Check;
use app\widgets\ItemList;
use app\widgets\ProgressBar;
use app\widgets\ProgressBarGroup;
use app\widgets\Select2;
use app\widgets\Tabs;
use app\widgets\GridView;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;

/** @var $this yii\web\View */

$this->title = 'Demo page';
$this->params['breadcrumbs'][] = $this->title;
$dataProvider = new ArrayDataProvider([
    'allModels' => [
        ['id' => 1, 'task' => 'Update software', 'progress' => 20, 'label' => '55'],
        ['id' => 2, 'task' => 'Clean database', 'progress' => 40, 'label' => '15'],
        ['id' => 3, 'task' => 'Cron job running', 'progress' => 80, 'label' => '84'],
        ['id' => 4, 'task' => 'Fix and squish bugs', 'progress' => 15, 'label' => '18'],
    ],
]);
?>
<div class="row demo-page">
    
    <div class="col-md-6">
        <?php Box::begin([
            'box' => Box::BOX_DEFAULT,
            'label' => 'Table',
        ]) ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    'id',
                    'task',
                    [
                        'attribute' => 'progress',
                        'format' => ['progressBar', [
                            'size' => ProgressBar::SIZE_SM,
                            'style' => ProgressBar::STYLE_INFO,
                        ]],
                    ],
                    [
                        'attribute' => 'label',
                        'value' => function ($model) {
                            $color = '';
                            if ($model['label'] < 20) {
                                $color = 'bg-red';
                            } 
                            if ($model['label'] > 50) {
                                $color = 'bg-yellow';
                            } 
                            if ($model['label'] > 80) {
                                $color = 'bg-green';
                            }
                            return Html::tag('span', $model['label'] . '%', ['class' => 'badge ' . $color]);
                        },
                        'format' => 'html',
                    ],
                ],
            ]) ?>
        <?php Box::end() ?>
        
        <?= Tabs::widget([
            'items' => [
                [
                    'label' => 'Tab 1',
                    'content' =>
                        '<div>' .
                    /*    Check::widget([
                            'name' => 'chk1',
                            'label' => 'Enable report',
                        ]) . '</div><div>' .
                        Check::widget([
                            'name' => 'chk2',
                            'label' => 'Send email',
                            'value'  => true,
                        ]) .  */
                         '</div>',
                ],
                [
                    'label' => 'Tab 2',
                    'content' => 
                        ProgressBar::widget([
                            'style' => ProgressBar::STYLE_SUCCESS,
                            'value' => 30,
                            'vertical' => true,
                        ]) . 
                        ProgressBar::widget([
                            'style' => ProgressBar::STYLE_WARNING,
                            'value' => 60,
                            'vertical' => true,
                        ]) .
                        ProgressBarGroup::widget([
                            'value' => 82,
                            'label' => 'Processing',
                        ]),
                ],
                [
                    'label' => 'Tab 3',
                    'content' => 'Content 3...',
                ],
                [
                    'label' => 'Dropdown',
                    'items' => [
                        [
                            'label' => 'Label',
                            'content' => '....',
                        ],
                        [
                            'label' => 'Label',
                            'content' => '....',
                        ],
                    ],
                ],
            ],
        ]) ?>
        
    </div>
    
    <div class="col-md-6">
        <?php Box::begin([
            'box' => Box::BOX_PRIMARY,
            'label' => 'Box 1',
            'collapsable' => true,
            'solid' => true,
        ]) ?>
            The body of the box. The body of the box. The body of the box.
            <?php app\widgets\Modal::begin([
                'toggleButton' => [
                    'label' => 'Modal with remote content.',
                    'class' => 'btn btn-flat btn-success',
                ],
                'header' => '<b>Remote content</b>',
                'remote' => ['modal-remote'],
            ]) ?>
                <p class="text-center text-capitalize">Loading...</p>
            <?php app\widgets\Modal::end() ?>
        <?php Box::end() ?>
        
        <?php Box::begin([
            'box' => Box::BOX_WARNING,
            'label' => 'Box 2',
            'removable' => true,
            'actions' => [
                [
                    'label' => 'Configure',
                    'options' => ['class' => 'configure'],
                ],
            ],
        ]) ?>
            The body of the box. The body of the box. The body of the box.
        <?php Box::end() ?>
            
        <?php Box::begin([
            'box' => Box::BOX_SUCCESS,
            'label' => 'Select2 widget',
        ]) ?>
            <?= Select2::widget([
                'name' => 'demo-select2',
                'items' => [
                    1 => 'Zoo',
                    2 => 'Park',
                    3 => 'Cinema',
                ],
            ]) ?>
            <hr>
            <?= Select2::widget([
                'name' => 'demo-select2',
                'hideSearch' => false,
                'items' => [
                    1 => 'Zoo',
                    2 => 'Park',
                    3 => 'Cinema',
                    4 => 'Shop',
                    5 => 'Market',
                ],
            ]) ?>
        <?php Box::end() ?>
            
        <?php Box::begin([
            'label' => 'Item list',
            'box' => Box::BOX_DANGER,
        ]) ?>
            <?= ItemList::widget([
                'items' => [
                    [
                        'title' => 'Created by',
                        'value' => 'user',
                    ],
                    [
                        'title' => 'Date',
                        'value' => '2016.08.09',
                    ],
                    [
                        'title' => 'Status',
                        'value' => 'Enabled',
                    ],
                ],
            ]) ?>
        <?php Box::end() ?>
    </div>
    
    <div class="col-md-12">
    </div>
    
</div> <!-- /.demo-page -->
