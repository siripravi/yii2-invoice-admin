<?php
/**
 * @author Skorobogatko Oleksii <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @since 0.1
 */

namespace app\forms\user;

use app\components\Param;
use app\models\User;
use app\base\MailTrait;
use Yii;
use yii\base\Model;
use yii\web\UserEvent;

/**
 * User register form.
 *
 * @author skoro
 */
class Register extends Model
{
    
    use MailTrait;
    
    const EVENT_BEFORE_REGISTER = 'userBeforeRegister';
    const EVENT_AFTER_REGISTER = 'userAfterRegister';

    /**
     * @var string
     */
    public $name;
    
    /**
     * @var string
     */
    public $email;
    
    /**
     * @var string
     */
    public $password;
    
    /**
     * @var string
     */
    public $password_repeat;
    
    /**
     * @var integer
     */
    public $status;
    
    /**
     * @var boolean
     */
    public $sendmail;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'filter', 'filter' => 'trim'],
            ['name', 'required'],
            ['name', 'string', 'min' => 2, 'max' => 255],
            ['name', 'unique', 'targetClass' => User::className(), 'message' => 'The name has already been taken.'],

            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => User::className(), 'message' => 'This email address has already been taken.'],

            ['password', 'required'],
            ['password', 'compare'],
            ['password', 'string', 'min' => 6, 'max' => 64],
            
            ['password_repeat', 'required'],
            
            ['status', 'default', 'value' => User::STATUS_ENABLED],
            ['status', 'integer'],
            ['status', 'in',
                'range' => [User::STATUS_DISABLED, User::STATUS_ENABLED, User::STATUS_PENDING],
            ],
            
            ['sendmail', 'boolean', 'on' => ['admin']],
            ['sendmail', 'default', 'value' => false, 'on' => ['admin']],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('app', 'User name'),
            'email' => Yii::t('app', 'Email'),
            'password' => Yii::t('app', 'Password'),
            'password_repeat' => Yii::t('app', 'Confirm password'),
            'status' => Yii::t('app', 'Status'),
            'sendmail' => Yii::t('app', 'Send email to user'),
        ];
    }

    /**
     * Register a new user.
     * @return User|false
     */
    public function register()
    {
        if ($this->validate()) {
            $user = new User();
            $user->name = $this->name;
            $user->email = $this->email;
            $user->status = $this->status;
            $user->setPassword($this->password);

            if ($this->userRegisterEvent(self::EVENT_BEFORE_REGISTER, $user) &&
                    $user->save()) {
                $this->assignDefaultRole($user);
                $this->mailAccountCreated($user);
                $this->userRegisterEvent(self::EVENT_AFTER_REGISTER, $user);
                return $user;
            }
        }
        
        $this->password = $this->password_repeat = '';
        $this->sendmail = false;
        
        return false;
    }
    
    /**
     * Assign default role to user.
     * @param User $user
     * @return boolean
     */
    protected function assignDefaultRole(User $user)
    {
        $auth = Yii::$app->authManager;
        
        $roleName = Param::value('User.defaultRole');
        if (!$roleName) {
            return false;
        }
        
        if (!($role = $auth->getRole($roleName))) {
            Yii::warning('Cannot find role: ' . $roleName);
            return false;
        }
        
        $auth->assign($role, $user->id);
        return true;
    }
    
    /**
     * This method generate user register event.
     * @param string $name event name.
     * @param User $user
     * @return boolean
     */
    protected function userRegisterEvent($name, User $user)
    {
        $event = new UserEvent([
            'identity' => $user,
        ]);
        $this->trigger($name, $event);
        
        return $event->isValid;
    }
    
    /**
     * Mail to user when account created.
     * @param User $user
     */
    protected function mailAccountCreated(User $user)
    {
        if ($this->sendmail) {
            return $this->mail('accountCreated', $user->email, [
                'subject' => Yii::t('app', 'An account created for you at {site}', [
                    'site' => Yii::$app->name,
                ]),
                'register' => $this,
                'user' => $user,
            ]);
        }
    }
}
