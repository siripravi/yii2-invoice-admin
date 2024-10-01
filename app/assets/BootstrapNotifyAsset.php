<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @version $Id$
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * GrowlAsset
 *
 * @author skoro
 */
class BootstrapNotifyAsset extends AssetBundle
{
    
    public $sourcePath = '@bower/remarkable-bootstrap-notify';
    
    public $js = [
        'dist/bootstrap-notify.min.js',
    ];
    
    public $depends = [
        'yii\bootstrap5\BootstrapAsset',
    ];
}
