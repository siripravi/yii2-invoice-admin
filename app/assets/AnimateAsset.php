<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @version $Id$
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * AnimateAsset
 *
 * @author skoro
 */
class AnimateAsset extends AssetBundle
{
    
    public $sourcePath = '@bower/animate.css';
    
    public $css = [
        'animate.min.css',
    ];
}
