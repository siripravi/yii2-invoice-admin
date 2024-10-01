<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @since 0.2
 */

namespace app\commands;

use app\base\ModuleMigrateException;
use Yii;

/**
 * Apply/revert module migration hooks.
 *
 * @author skoro
 */
class MigrateController extends \yii\console\controllers\MigrateController
{
    
    /**
     * Get new migrations for module.
     * @param string $moduleId
     * @return array migrations list
     */
    public function getModuleNewMigrations($moduleId)
    {
        if (!is_dir($dir = $this->getModuleMigrationsDir($moduleId))) {
            return [];
        }
        $this->migrationPath = $dir;
        return $this->getNewMigrations();
    }
    
    /**
     * Apply module migrations.
     * @param string $moduleId
     * @throws ModuleMigrateException
     */
    public function moduleMigrateUp($moduleId)
    {
        $failed = [];
        $migrations = $this->getModuleNewMigrations($moduleId);
        ob_start();
        foreach ($migrations as $migration) {
            if (!$this->migrateUp($migration)) {
                $failed[] = $migration;
            }
        }
        $output = ob_get_clean();
        if ($failed) {
            throw new ModuleMigrateException($moduleId, $failed, $output);
        }
    }
    
    /**
     * Revert module migrations.
     * @param string $moduleId
     * @param array $migrations
     * @throws ModuleMigrateException
     */
    public function moduleMigrateDown($moduleId, $migrations)
    {
        $this->migrationPath = $this->getModuleMigrationsDir($moduleId);
        $failed = [];
        ob_start();
        // Start revert migrations from reverse order.
        $migrations = array_reverse($migrations);
        foreach ($migrations as $migration) {
            if (!$this->migrateDown($migration)) {
                $failed[] = $migration;
            }
        }
        $output = ob_get_clean();
        if ($failed) {
            throw new ModuleMigrateException($moduleId, $failed, $output);
        }
    }
    
    /**
     * 
     * @param string $moduleId
     * @return string
     */
    protected function getModuleMigrationsDir($moduleId)
    {
        return Yii::getAlias('@modules') . DIRECTORY_SEPARATOR . $moduleId .
                DIRECTORY_SEPARATOR . 'migrations';
    }
}
