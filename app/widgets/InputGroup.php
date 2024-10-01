<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @version $Id$
 * @since 0.1
 */

namespace app\widgets;

use yii\bootstrap5\InputWidget;
use yii\helpers\Html;

/**
 * InputGroup
 * 
 * For example, put button labeled Go! to end of text input:
 * ```php
 * echo InputGroup::widget([
 *   'model' => $model,
 *   'attribute' => 'name',
 *   'addon' => Html::a('Go!', ['go'], ['class' => 'btn btn-default']),
 *   'button' => true,
 * ]);
 * ```
 * 
 * @link http://getbootstrap.com/components/#input-groups
 * @author skoro
 */
class InputGroup extends InputWidget
{
    
    const SIZE_LARGE = 'input-group-lg';
    const SIZE_SMALL = 'input-group-sm';
    
    /**
     * @var string input group size.
     */
    public $size = '';
    
    /**
     * @var boolean put addon on left or right.
     */
    public $left = false;
    
    /**
     * @var string addon content.
     */
    public $addon = '';
    
    /**
     * @var boolean treat addon as a button.
     */
    public $button = false;
    
    /**
     * @var array
     */
    public $addonOptions = [];
    
    /**
     * @var array
     */
    public $inputOptions = ['class' => 'form-control'];
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->inputOptions['id'] = $this->options['id'];
        $this->options['id'] = $this->getId();
    }
    
    /**
     * @inheritdoc
     */
    public function run()
    {
        if ($this->hasModel()) {
            $input = Html::activeTextInput($this->model, $this->attribute, $this->inputOptions);
        } else {
            $input = Html::textInput($this->name, $this->value, $this->inputOptions);
        }
        
        $addon = $this->renderAddon();
        
        Html::addCssClass($this->options, 'input-group');
        Html::addCssClass($this->options, $this->size);
        
        $content = $this->left ? $addon . $input : $input . $addon;
        
        return Html::tag('div', $content, $this->options);
    }
    
    /**
     * Renders addon container.
     * @return string
     */
    protected function renderAddon()
    {
        if ($this->button) {
            $tag = 'div';
            Html::addCssClass($this->addonOptions, 'input-group-btn');
        }
        else {
            $tag = 'span';
            Html::addCssClass($this->addonOptions, 'input-group-addon');
        }
        return Html::tag($tag, $this->addon, $this->addonOptions);
    }
}
