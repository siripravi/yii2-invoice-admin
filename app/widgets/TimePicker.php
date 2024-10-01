<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @version $Id$
 */

namespace app\widgets;

use yii\helpers\Html;
use yii\bootstrap5\InputWidget;
use app\assets\TimePickerAsset;

/**
 * AdminLTE TimePicker widget.
 *
 * @author skoro
 */
class TimePicker extends InputWidget
{
    
    const MODE_24H = '24h';
    const MODE_12H = '12h';
    
    /**
     * @var string
     */
    public $mode = self::MODE_24H;
    
    /**
     * @var array
     */
    public $containerOptions = [];
    
    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->registerScript();
        
        $options = $this->options;
        Html::addCssClass($options, 'form-control');
        
        if ($this->hasModel()) {
            $input = Html::activeTextInput($this->model, $this->attribute, $options);
        } else {
            $input = Html::textInput($this->name, $this->value, $options);
        }
        
        $input .= '<span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>';
        
        $containerOptions = $this->containerOptions;
        $containerOptions['id'] = $this->getId();
        Html::addCssClass($containerOptions, 'input-group bootstrap-timepicker timepicker');
        
        return Html::tag('div', $input, $containerOptions);
    }
    
    /**
     * Include assets and enable plugin.
     */
    protected function registerScript()
    {
        if ($this->mode === static::MODE_24H) {
            $this->clientOptions['showMeridian'] = false;
        }
        
        if ($this->hasModel() || $this->value) {
            $this->clientOptions['defaultTime'] = false;
        }
        
        TimePickerAsset::register($this->getView());
        
        $this->registerPlugin('timepicker');
    }
}
