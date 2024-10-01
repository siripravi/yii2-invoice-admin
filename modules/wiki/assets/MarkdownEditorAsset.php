<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @version $Id$
 */

namespace modules\wiki\assets;

/**
 * MarkdownEditorAsset
 *
 * @author skoro
 */
class MarkdownEditorAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@bower/bootstrap-markdown';
    
    public $css = [
        'css/bootstrap-markdown.min.css',
    ];
    
    public $js = [
        'js/bootstrap-markdown.js',
    ];
    
    public $depends = [
        'yii\bootstrap\BootstrapAsset',
    ];
    
    /**
     * Editor language.
     * @var string
     */
    public $language;

    /**
     * Register language script.
     */
    public function registerAssetFiles($view)
    {
        if ($this->language) {
            $this->js[] = 'locale/bootstrap-markdown.' . $this->language . '.js';
        }
        parent::registerAssetFiles($view);
    }
    
}
