<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @version $Id$
 * @since 0.2
 */

namespace modules\wiki\assets;

/**
 * DiffAsset
 *
 * @author skoro
 */
class DiffAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@modules/wiki/assets';
    
    public $css = [
        'diff.css',
    ];
}
