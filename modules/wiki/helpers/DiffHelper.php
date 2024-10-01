<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @since 0.2
 */

namespace modules\wiki\helpers;

use Diff;
use modules\wiki\DiffRendererHtmlInline;
use modules\wiki\models\History;

/**
 * Page differences helper.
 *
 * @author skoro
 */
class DiffHelper
{
    
    /**
     * 
     * @param History $history
     * @param array $diffOptions
     * @return string
     */
    public static function diff(History $history, $diffOptions = [])
    {
        if (!($previous = $history->previous)) {
            return '';
        }
        
        $content = explode("\n", $history->content);
        $prevContent = explode("\n", $previous->content);
        $diff = new Diff($content, $prevContent, $diffOptions);
        $renderer = new DiffRendererHtmlInline();
        return $diff->render($renderer);
    }
}
