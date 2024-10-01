<?php
/**
 * @author Skorobogatko Oleksii <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @since 0.1
 */

namespace app\base\console;

use Yii;
use yii\helpers\Console;

/**
 * Controller
 * 
 * Parent for console controllers.
 *
 * @todo Helper method for output tabular data.
 * @author skoro
 */
class Controller extends \yii\console\Controller
{
    
    /**
     * Prints translated message.
     * @param string $message
     * @param array $params
     */
    public function p($message, array $params = [])
    {
        $this->stdout(Yii::t('app', $message, $params) . PHP_EOL);
    }
    
    /**
     * Prints error message.
     * @param string $message
     * @param array $params
     */
    public function err($message, array $params = [])
    {
        $this->stderr(Yii::t('app', $message, $params) . PHP_EOL, Console::FG_RED);
    }
    
}
