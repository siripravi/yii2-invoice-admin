<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @version $Id$
 */

namespace app\widgets;

use app\assets\CheckAsset;
use yii\base\InvalidValueException;
use yii\bootstrap5\InputWidget;
use yii\helpers\Html;

/**
 * iCheck plugin
 *
 * @link https://github.com/fronteed/icheck
 * @author skoro
 */
class Check extends InputWidget
{
    
    /**
     * Control type.
     */
    const TYPE_CHECKBOX = 'checkbox';
    const TYPE_RADIO = 'radio';
    
    /**
     * Predefined styles.
     */
    const STYLE_MINIMAL = 'minimal';
    const STYLE_FLAT = 'flat';
    const STYLE_LINE = 'line';
    const STYLE_SQUARE = 'square';
    
    /**
     * Style colors.
     */
    const COLOR_RED = 'red';
    const COLOR_GREEN = 'green';
    const COLOR_BLUE = 'blue';
    const COLOR_AERO = 'aero';
    const COLOR_GREY = 'grey';
    const COLOR_ORANGE = 'orange';
    const COLOR_YELLOW = 'yellow';
    const COLOR_PINK = 'pink';
    const COLOR_PURPLE = 'purple';
    
    /**
     * @var string control type.
     */
    public $type = self::TYPE_CHECKBOX;
    
    /**
     * @var string control style.
     */
    public $style = self::STYLE_FLAT;
    
    /**
     * @var string style color.
     */
    public $color = self::COLOR_GREEN;
    
    /**
     * @var string
     */
    public $label = '';
    
    /**
     * @var array checkbox list
     */
    public $items = [];
    
    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->registerPlugin('iCheck');
        
        if ($this->label) {
            $this->options['label'] = $this->label;
        }
        
        if ($this->items) {
            if (!($this->items = array_filter($this->items))) {
                throw new InvalidValueException('Empty items list.');
            }
        }
        
        if ($this->hasModel()) {
            if ($this->items) {
                $method = $this->type === static::TYPE_CHECKBOX ? 'activeCheckboxList' : 'activeRadioList';
                $input = Html::$method($this->model, $this->attribute, $this->items, $this->options);
            } else {
                $input = Html::activeCheckbox($this->model, $this->attribute, $this->options);
            }
        } elseif ($this->items) {
            $method = $this->type === static::TYPE_CHECKBOX ? 'checkboxList' : 'radioList';
            $input = Html::$method($this->name, (bool) $this->value, $this->items, $this->options);
        } else {
            $input = Html::checkbox($this->name, (bool) $this->value, $this->options);
        }
        
        return $input;
    }
    
    /**
     * @inheritdoc
     */
    protected function registerPlugin($name)
    {
        $asset = CheckAsset::register($this->getView());
        $asset->style = $this->style;
        
        $class = $this->type === static::TYPE_CHECKBOX ? 'checkboxClass' : 'radioClass';
        $this->clientOptions[$class] = static::createStyleName($this->type, $this->style, $this->color);
        
        parent::registerPlugin($name);
    }
    
    /**
     * Create compound checkbox style name.
     * @param string $type
     * @param string $style
     * @param string $color
     * @return string
     */
    public static function createStyleName($type, $style, $color)
    {
        return 'i' . $type . '_' . $style . '-' . $color;
    }
}
