<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @version $Id$
 */

namespace app\widgets;

use yii\bootstrap5\Widget;
use yii\helpers\Html;

/**
 * Extension to ProgressBar widget which add label and number.
 *
 * @author skoro
 */
class ProgressBarGroup extends Widget
{
    /**
     * @var string progress bar label.
     */
    public $label = '';
    
    /**
     * @var boolean
     */
    public $encodeLabel = true;
    
    /**
     * @var array
     */
    public $options = [];
    
    /**
     * @var array
     */
    public $labelOptions = [];
    
    /**
     * @var array
     */
    public $numberOptions = [];
    
    /**
     * @var integer current value of progress.
     */
    public $value;
    
    /**
     * @var integer max progress
     */
    public $total = 100;
    
    /**
     * @var array progress bar options.
     */
    public $progress = [];

    /**
     * @inheritdoc
     */
    public function run()
    {
        $options = $this->options;
        Html::addCssClass($options, 'progress-group');
        
        $label = $this->renderLabel();
        $number = $this->renderNumber();
        
        $this->progress['value'] = $this->value;
        $this->progress['total'] = $this->total;
        $progress = ProgressBar::widget($this->progress);
        
        return Html::tag('div', $label . "\n" . $number . "\n" . $progress, $options);
    }
    
    /**
     * Renders progress bar label.
     * @return string
     */
    protected function renderLabel()
    {
        $options = $this->labelOptions;
        Html::addCssClass($options, 'progress-text');
        $label = $this->encodeLabel ? Html::encode($this->label) : $this->label;
        return Html::tag('span', $label, $options);
    }
    
    /**
     * Renders progress number.
     * @return string
     */
    protected function renderNumber()
    {
        $options = $this->numberOptions;
        Html::addCssClass($options, 'progress-number');
        $number = '<b>' . $this->value . '</b>/' . $this->total;
        return Html::tag('span', $number, $options);
    }
}
