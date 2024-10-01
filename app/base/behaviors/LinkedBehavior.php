<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @since 0.1
 */

namespace app\base\behaviors;

use Yii;
use yii\base\Behavior;
use yii\db\Query;

/**
 * LinkedBehavior
 * 
 * This behavior links two tables via helper table (many-to-many relation).
 * 
 * @author skoro
 */
class LinkedBehavior extends Behavior
{
    
    /**
     * @var string helper table name.
     */
    protected $_table;
    
    /**
     * @var string linked model attribute in helper table.
     */
    protected $_linkAttribute;
    
    /**
     * @var string which module to link to owner.
     */
    protected $_linkModel;
    
    /**
     * @var string owner attribute in helper table.
     */
    protected $_sourceAttribute;
    
    /**
     * @var string
     */
    protected $_idAttribute = 'id';
    
    /**
     * Relation to linked models.
     * @return \yii\db\ActiveQuery
     */
    protected function getRelation()
    {
        return $this->owner->hasMany($this->_linkModel, [$this->_idAttribute => $this->_linkAttribute])
                ->viaTable($this->_table, [$this->_sourceAttribute => $this->_idAttribute]);
    }
    
    /**
     * Link model with owner.
     * @param Model $model
     * @return boolean
     * @throws InvalidCallException
     */
    protected function addLinked($model)
    {
        if ($model->isNewRecord) {
            throw new InvalidCallException('Model must be saved before adding.');
        }
        if ($this->owner->isNewRecord) {
            throw new InvalidCallException('Save the model before linking with other.');
        }
        $params = [
            $this->_sourceAttribute => $this->owner->id,
            $this->_linkAttribute => $model->id,
        ];
        $exists = (new Query())->from($this->_table)
                ->where($params)
                ->exists();
        if ($exists) {
            return false;
        }
        return Yii::$app->db->createCommand()
                ->insert($this->_table, $params)
                ->execute();
        
    }
    
    /**
     * Break relation between linking and owner.
     * @param Model $model linked model
     * @return integer
     */
    protected function removeLinked($model)
    {
        return Yii::$app->db->createCommand()
                ->delete($this->_table, [
                    $this->_sourceAttribute => $this->owner->id,
                    $this->_linkAttribute => $model->id,
                ])
                ->execute();
    }
    
}
