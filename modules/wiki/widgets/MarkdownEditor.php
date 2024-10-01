<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @since 0.2
 */

namespace modules\wiki\widgets;

use yii\widgets\InputWidget;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use modules\wiki\assets\MarkdownEditorAsset;

/**
 * Bootstrap markdown editor.
 *
 * @link http://www.codingdrama.com/bootstrap-markdown/
 * @author skoro
 */
class MarkdownEditor extends InputWidget
{
    
    const RESIZE_NONE = 'none';
    const RESIZE_BOTH = 'both';
    const RESIZE_VERTICAL = 'vertical';
    const RESIZE_HORIZONTAL = 'horizontal';
    
    /**
     * @var array
     */
    public $clientOptions = [];
    
    /**
     * @var string
     */
    public $language;
    
    /**
     * @var boolean wrap editor by container with 'form-group' class.
     */
    public $formGroup = true;
    
    /**
     * @var integer|false editor height.
     */
    public $rows = 10;
    
    /**
     * @var string editor resize mode.
     */
    public $resize = self::RESIZE_VERTICAL;
    
    /**
     * @var string|array|false
     */
    public $previewUrl = false;
    
    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->registerClientScript();
        
        if ($this->rows !== false) {
            $this->options['rows'] = $this->rows;
        }
        
        if ($this->hasModel()) {
            $editor = Html::activeTextarea($this->model, $this->attribute, $this->options);
        } else {
            $editor = Html::textarea($this->name, $this->value, $this->options);
        }
        
        if ($this->formGroup) {
            $editor = Html::tag('div', $editor, ['class' => 'form-group']);
        }
        
        return $editor;
    }
    
    /**
     * Register plugin script.
     */
    protected function registerClientScript()
    {
        $view = $this->getView();
        $id = $this->options['id'];
        if ($this->language) {
            $this->clientOptions['language'] = $this->language;
        }
        if ($this->resize !== self::RESIZE_NONE) {
            $this->clientOptions['resize'] = $this->resize;
        }
        if ($this->previewUrl !== false) {
//            $this->clientOptions['previewUrl'] = Url::to($this->previewUrl);
            $previewUrl = Url::to($this->previewUrl);
            $this->clientOptions['onPreview'] = new \yii\web\JsExpression("function (e) {
                var content = e.getContent();
                if (content.trim().length === 0) { return ''; }
                $.ajax({
                    url: '{$previewUrl}',
                    method: 'POST',
                    cache: false,
                    data: {content: content},
                    async: false,
                    success: function (html) { content = html; } 
                });
                return content;
            }");
        }
        $clientOptions = Json::encode($this->clientOptions);
        
        
        $view->registerJs("jQuery('#$id').markdown($clientOptions);");
        $asset = MarkdownEditorAsset::register($view);
        if ($this->language) {
            $asset->language = $this->language;
        }
    }
    
}
