<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @since 0.2
 */

namespace app\base\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;

/**
 * SerializableBehavior
 *
 * @author skoro
 */
class SerializableBehavior extends Behavior
{
    
    /**
     * @var array list of attribute name to be serialize.
     */
    public $attributes = [];
    
    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_FIND => 'onAfterFind',
            ActiveRecord::EVENT_BEFORE_INSERT => 'onBeforeSave',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'onBeforeSave',
            ActiveRecord::EVENT_AFTER_INSERT => 'onAfterSave',
            ActiveRecord::EVENT_AFTER_UPDATE => 'onAfterSave',
        ];
    }
    
    public function onAfterFind()
    {
        $this->unserializeAttributes();
    }
    
    public function onBeforeSave()
    {
        $this->serializeAttributes();
    }
    
    public function onAfterSave()
    {
        $this->unserializeAttributes();
    }
    
    protected function serializeAttributes()
    {
        foreach ($this->attributes as $attribute) {
            $this->owner->$attribute = serialize($this->owner->$attribute);
        }
    }
    
    protected function unserializeAttributes()
    {
        foreach ($this->attributes as $attribute) {
            $this->owner->$attribute = unserialize($this->owner->$attribute);
            $this->owner->setOldAttribute($attribute, $this->owner->$attribute);
        }
    }
}
