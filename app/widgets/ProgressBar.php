<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @version $Id$
 */

namespace app\widgets;

use yii\base\Widget;
use yii\helpers\Html;

/**
 * ProgressBar
 * 
 * ```php
 * echo ProgressBar::widget([
 *  'total' => 1200,
 *  'value' => 250,
 * ]);
 * ```
 *
 * @author skoro
 */
class ProgressBar extends Widget
{
    
    /**
     * Progress bar styles.
     */
    const STYLE_DANGER = 'danger';
    const STYLE_INFO = 'info';
    const STYLE_PRIMARY = 'primary';
    const STYLE_SUCCESS = 'success';
    const STYLE_WARNING = 'warning';
    
    /**
     * Progress sizes.
     */
    const SIZE_XS = 'progress-xs';
    const SIZE_XXS = 'progress-xxs';
    const SIZE_SM = 'progress-sm';
    
    /**
     * @var integer max progress
     */
    public $total = 100;
    
    /**
     * @var integer current value of progress.
     */
    public $value;
    
    /**
     * @var string progress bar style.
     */
    public $style = self::STYLE_PRIMARY;
    
    /**
     * @var array
     */
    public $options = [];
    
    /**
     * @var string progress bar height.
     */
    public $size = '';
    
    /**
     * @var boolean render a vertical progress bar.
     */
    public $vertical = false;
    
    /**
     * @inheritdoc
     */
    public function run()
    {
        $options = $this->options;
        Html::addCssClass($options, 'progress');
        
        if ($this->size) {
            Html::addCssClass($options, $this->size);
        }
        
        if ($this->vertical) {
            Html::addCssClass($options, 'vertical');
        }
        
        $bar = $this->renderBar();
        
        return Html::tag('div', $bar, $options);
    }
    
    /**
     * Renders and calculate percent value.
     * @return string
     */
    protected function renderBar()
    {
        $options = [
            'class' => 'progress-bar',
            'role' => 'progressbar',
            'aria-valuenow' => $this->value,
            'aria-valuemin' => 0,
            'aria-valuemax' => $this->total,
        ];
        if ($this->style) {
            Html::addCssClass($options, 'progress-bar-' . $this->style);
        }
        
        if ($this->value === 0 || $this->value < 0) {
            $value = 0;
        }
        elseif ($this->value > 0 && $this->total > 0) {
            $value = (int)(($this->value * 100) / $this->total);
        }
        else {
            $value = $this->total == 0 ? 100 :
                ($this->total > 100 ? 100 : $this->total);
        }
        
        $percent = $value . '%';
        if ($this->vertical) {
            Html::addCssStyle($options, ['height' => $percent]);
        } else {
            Html::addCssStyle($options, ['width' => $percent]);
        }
        
        $content = Html::tag('span', $percent, ['class' => 'sr-only']);
        return Html::tag('div', $content, $options);
    }
}
