<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @since 0.2
 */

namespace modules\wiki\forms;

use modules\wiki\models\Wiki;
use Yii;
use yii\base\InvalidValueException;
use yii\base\Model;
use yii\db\Exception as DbException;

/**
 * Description of DeletePage
 *
 * @author skoro
 */
class DeleteWiki extends Model
{
    
    /**
     * Delete modes of how to delete children.
     */
    const DELETE_CHILDREN = 1; // delete all children
    const DELETE_MOVEUP = 2; // move children to one level up
    const DELETE_MOVEID = 3; // move to specific page
    
    /**
     * @var integer
     */
    public $mode;
    
    /**
     * @var integer
     */
    public $parentId;
    
    /**
     * @var Wiki
     */
    protected $_wiki;
    
    public function __construct(Wiki $wiki, $config = [])
    {
        $this->_wiki = $wiki;
        parent::__construct($config);
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['mode', 'required', 'message' => 'Select how to delete pages.'],
            ['mode', 'integer'],
            ['mode', 'in', 'range' => [
                self::DELETE_CHILDREN, self::DELETE_MOVEUP, self::DELETE_MOVEID,
            ]],
            ['mode', 'validateMode'],
            
            ['parentId', 'integer'],
            ['parentId', 'exist',
                'when' => function (DeleteWiki $model) {
                    return $model->mode == self::DELETE_MOVEID;
                },
                'targetClass' => Wiki::className(),
                'targetAttribute' => ['parentId' => 'id'],
            ],
        ];
    }
    
    /**
     * Validate 'mode' parameter.
     */
    public function validateMode($attribute, $params = [])
    {
        $value = $this->$attribute;
        if ($value == self::DELETE_MOVEID && empty($this->parentId)) {
            $this->addError('parentId', Yii::t('app', 'Select base page.'));
        }
    }
    
    /**
     * @return Wiki
     */
    public function getWiki()
    {
        return $this->_wiki;
    }
    
    /**
     * @return boolean
     */
    public function isChildrenExists()
    {
        return (bool) $this->_wiki->getChildren()->count();
    }
    
    /**
     * @return Wiki[]
     */
    public function getChildren()
    {
        return $this->_wiki->children;
    }
    
    /**
     * Returns list of available delete choices.
     * @return array
     */
    public function getChoices()
    {
        $choices = [
            self::DELETE_CHILDREN => Yii::t('app', 'Delete also'),
        ];
        if ($parent = $this->_wiki->parent) {
            $choices[self::DELETE_MOVEUP] = Yii::t('app', 'Move these pages to "{title}"', [
                'title' => $parent->title,
            ]);
        }
        $choices[self::DELETE_MOVEID] = Yii::t('app', 'Move to selected:');
        return $choices;
    }
    
    /**
     * Delete page.
     * @return boolean
     */
    public function delete()
    {
        if (!$this->validate()) {
            return false;
        }
        
        $result = true;
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        
        try {
        
            switch ($this->mode) {
                case self::DELETE_CHILDREN:
                    $this->deleteAllChildren();
                    $this->parentId = null;
                    break;
                
                case self::DELETE_MOVEUP:
                    if (!($parent = $this->_wiki->parent)) {
                        throw new InvalidValueException('Wiki has not parent.');
                    }
                    $this->parentId = $parent->id;
                    break;
                
                case self::DELETE_MOVEID:
                    break;
            }
            
            // Reparent wiki children.
            if ($this->parentId) {
                $db->createCommand()
                    ->update(Wiki::tableName(), [
                        'parent_id' => $this->parentId,
                    ], [
                        'parent_id' => $this->_wiki->id,
                    ])
                    ->execute();
            }
            
            if ($this->_wiki->delete() === false) {
                throw new DbException('Cannot delete wiki');
            }
            
            $transaction->commit();
            
        } catch (\Exception $ex) {
            $transaction->rollBack();
            $result = false;
        }
        
        return $result;
    }
    
    protected function deleteAllChildren()
    {
        $children = $this->_wiki->getChildrenAll();
        
        foreach ($children as $child) {
            if ($child->delete() === false) {
                throw new DbException('Cannot delete child wiki id: ' . $child->id);
            }
        }
    }
}
