<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @since 0.2
 */

namespace app\base;

use Closure;

/**
 * This trait is helper for generate model url.
 *
 * @author skoro
 */
trait ModelUrlTrait
{
    /**
     * @var string|array|Closure delete url.
     */
    public $url;
    
    /**
     * Returns model's url.
     * @param mixed $model
     * @param string|array $default default url
     * @return array|string
     */
    protected function getModelUrl($model, $default)
    {
        if ($this->url instanceof Closure) {
            $url = call_user_func($this->url, $model);
        }
        else {
            $url = empty($this->url) ? $default : $this->url;
            if (is_array($url) && isset($model->id)) {
                $url['id'] = $model->id;
            }
        }
        
        return $url;
    }
}
