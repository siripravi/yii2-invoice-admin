<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @since 0.2
 */

namespace modules\wiki\forms;

use modules\wiki\models\History;
use modules\wiki\models\Wiki;
use Yii;
use yii\base\Model;

/**
 * Editor
 *
 * @author skoro
 */
class Editor extends Model
{
    
    const EVENT_BEFORE_UPDATE = 'beforeWikiUpdate';
    const EVENT_AFTER_UPDATE = 'afterWikiUpdate';
    
    /**
     * @var string
     */
    public $title;
    
    /**
     * @var string
     */
    public $slug;
    
    /**
     * @var string
     */
    public $content;
    
    /**
     * @var string
     */
    public $summary;
    
    /**
     * @var Wiki
     */
    protected $_wiki;
    
    /**
     * @param Wiki $wiki
     * @param array $config
     */
    public function __construct(Wiki $wiki, $config = array())
    {
        $this->_wiki = $wiki;
        $this->title = $wiki->title;
        $this->slug = $wiki->slug;
        
        $this->content = $this->getHistoryContent();
        
        parent::__construct($config);
    }
    
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['title', 'required'],
            ['title', 'string', 'max' => 255],
            ['title', 'filter', 'filter' => 'strip_tags'],
            ['title', 'filter', 'filter' => 'trim'],
            
            ['slug', 'string', 'max' => 255],
            
            ['summary', 'string', 'max' => 255],
            ['summary', 'default', 'value' => ''],
            
            ['content', 'string'],
        ];
    }
    
    /**
     * Validate and save wiki page.
     * @return Wiki|false
     */
    public function save()
    {
        $this->trigger(self::EVENT_BEFORE_UPDATE);
        
        if (!$this->validate()) {
            return false;
        }
        
        $transaction = Yii::$app->db->beginTransaction();
        
        try {
            $isNew = $this->isNew(); // Preserve New status for later checking.
            $this->_wiki->title = $this->title;
            $this->_wiki->slug = $this->slug;
            if (!$this->_wiki->save()) {
                throw new \yii\db\Exception('Cannot save Wiki model.');
            }

            // Don't save wiki if content not modified.
            if ($isNew || $this->content != $this->getHistoryContent()) {
                $history = new History();
                $history->wiki_id = $this->_wiki->id;
                $history->content = $this->content;
                $history->host_ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
                $history->summary = $this->summary;
                if (!$history->save()) {
                    throw new \yii\db\Exception('Cannot save History model.');
                }
            }
            
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            return false;
        }
        
        $this->trigger(self::EVENT_AFTER_UPDATE);
            
        return $this->_wiki;
    }
    
    /**
     * @return boolean
     */
    public function isNew()
    {
        return $this->_wiki->isNewRecord;
    }
    
    /**
     * Returns wiki instance.
     * @return Wiki
     */
    public function getWiki()
    {
        return $this->_wiki;
    }
    
    /**
     * Returns recent actual page content.
     * @return string
     */
    public function getHistoryContent()
    {
        $history = $this->_wiki->historyLatest;
        return $history ? $history->content : '';
    }
}
