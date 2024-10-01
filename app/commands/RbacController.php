<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @since 0.2
 */

namespace app\commands;

use app\base\console\Controller;
use app\models\User;
use InvalidArgumentException;
use Yii;
use yii\helpers\Console;
use yii\rbac\DbManager;
use yii\rbac\Item;
use yii\rbac\Permission;
use yii\rbac\Role;
use yii\rbac\Rule;
use yii\validators\EmailValidator;

/**
 * Rbac console command.
 *
 * @author skoro
 */
class RbacController extends Controller
{
    
    /**
     * @var DbManager
     */
    protected $_auth;
    
    /**
     * @var Permission[]
     */
    protected $_perms;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->_auth = Yii::$app->authManager;
        parent::init();
    }
    
    /**
     * Initializes rbac rules.
     * @param boolean $forceYes put 'yes' to confirmation, use this with caution!
     */
    public function actionInit($forceYes = false)
    {
        if (!$forceYes) {
            $confirm = $this->confirm("Do you want to initialize RBAC rules ?\nPlease aware, previous rules WILL BE OVERWRITTEN", false);
            if (!$confirm) {
                return;
            }
        }
        
        $this->stdout('Auth rules are deleting...'.PHP_EOL, Console::BOLD);
        $db = Yii::$app->db;
        $db->createCommand()->delete($this->_auth->itemTable)->execute();
        $db->createCommand()->delete($this->_auth->itemChildTable)->execute();
        $db->createCommand()->delete($this->_auth->assignmentTable)->execute();
        $db->createCommand()->delete($this->_auth->ruleTable)->execute();
        
        $this->stdout('Creating permissions...'.PHP_EOL, Console::BOLD);
        
        // Users
        $this->addPermission('createUser', 'Create a user');
        $this->addPermission('viewAnyUser', 'View an any user');
        $this->addPermission('updateAnyUser', 'Update an any user');
        $this->addPermission('deleteAnyUser', 'Delete an any user');
        
        //Settings
        $this->addPermission('updateSettings', 'Update site settings');
        
        $this->stdout('Attach permissions to roles...'.PHP_EOL, Console::BOLD);
        $registered = $this->_auth->createRole('Registered');
        $this->_auth->add($registered);
        
        $admin = $this->_auth->createRole('Administrator');
        $this->_auth->add($admin);
        $this->_auth->addChild($admin, $registered);
        $this->_auth->addChild($admin, $this->_perms['createUser']);
        $this->_auth->addChild($admin, $this->_perms['viewAnyUser']);
        $this->_auth->addChild($admin, $this->_perms['updateAnyUser']);
        $this->_auth->addChild($admin, $this->_perms['deleteAnyUser']);
        $this->_auth->addChild($admin, $this->_perms['updateSettings']);
    }
    
    /**
     * Show available roles.
     * @param string $roleName role name.
     */
    public function actionIndex($roleName = '')
    {
        if ($roleName) {
            $roles = [$this->getRole($roleName)];
        } else {
            $roles = $this->_auth->getRoles();
        }
        
        foreach ($roles as $role) {
            $this->stdout($role->name . PHP_EOL);
            $permissions = $this->_auth->getPermissionsByRole($role->name);
            foreach ($permissions as $permission) {
                $this->stdout("- {$permission->name}\n");
            }
        }
    }
    
    /**
     * Assign role to a user.
     * @param string $nameOrEmail
     * @param string $roleName
     */
    public function actionAssign($nameOrEmail, $roleName)
    {
        $user = $this->getUser($nameOrEmail);
        $role = $this->getRole($roleName); // Ensure that role is exist.
        foreach ($user->getRoles() as $userRole) {
            if ($userRole->name == $role->name) {
                $this->err('Already assinged to "{role}" role.', ['role' => $role->name]);
                return;
            }
        }
        $this->_auth->assign($role, $user->id);
        $this->p('Role "{role}" assigned to user "{name}".', [
            'role' => $role->name,
            'name' => $user->name,
        ]);
    }
    
    /**
     * Revoke role from a user.
     * @param string $nameOrEmail
     * @param string $roleName
     */
    public function actionRevoke($nameOrEmail, $roleName)
    {
        $user = $this->getUser($nameOrEmail);
        $role = $this->getRole($roleName);
        if ($this->_auth->revoke($role, $user->id)) {
            $this->p('Role "{role}" revoked from the user "{name}".', [
                'role' => $role->name,
                'name' => $user->name,
            ]);
        } else {
            $this->stdout("Couldn't revoke role.\n", Console::FG_RED);
        }
    }
    
    /**
     * Add permission to a role.
     * @param string $roleName role name.
     * @param string $permName permission name.
     * @param string $desc optional, permission description.
     */
    public function actionAddperm($roleName, $permName, $desc = '')
    {
        $role = $this->getRole($roleName);
        
        // Check if permission already exists.
        if (!($permission = $this->_auth->getPermission($permName))) {
            $permission = $this->_auth->createPermission($permName);
            $permission->description = $desc;
            $this->_auth->add($permission);
        }
        
        $permissions = $this->_auth->getPermissionsByRole($roleName);
        if (isset($permissions[$permName])) {
            $this->stdout("Role '$roleName' already has permission '$permName'.", Console::FG_YELLOW);
            return;
        }
        
        $this->_auth->addChild($role, $permission);
        $this->p('Permission "{perm}" added to role "{role}"', [
            'perm' => $permName,
            'role' => $role->name,
        ]);
    }

    /**
     * Delete permission from a role.
     * @param string $roleName role name.
     * @param string $permName permission name.
     */
    public function actionDelperm($roleName, $permName)
    {
        $role = $this->getRole($roleName);
        $permissions = $this->_auth->getPermissionsByRole($roleName);
        if (!isset($permissions[$permName])) {
            $this->stderr("Role '$roleName' has not permission '$permName'.", Console::FG_RED);
            return;
        }
        
        if (!($item = $this->_auth->getPermission($permName))) {
            $this->stderr("Permission '$permName' cannot found by auth manager.", Console::FG_RED);
            return;
        }
        
        $this->_auth->removeChild($role, $item);
        $this->p('Permission "{perm}" removed from role "{role}"', [
            'perm' => $permName,
            'role' => $role->name,
        ]);
        
        // Transfer permissions to Administrator role.
        if ($roleName != 'Administrator') {
            $adminRole = $this->getRole('Administrator');
            $permissions = $this->_auth->getPermissionsByRole($adminRole->name);
            if (!isset($permissions[$permName])) {
                $this->_auth->addChild($adminRole, $item);
                $this->p('Permission "{perm}" transfer to Administrator.', ['perm' => $permName]);
            }
        }
    }
    
    /**
     * Add permission.
     * @param string $name
     * @param string $description
     * @param Rule $rule
     * @return Item
     */
    protected function addPermission($name, $description, $rule = null)
    {
        $perm = $this->_auth->createPermission($name);
        $perm->description = $description;
        if ($rule instanceof Rule) {
            $perm->ruleName = $rule->name;
        }
        $this->_perms[$name] = $perm;
        $this->_auth->add($perm);
        return $perm;
    }
    
    /**
     * Get user model by its name or email.
     * @param string $nameOrEmail
     * @throws InvalidArgumentException when user not found.
     * @return User
     */
    protected function getUser($nameOrEmail)
    {
        $emailValidator = new EmailValidator();
        if ($emailValidator->validate($nameOrEmail)) {
            $user = User::findByEmail($nameOrEmail);
        } else {
            $user = User::findOne(['name' => $nameOrEmail]);
        }
        
        if (!$user) {
            throw new InvalidArgumentException("Couldn't find user by specified email or name.");
        }
        
        return $user;
    }

    /**
     * Get role by its name.
     * @param string $name
     * @return Role
     * @throws InvalidArgumentException when role not found.
     */
    protected function getRole($name)
    {
        $role = $this->_auth->getRole($name);
        if (!$role) {
            throw new InvalidArgumentException('Role "' . $name . '" not found.');
        }
        return $role;
    }
}
