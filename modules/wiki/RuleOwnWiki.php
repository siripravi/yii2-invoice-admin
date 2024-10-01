<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @since 0.2
 */

namespace modules\wiki;

/**
 * RuleOwnWiki
 *
 * @author skoro
 */
class RuleOwnWiki extends \yii\rbac\Rule
{
    public $name = 'isOwnWiki';
    
    /**
     * @param string|integer $user the user ID.
     * @param Item $item the role or permission that this rule is associated with
     * @param array $params parameters passed to ManagerInterface::checkAccess().
     * @return boolean a value indicating whether the rule permits the role or permission it is associated with.
     */    
    public function execute($user, $item, $params)
    {
        return isset($params['wiki']) ? $params['wiki']->user_id == $user : false;
    }
}
