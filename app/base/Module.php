<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @since 0.2
 */

namespace app\base;

use Yii;

/**
 * Application module.
 *
 * @author skoro
 */
class Module extends \yii\base\Module
{
    /**
     * Module statuses.
     */
    const STATUS_INSTALLED = 1;
    const STATUS_NOTINSTALLED = 0;
    
    /**
     * Events.
     */
    const EVENT_MODULE_INSTALLED = 'eventModuleInstalled';
    const EVENT_MODULE_UNINSTALLED = 'eventModuleUninstalled';
    
    /**
     * @var string required, module name.
     */
    public $moduleName;

    /**
     * @var string
     */
    public $moduleDescription = '';
    
    /**
     * Emit event when module is installed.
     */
    public function install()
    {
        $this->trigger(self::EVENT_MODULE_INSTALLED);
    }
    
    /**
     * Emit event when module is uninstalled.
     */
    public function uninstall()
    {
        $this->trigger(self::EVENT_MODULE_UNINSTALLED);
    }
    
    /**
     * Adds menu entries.
     */
    public function addMenu($menu, array $items)
    {
        try {
            Yii::$app->menu->insertItems($menu, $items);
        } catch (\Exception $e) {
            // Menu does not available in console applications.
        }
    }
}
