<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @since 0.1
 */

namespace app\base\behaviors;

use Yii;
use ReflectionClass;
use yii\base\Behavior;

/**
 * StatusBehavior
 *
 * This behavior extracts STATUS_ constant from a model and makes appropriate
 * labels. For example, model has:
 * ```php
 * class Order extends Model {
 *      const STATUS_ACTIVE = 0;
 *      const STATUS_COMPLETED = 1;
 *      const STATUS_CANCELLED = 2;
 *
 *      public $status;
 * }
 * $order = new Order();
 * $order->getStatusLabels(); // Will returns: 0 => Active, 1 => Completed, ...
 * $order->getStatusLabel(); // Will returns current model status based on
 *                           // 'status' model's attribute.
 * ```
 * 
 *
 * @author skoro
 */
class StatusBehavior extends Behavior
{
    
    /**
     * @var string[] a list of status labels.
     */
    protected $_statuses;
    
    /**
     * @var string
     */
    public $statusAttribute = 'status';
    
    /**
     * @inheritdoc
     */
    public function attach($owner)
    {
        parent::attach($owner);
        $reflection = new ReflectionClass($this->owner);
        $consts = $reflection->getConstants();
        foreach ($consts as $name => $value) {
            if (strpos($name, 'STATUS_') === 0) {
                $this->_statuses[$value] = $this->createLabel($name);
            }
        }
    }
    
    /**
     * Returns model's all status labels.
     * @return array
     */
    public function getStatusLabels()
    {
        return $this->_statuses;
    }
    
    /**
     * Returns model's status field label.
     * @return string
     */
    public function getStatusLabel()
    {
        $status = $this->owner->{$this->statusAttribute};
        return $this->_statuses[$status];
    }
    
    /**
     * Create label from constant name.
     * @param string $const
     * @return string
     */
    protected function createLabel($const)
    {
        $label = str_replace('STATUS_', '', $const);
        $label = ucfirst(strtolower($label));
        return Yii::t('app', $label);
    }
}
