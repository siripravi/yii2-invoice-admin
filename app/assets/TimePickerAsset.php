<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @version $Id$
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * TimePickerAsset
 *
 * @author skoro
 */
class TimePickerAsset extends AssetBundle
{
    
    public $sourcePath = '@vendor/almasaeed2010/adminlte/plugins/timepicker';
    
    public $css = [
        'bootstrap-timepicker.min.css',
    ];
    
    public $js = [
        'bootstrap-timepicker.min.js',
    ];
}
