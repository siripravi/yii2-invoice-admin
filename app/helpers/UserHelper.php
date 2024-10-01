<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @since 0.2
 */

namespace app\helpers;

use app\models\User;
use yii\helpers\Html;

/**
 * Some view helpers for User model.
 *
 * @author skoro
 * @since 0.2
 */
class UserHelper
{
    
    /**
     * Renders user status label.
     * @param User $user
     * @param array $options
     * @return string
     */
    public static function status(User $user, array $options = [])
    {
        $defaults = ['class' => 'label'];
        switch ($user->status) {
            case User::STATUS_DISABLED:
                Html::addCssClass($defaults, 'label-danger');
                break;
            case User::STATUS_PENDING:
                Html::addCssClass($defaults, 'label-warning');
                break;
            default:
                Html::addCssClass($defaults, 'label-success');
                
        }
        $options = array_merge($defaults, $options);
        return Html::tag('span', $user->getStatusLabel(), $options);
    }
    
    /**
     * Returns a link to user profile page suitable for use in Url::to().
     * @param User $user
     * @param array $params optional, additional parameters to link.
     * @return array
     */
    public static function getProfileUrl(User $user, array $params = [])
    {
        return array_merge(['/user/profile', 'id' => $user->id], $params);
    }
    
    /**
     * 
     * @param User $user
     * @return string
     */
    public static function userLink(User $user, array $options = [], array $linkParams = [])
    {
        return Html::a(Html::encode($user->name), static::getProfileUrl($user, $linkParams), $options);
    }
}
