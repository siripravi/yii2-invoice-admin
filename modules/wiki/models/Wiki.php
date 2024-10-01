<?php

namespace modules\wiki\models;

use app\models\User;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "wiki".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $title
 * @property string $slug
 * @property integer $parent_id
 * @property integer $created_at
 * 
 * @property User $user
 * @property History $history
 * @property Wiki parent
 * @property Wiki children
 */
class Wiki extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%wiki}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'user_id',
                'updatedByAttribute' => false,
            ],
            [
                'class' => \yii\behaviors\TimestampBehavior::className(),
                'updatedAtAttribute' => false,
            ],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['user_id', 'integer'],
            ['user_id', 'exist',
                'when' => function ($model) {
                    return $model->user;
                },
                'skipOnError' => true,
                'targetClass' => User::className(),
                'targetAttribute' => ['user_id' => 'id'],
            ],
                        
            ['title', 'required'],
            ['title', 'string', 'max' => 255],
            ['title', 'filter', 'filter' => 'strip_tags'],
            ['title', 'filter', 'filter' => 'trim'],
                        
            ['slug', 'string', 'max' => 255],
            ['slug', 'default', 'value' => ''],
                        
            ['parent_id', 'integer'],
            ['parent_id', 'default', 'value' => null],
            ['parent_id', 'exist',
                'skipOnEmpty' => true,
                'skipOnError' => true,
                'targetClass' => static::className(),
                'targetAttribute' => ['parent_id' => 'id'],
            ],
            
            ['created_at', 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHistory()
    {
        return $this->hasMany(History::className(), ['wiki_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHistoryLatest()
    {
        return $this->hasOne(History::className(), ['wiki_id' => 'id'])->orderBy('rev DESC');
    }

    /**
     * @return ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(static::className(), ['id' => 'parent_id']);
    }
    
    /**
     * @return ActiveQuery
     */
    public function getChildren()
    {
        return $this->hasMany(static::className(), ['parent_id' => 'id']);
    }
    
    /**
     * Recursive finds current wiki children.
     * @return Wiki[]
     */
    public function getChildrenAll($queryCallback = null)
    {
        $query = new ActiveQuery(static::className());
        if ($queryCallback instanceof \Closure) {
            call_user_func($queryCallback, $query);
        }
        return $this->getTree($query, $this->id);
    }
    
    /**
     * Get recursive all children.
     * @param ActiveQuery $query get parents query.
     * @param array $id request children from parent id.
     * @param array $tree internal, current list of children.
     * @return Wiki[]
     */
    protected function getTree($query, $id = 0, $tree = [])
    {
        $_query = clone $query;
        /** @var $children Wiki[] */
        $children = $_query->where(['parent_id' => $id])->all();
        /** @var $child Wiki */
        foreach ($children as $child) {
            $tree[] = $child;
            $tree = $this->getTree($query, $child->id, $tree);
        }
        return $tree;
    }
    
    /**
     * Finds all root pages.
     * @return Wiki[]
     */
    public static function findAllRoot()
    {
        return static::findAll(['parent_id' => null]);
    }
}
