<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @since 0.1
 */

namespace app\forms\user;

use app\models\User;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * User Profile form.
 *
 * @author skoro
 */
class Profile extends Model
{
    
    /**
     * @var string 
     */
    public $name;
    
    /**
     * @var string
     */
    public $password;
    
    /**
     * @var string
     */
    public $password_repeat;
    
    /**
     * @var string[]
     */
    public $roles;
    
    /**
     * @var integer
     */
    public $status;
    
    /**
     * @var User
     */
    protected $user;
    
    /**
     * Creates a form model with given user.
     * @param User $user
     * @param array $config name-value pairs that will be used to initialize the object properties
     */
    public function __construct(User $user, $config = [])
    {
        $this->user = $user;
        $this->reset();
        parent::__construct($config);
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'required'],
            ['name', 'string', 'min' => 3, 'max' => 64],
            ['name', 'filter', 'filter' => 'trim'],
            
            ['password', 'compare'],
            ['password', 'string', 'min' => 6, 'max' => 64],
            
            ['password_repeat', 'string', 'min' => 6, 'max' => 64],
            
            ['roles', 'required', 'on' => 'admin'],
            ['roles', 'each', 'rule' => ['string'], 'on' => 'admin'],
            
            ['status', 'required', 'on' => 'admin'],
            ['status', 'integer'],
            ['status', 'in',
                'range' => [User::STATUS_DISABLED, User::STATUS_ENABLED, User::STATUS_PENDING],
            ],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('app', 'User name'),
            'password' => Yii::t('app', 'Password'),
            'password_repeat' => Yii::t('app', 'Confirm password'),
            'roles' => Yii::t('app', 'Roles'),
            'status' => Yii::t('app', 'Status'),
        ];
    }
    
    /**
     * Read only email property.
     * @return string
     */
    public function getEmail()
    {
        return $this->user->email;
    }
    
    /**
     * Returns user instance.
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
    
    /**
     * Save changes.
     * @return boolean
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }
        
        $this->user->name = $this->name;
        if (!empty($this->password)) {
            $this->user->setPassword($this->password);
        }

        // Changes made by administrator.
        if ($this->getScenario() === 'admin') {
            $auth = Yii::$app->authManager;
            $this->user->status = $this->status;
            // Save only those roles that not assigned to user.
            $userRoles = $this->getUserRoleNames();
            // New roles.
            $diffAssign = array_diff($this->roles, $userRoles);
            foreach ($diffAssign as $roleName) {
                if ($role = $auth->getRole($roleName)) {
                    $auth->assign($role, $this->user->id);
                }
            }
            // Revoked roles.
            $diffRevoke = array_diff($userRoles, $this->roles);
            foreach ($diffRevoke as $roleName) {
                if ($role = $auth->getRole($roleName)) {
                    $auth->revoke($role, $this->user->id);
                }
            }
        }
        
        return $this->user->save();
    }
    
    public function reset()
    {
        $this->name = $this->user->name;
        $this->password = '';
        $this->password_repeat = '';
        $this->status = $this->user->status;
        $this->roles = $this->getUserRoleNames();
    }
    
    /**
     * @return string[]
     */
    protected function getUserRoleNames()
    {
        return ArrayHelper::getColumn($this->user->getRoles(), 'name', false);
    }
}
