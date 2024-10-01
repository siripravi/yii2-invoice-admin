<?php
/**
 * @author Skorobogatko Oleksii <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @since 0.1
 */

namespace app\forms\user;

use app\models\User;
use app\base\MailTrait;
use Yii;
use yii\base\Model;

/**
 * PasswordRequest
 *
 * @author skoro
 */
class PasswordRequest extends Model
{
    
    use MailTrait;
    
    /**
     * @var string
     */
    public $email;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => Yii::$app->user->identityClass,
                'filter' => ['status' => User::STATUS_ENABLED],
                'message' => 'User with this email not found.',
            ],
        ];
    }
    
    /**
     * Send password reset instructions.
     * @return boolean
     */
    public function sendEmail()
    {
        $user = User::findByEmail($this->email);
        if ($user && $user->status === User::STATUS_ENABLED) {
            $user->generatePasswordResetToken();
            if ($user->save()) {
                return $this->mail('passwordRequest', $this->email, [
                    'subject' => Yii::t('app', 'Reset password information for {name} at {site}', [
                        'name' => $user->name,
                        'site' => Yii::$app->name]
                    ),
                    'user' => $user,
                ]);
            }
        }
        
        return false;
    }
    
}
