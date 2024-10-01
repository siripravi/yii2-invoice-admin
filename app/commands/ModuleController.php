<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @since 0.2
 */

namespace app\commands;

use app\base\console\Controller;
use app\base\ModuleMigrateException;
use Exception;
use Yii;
use yii\helpers\Console;

/**
 * Module management.
 *
 * @author skoro
 */
class ModuleController extends Controller
{
    
    /**
     * Refresh module list before any module operation.
     */
    public function beforeAction($action)
    {
        Yii::$app->scanModules();
        return parent::beforeAction($action);
    }
    
    /**
     * List all application modules.
     */
    public function actionIndex()
    {
        $modules = Yii::$app->getModulesByStatus(null);
        printf("%-16s %-20s %-12s\n", 'ID', 'Module name', 'Status');
        print str_repeat('-', 60) . PHP_EOL;
        foreach ($modules as $module) {
            printf("%-16s %-20s %-12s\n",
                $module['module_id'],
                $module['name'],
                $module['installed'] ? 'Installed' : 'Not installed'
            );
        }
    }
    
    /**
     * Install a module.
     * @param string $moduleId module id
     */
    public function actionInstall($moduleId)
    {
        try {
            $module = $this->getModule($moduleId);
            if ($module['installed']) {
                throw new Exception('Module already installed.');
            }
            if (!$this->confirm('Are you sure to install module: ' . $module['name'])) {
                return;
            }
            Yii::$app->installModule($moduleId);
            $this->stdout('Module installed.', Console::BOLD);
        } catch (ModuleMigrateException $e) {
            $this->stderr($e->getMessage() . PHP_EOL);
            foreach ($e->migrations as $migration) {
                $this->stderr("\t" . $migration . PHP_EOL, Console::FG_RED);
            }
        } catch (Exception $e) {
            $this->stderr($e->getMessage());
        }
    }
    
    /**
     * Uninstall a module.
     * @param string $moduleId module id
     */
    public function actionUninstall($moduleId)
    {
        try {
            $module = $this->getModule($moduleId);
            if (!$module['installed']) {
                throw new Exception('Module already uninstalled.');
            }
            $this->stdout('All module data WILL BE deleted!' . PHP_EOL, Console::BOLD);
            if (!$this->confirm('Are you sure to uninstall module: ' . $module['name'])) {
                return;
            }
            Yii::$app->uninstallModule($moduleId);
            $this->stdout('Module uninstalled.', Console::BOLD);
        } catch (ModuleMigrateException $e) {
            $this->stderr($e->getMessage() . PHP_EOL);
            foreach ($e->migrations as $migration) {
                $this->stderr("\t" . $migration . PHP_EOL, Console::FG_RED);
            }
        } catch (Exception $e) {
            $this->stderr($e->getMessage());
        }
    }
    
    /**
     * Get detailed info about module.
     * @param string $moduleId module id
     */
    public function actionInfo($moduleId)
    {
        $module = $this->getModule($moduleId);
        $this->stdout('ID: ', Console::BOLD);
        $this->stdout($module['module_id'] . PHP_EOL);
        $this->stdout('Status: ', Console::BOLD);
        $this->stdout(($module['installed'] ? 'Installed' : 'Not installed') . PHP_EOL);
        $this->stdout('Name: ', Console::BOLD);
        $this->stdout($module['name'] . PHP_EOL);
        $this->stdout('Description: ', Console::BOLD);
        $this->stdout($module['desc'] . PHP_EOL);
        if (isset($module['data']['migrations'])) {
            $this->stdout('Migrations:' . PHP_EOL, Console::BOLD);
            foreach ($module['data']['migrations'] as $migration) {
                $this->stdout("\t" . $migration . PHP_EOL);
            }
        }
    }
    
    /**
     * Get module definition by module id.
     * @param string $moduleId
     * @return array
     * @throws Exception
     */
    protected function getModule($moduleId)
    {
        if (!($module = Yii::$app->getModuleById($moduleId))) {
            throw new Exception('Module ' . $moduleId . ' not found.');
        }
        return $module;
    }
}
