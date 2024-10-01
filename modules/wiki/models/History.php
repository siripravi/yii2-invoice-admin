<?php

namespace modules\wiki\models;

use app\models\User;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "wiki_history".
 *
 * @property integer $id
 * @property integer $wiki_id
 * @property integer $user_id
 * @property string $content
 * @property integer $created_at
 * @property string $summary
 * @property string $host_ip
 * @property integer $rev
 *
 * @property Wiki $wiki
 * @property User $user
 */
class History extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%wiki_history}}';
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
                'class' => TimestampBehavior::className(),
                'updatedAtAttribute' => false,
            ],
        ];
    }
    
    public function init()
    {
        parent::init();
        $this->on(self::EVENT_BEFORE_VALIDATE, [$this, 'onBeforeValidate']);
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['wiki_id', 'required'],
            ['wiki_id', 'integer'],
            ['wiki_id', 'exist',
                'skipOnError' => true,
                'targetClass' => Wiki::className(),
                'targetAttribute' => ['wiki_id' => 'id'],
            ],
            
            ['user_id', 'integer'],
            ['user_id', 'exist',
                'skipOnError' => true,
                'targetClass' => User::className(),
                'targetAttribute' => ['user_id' => 'id'],
            ],
            
            ['content', 'string'],
            
            ['rev', 'required'],
            ['rev', 'integer'],
            
            ['summary', 'string', 'max' => 255],
            ['summary', 'filter', 'filter' => 'strip_tags'],
            ['summary', 'filter', 'filter' => 'trim'],
            
            ['host_ip', 'string', 'max' => 15],
            
            ['created_at', 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'wiki_id' => 'Wiki ID',
            'user_id' => 'User ID',
            'content' => 'Content',
            'created_at' => 'Created At',
            'summary' => 'Summary',
            'host_ip' => 'Host Ip',
        ];
    }
    
    /**
     * Increment revision number for new records.
     */
    public function onBeforeValidate()
    {
        if (!$this->isNewRecord && !$this->wiki_id) {
            return;
        }
        $max = static::find()->where(['wiki_id' => $this->wiki_id])->max('rev');
        $this->rev = (int) $max + 1;
    }
    
    /**
     * @return ActiveQuery
     */
    public function getWiki()
    {
        return $this->hasOne(Wiki::className(), ['id' => 'wiki_id']);
    }
    
    /**
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
    
    /**
     * @return ActiveQuery
     */
    public function getPrevious()
    {
        $rev = (int) $this->rev - 1;
        if ($rev < 1) {
            return false;
        }
        return static::find()->where(['wiki_id' => $this->wiki_id, 'rev' => $rev]);
    }
    
    /**
     * Get latest version of wiki page.
     * @param integer $wikiId
     * @return Wiki|false
     */
    public static function findOneLatest($wikiId)
    {
        return static::find()
            ->where([
                'wiki_id' => $wikiId,
            ])
            ->with('wiki')
            ->orderBy('created_at DESC')
            ->one();
    }
}
