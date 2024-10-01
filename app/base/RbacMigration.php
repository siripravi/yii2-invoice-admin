<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @since 0.2
 */

namespace app\base;

use app\components\Param;
use Yii;
use yii\helpers\ArrayHelper;
use yii\rbac\ManagerInterface;

/**
 * This class intended for rbac auth migrations.
 * @author skoro
 */
class RbacMigration extends Migration
{
    
    /**
     * @var array rbac definition. Example:
     * ```
     * $rbac = [
     *      'permissions' => [
     *          // Simple permission
     *          'viewPost' => 'View a post',
     *          'updatePost' => [
     *              'description' => 'Update any post',
     *              'rule' => 'rule class name',
     *              'child' => 'child permission',
     *          ],
     *          ...
     *      ],
     *      'roles' => [
     *          'PostAdmin' => [ // list of permissions or child roles ],
     *          ...
     *      ],
     * ];
     */
    public $rbac;
    
    /**
     * @var ManagerInterface
     */
    protected $_auth;
    
    /**
     * @var array
     */
    protected $_permissions;
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->_auth = Yii::$app->authManager;
    }
    
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createPermissions();
        $this->createRoles();
        $this->addRolesToDefaultRoleList();
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->addRolesToDefaultRoleList(true);
        $this->removePermissions();
        $this->removeRoles();
    }
    
    /**
     * Create permissions.
     */
    protected function createPermissions()
    {
        $permissions = ArrayHelper::getValue($this->rbac, 'permissions');
        if (!$permissions) {
            return;
        }
        $this->_permissions = [];
        $children = [];
        foreach ($permissions as $name => $data) {
            $permission = $this->_auth->createPermission($name);
            if (is_string($data)) {
                $permission->description = $data;
            } else {
                $permission->description = ArrayHelper::getValue($data, 'description', '');
                if ($ruleClass = ArrayHelper::getValue($data, 'rule')) {
                    $rule = Yii::createObject($ruleClass);
                    if (!$this->_auth->getRule($rule->name)) {
                        $this->_auth->add($rule);
                    }
                    $permission->ruleName = $rule->name;
                }
                $children[$name] = ArrayHelper::getValue($data, 'child');
            }
            $this->_auth->add($permission);
            $this->_permissions[$name] = $permission;
        }
        foreach ($children as $permName => $childName) {
            $this->_auth->addChild($this->_permissions[$permName], $this->_permissions[$childName]);
        }
    }
    
    /**
     * Create roles.
     */
    protected function createRoles()
    {
        if (!($roles = ArrayHelper::getValue($this->rbac, 'roles'))) {
            return;
        }
        foreach ($roles as $name => $permissions) {
            if (!($role = $this->_auth->getRole($name))) {
                $role = $this->_auth->createRole($name);
                $this->_auth->add($role);
            }
            foreach ($permissions as $name) {
                if (!($child = ArrayHelper::getValue($this->_permissions, $name))) {
                    $child = $this->_auth->getRole($name);
                }
                if ($child) {
                    $this->_auth->addChild($role, $child);
                }
            }
        }
    }
    
    /**
     * Remove roles.
     */
    protected function removeRoles()
    {
        if (!($roles = ArrayHelper::getValue($this->rbac, 'roles'))) {
            return;
        }
        foreach ($roles as $name => $children) {
            if (!($role = $this->_auth->getRole($name))) {
                continue;
            }
            foreach ($children as $childName) {
                if ($permission = ArrayHelper::getValue($this->_permissions, $childName)) {
                    $this->_auth->removeChild($role, $permission);
                } elseif ($childRole = $this->_auth->getRole($childName)) {
                    $this->_auth->removeChild($role, $childRole);
                }
            }
            $this->_auth->remove($role);
            $userIds = $this->_auth->getUserIdsByRole($name);
            foreach ($userIds as $userId) {
                $this->_auth->revoke($role, $userId);
            }
        }
    }
    
    /**
     * Remove permissions.
     */
    protected function removePermissions()
    {
        if (!($permissions = ArrayHelper::getValue($this->rbac, 'permissions'))) {
            return;
        }
        $this->_permissions = [];
        foreach ($permissions as $name => $data) {
            if (!($permission = $this->_auth->getPermission($name))) {
                continue;
            }
            if (($childName = ArrayHelper::getValue($data, 'child')) &&
                    ($child = $this->_auth->getPermission($childName))) {
                $this->_auth->removeChild($permission, $child);
            }
            if ($ruleClass = ArrayHelper::getValue($data, 'rule')) {
                $rule = Yii::createObject($ruleClass);
                $this->_auth->remove($rule);
            }
            $this->_auth->remove($permission);
            $this->_permissions[$name] = $permission;
        }
    }
    
    /**
     * Add or remove roles to User.defaultRole parameter.
     * @param boolean $uninstall remove roles instead adding.
     */
    protected function addRolesToDefaultRoleList($uninstall = false)
    {
        if (!($roles = ArrayHelper::getValue($this->rbac, 'roles'))) {
            return;
        }
        if (!($config = Param::getConfig('User.defaultRole'))) {
            return;
        }
        foreach ($roles as $roleName => $ignore) {
            $options = $config->options;
            if ($uninstall && isset($options[$roleName])) {
                unset($options[$roleName]);
            } elseif (!$uninstall && !isset($options[$roleName])) {
                $options[$roleName] = $roleName;
            }
            $config->options = $options;
        }
        $config->save();
    }
}
