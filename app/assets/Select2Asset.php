<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @version $Id$
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Select2Asset
 *
 * @author skoro
 */
class Select2Asset extends AssetBundle
{
    
    public $sourcePath = '@vendor/almasaeed2010/adminlte/bower_components/select2/dist';
    
    public $css = [
        'css/select2.css',
    ];
    
    public $js = [
        'js/select2.js',
    ];
    
    public $depends = [
        'yii\web\JqueryAsset',
    ];
    
    /**
     * Select2 localization language.
     * @var string
     */
    public $language;
    
    /**
     * Register language script.
     */
    public function registerAssetFiles($view)
    {
        if ($this->language) {
            $this->js[] = "i18n/{$this->language}.js";
        }
        parent::registerAssetFiles($view);
    }
}
