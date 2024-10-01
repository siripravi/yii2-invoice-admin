<?php
/**
 * @author Skorobogatko Oleksii <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @since 0.3
 */

namespace app\base;

use app\components\Param;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Wrapper for mailer compose.
 * 
 * Send email to destination address from site admin email.
 *
 * @author skoro
 */
trait MailTrait
{
    
    /**
     * Send email.
     * @param string $view mail template.
     * @param string $to destination email address.
     * @param array $params view parameters. Special parameter 'subject'
     * used for mail subject rest parameters passed to view.
     * @return boolean
     */
    public function mail($view, $to, array $params)
    {
        $views = [
            'html' => $view . '-html',
            'text' => $view . '-text',
        ];
        
        $subject = ArrayHelper::remove($params, 'subject');
        
        $compose = Yii::$app->mailer->compose($views, $params);
        if (!empty($subject)) {
            $compose->setSubject($subject);
        }
        $compose->setTo($to)
                ->setFrom(Param::value('Site.adminEmail'));
        
        return $compose->send();
    }
}
