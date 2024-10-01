<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @version $Id$
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * iCheck check/radion plugin.
 *
 * @author skoro
 */
class CheckAsset extends AssetBundle
{
    
    public $sourcePath = '@vendor/almasaeed2010/adminlte/plugins/iCheck';
    
    public $js = [
        'icheck.min.js',
    ];
    
    public $css = [
        'all.css',
    ];
    
    public $depends = [
        'yii\web\JqueryAsset',
    ];
    
    /**
     * @var string checkbox style name.
     */
    public $style;
    
    /**
     * @inheritdoc
     */
    public function registerAssetFiles($view)
    {
        $this->css[] = $this->style . '/_all.css';
        parent::registerAssetFiles($view);
    }
}
