<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @since 0.1
 */

namespace app\base;

/**
 * Parent of controller actions.
 *
 * @author skoro
 */
class Action extends \yii\base\Action
{
    /**
     * @var string view layout
     */
    public $layout;
    
    /**
     * @var string action view
     */
    public $view;
    
    /**
     * Render action.
     * @param array $params view parameters
     * @return string
     */
    protected function render(array $params = [])
    {
        if ($this->layout) {
            $this->controller->layout = $this->layout;
        }
        return $this->controller->render($this->view, $params);
    }
}
