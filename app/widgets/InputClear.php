<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @version $Id$
 * @since 0.1
 */

namespace app\widgets;

use app\helpers\Icon;
use yii\bootstrap5\Button;
use yii\web\JsExpression;

/**
 * InputClear
 *
 * Adds to input button by pressing which input is cleared.
 * 
 * @author skoro
 */
class InputClear extends InputGroup
{
    
    /**
     * @var string
     */
    public $icon = 'glyphicon glyphicon-erase';
    
    /**
     * @inheritdoc
     */
    public $button = true;
    
    /**
     * @var array
     */
    public $buttonOptions = ['class' => 'btn btn-default btn-flat'];
    
    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->addon = Button::widget([
            'label' => Icon::icon($this->icon),
            'options' => $this->buttonOptions,
            'encodeLabel' => false,
            'clientEvents' => [
                'click' => new JsExpression("
                    function (ev) {
                        ev.preventDefault();
                        jQuery('#{$this->inputOptions['id']}').val('');
                    }
                "),
            ],
        ]);
        return parent::run();
    }
    
}
