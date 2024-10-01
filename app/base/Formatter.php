<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @since 0.1
 */

namespace app\base;

use app\models\User;
use app\widgets\ProgressBar;
use Yii;
use yii\helpers\Html;

/**
 * Formatter
 *
 * @author skoro
 */
class Formatter extends \yii\i18n\Formatter
{
    
    /**
     * Markdown parsers.
     * @see Formatter::asMarkdown()
     */
    const MARKDOWN_PARSER_TRADITIONAL = '\cebe\markdown\Markdown';
    const MARKDOWN_PARSER_GITHUB = '\cebe\markdown\GithubMarkdown';
    const MARKDOWN_PARSER_EXTRA = '\cebe\markdown\MarkdownExtra';

    /**
     * Format value as progress bar widget.
     * @param integer $value progress value
     * @param array $options widget options
     * @return string
     */
    public function asProgressBar($value, $options = [])
    {
        $options['value'] = $value;
        return ProgressBar::widget($options);
    }
    
    /**
     * Converts Markdown to html.
     * @param string $text markdown source.
     * @param string $parserClass markdown parser class.
     * @return string
     */
    public function asMarkdown($text, $parserClass = self::MARKDOWN_PARSER_EXTRA)
    {
        $parser = new $parserClass();
        return $parser->parse($text);
    }
    
    /**
     * Output user as link to their profile.
     * User must has 'viewAnyUser' permission to link to user profiles otherwise
     * outputs as simple text.
     * @param User $user
     * @param array $options link options
     * @return string
     */
    public function asUserlink($user, array $options = [])
    {
        if (empty($user)) {
            return $this->nullDisplay;
        }
        
        $username = Html::encode($user->name);
        if (Yii::$app->user->id == $user->id) {
            $link = ['/user/profile'];
        } elseif (Yii::$app->user->can('viewAnyUser')) {
            $link = ['/user/profile', 'id' => $user->id];
        } else {
            return $username;
        }
        
        return Html::a($username, $link, $options);
    }
}
