<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @since 0.2
 */

namespace app\base;

use Yii;

/**
 * ModuleApplicationTrait
 *
 * @author skoro
 */
trait ModuleApplicationTrait
{
    /**
     * @var array
     */
    private $_modules = [];
    
    /**
     * Retrieves the child module of the specified ID.
     * This method supports retrieving both child modules and grand child modules.
     * @param string $id module ID (case-sensitive). To retrieve grand child modules,
     * use ID path relative to this module (e.g. `admin/content`).
     * @param boolean $load whether to load the module if it is not yet loaded.
     * @return Module|null the module instance, null if the module does not exist.
     * @see hasModule()
     */
    public function getModule($id, $load = true)
    {
        if (($pos = strpos($id, '/')) !== false) {
            // sub-module
            $module = $this->getModule(substr($id, 0, $pos));

            return $module === null ? null : $module->getModule(substr($id, $pos + 1), $load);
        }

        if (isset($this->_modules[$id])) {
            if ($this->_modules[$id] instanceof \yii\base\Module) {
                return $this->_modules[$id];
            } elseif ($load) {
                Yii::trace("Loading module: $id", __METHOD__);
                /** @var $module Module */
                $module = Yii::createObject($this->_modules[$id], [$id, $this]);
                $module->setInstance($module);
                return $this->_modules[$id] = $module;
            }
        }

        return null;
        
    }
    /**
     * Returns the sub-modules in this module.
     * @param boolean $loadedOnly whether to return the loaded sub-modules only. If this is set false,
     * then all sub-modules registered in this module will be returned, whether they are loaded or not.
     * Loaded modules will be returned as objects, while unloaded modules as configuration arrays.
     * @return array the modules (indexed by their IDs)
     */
    public function getModules($loadedOnly = false)
    {
        if ($loadedOnly) {
            $modules = [];
            foreach ($this->_modules as $module) {
                if ($module instanceof \yii\base\Module) {
                    $modules[] = $module;
                }
            }

            return $modules;
        } else {
            return $this->_modules;
        }
    }
    
    /**
     * Checks whether the child module of the specified ID exists.
     * This method supports checking the existence of both child and grand child modules.
     * @param string $id module ID. For grand child modules, use ID path relative to this module (e.g. `admin/content`).
     * @return boolean whether the named module exists. Both loaded and unloaded modules
     * are considered.
     */
    public function hasModule($id)
    {
        if (($pos = strpos($id, '/')) !== false) {
            // sub-module
            $module = $this->getModule(substr($id, 0, $pos));

            return $module === null ? false : $module->hasModule(substr($id, $pos + 1));
        } else {
            return isset($this->_modules[$id]);
        }
    }

    /**
     * Adds a sub-module to this module.
     * @param string $id module ID
     * @param Module|array|null $module the sub-module to be added to this module. This can
     * be one of the following:
     *
     * - a [[Module]] object
     * - a configuration array: when [[getModule()]] is called initially, the array
     *   will be used to instantiate the sub-module
     * - null: the named sub-module will be removed from this module
     */
    public function setModule($id, $module)
    {
        if ($module === null) {
            unset($this->_modules[$id]);
        } else {
            $this->_modules[$id] = $module;
        }
    }

    /**
     * Registers sub-modules in the current module.
     *
     * Each sub-module should be specified as a name-value pair, where
     * name refers to the ID of the module and value the module or a configuration
     * array that can be used to create the module. In the latter case, [[Yii::createObject()]]
     * will be used to create the module.
     *
     * If a new sub-module has the same ID as an existing one, the existing one will be overwritten silently.
     *
     * The following is an example for registering two sub-modules:
     *
     * ```php
     * [
     *     'comment' => [
     *         'class' => 'app\modules\comment\CommentModule',
     *         'db' => 'db',
     *     ],
     *     'booking' => ['class' => 'app\modules\booking\BookingModule'],
     * ]
     * ```
     *
     * @param array $modules modules (id => module configuration or instances)
     */
    public function setModules($modules)
    {
        foreach ($modules as $id => $module) {
            $this->_modules[$id] = $module;
        }

        $modules = $this->getModulesByStatus(Module::STATUS_INSTALLED);
        foreach ($modules as $module) {
            $id = $module['module_id'];
            if (!isset($this->_modules[$id])) {
                $params = $module['data'];
                /** @var $module Module */
                $module = Yii::createObject($params['class'], [$id, $this]);
                $module->setInstance($module);
                $this->_modules[$id] = $module;
            }
        }
    }
    
    /**
     * Get modules by its status: installed or not installed.
     * @param boolean|null $installed boolean for installed or not installed,
     *                                null for all modules.
     * @return array
     */
    public function getModulesByStatus($installed = true)
    {
        try {
            $query = (new \yii\db\Query)
                    ->select('*')
                    ->from('{{%module}}')
                    ->orderBy('name');
            if ($installed !== null) {
                $query->where([
                    'installed' => (int) $installed,
                ]);
            }

            $modules = array_map(function ($module) {
                $module['data'] = unserialize($module['data']);
                $module['installed'] = (bool) $module['installed'];
                return $module;
            }, $query->all());
            
        } catch (\Exception $e) {
            Yii::error($e->getMessage());
            $modules = [];
        }
        
        return $modules;
    }
    
    /**
     * Get module by id.
     * @param string $moduleId
     * @return array
     */
    public function getModuleById($moduleId)
    {
        $module = Yii::$app->db
                ->createCommand('SELECT * FROM {{%module}} WHERE module_id = :module_id')
                ->bindValue(':module_id', $moduleId)
                ->queryOne();
        if ($module) {
            $module['data'] = unserialize($module['data']);
            $module['installed'] = (bool) $module['installed'];
        }
        return $module;
    }
    
    /**
     * Check is module installed or not.
     * @param string $moduleId
     * @return boolean
     */
    public function isModuleInstalled($moduleId)
    {
        $module = $this->getModuleById($moduleId);
        return $module && $module['installed'];
    }
    
    /**
     * Update module entry.
     * @param array $module module definition returned by getModuleById() method.
     * @return boolean
     */
    public function updateModule($module)
    {
        if (isset($module['data'])) {
            $module['data'] = serialize($module['data']);
        }
        $module['installed'] = (int) $module['installed'];
        
        return Yii::$app->db->createCommand()
                ->update('{{%module}}', $module, [
                    'module_id' => $module['module_id'],
                ])
                ->execute();
    }
    
    /**
     * Scan `modules` directory for new modules or update exists ones.
     * @return array list of modules
     * @throws \RuntimeException when `modules` directory cannot be open
     */
    public function scanModules()
    {
        Yii::trace('Module scanning.', __METHOD__);
        if (!is_dir($dir = Yii::getAlias('@modules'))) {
            throw new \RuntimeException('No modules directory.');
        }
        
        if (!($dh = opendir($dir))) {
            throw new \RuntimeException('Cannot open directory: ' . $dir);
        }
        
        $db = Yii::$app->db;
        $modules = [];
        while (($file = readdir($dh)) !== false) {
            if ($file != '.' && $file != '..' &&
                    is_dir($dir . DIRECTORY_SEPARATOR . $file)) {
                $className = ucwords(str_replace(['_', '-'], ' ', $file)) . 'Module';
                $className = str_replace(' ', '', $className);
                $moduleFile = $dir . DIRECTORY_SEPARATOR . $file . DIRECTORY_SEPARATOR . $className . '.php';
                if (!file_exists($moduleFile) && !is_readable($moduleFile)) {
                    continue;
                }
                $moduleClass = 'modules\\' . $file . '\\' . $className;
                $module = Yii::createObject($moduleClass, [$file, $this]);
                $exists = $this->getModuleById($file);
                if ($exists) {
                    Yii::trace("Update module $file info", __METHOD__);
                    $db->createCommand()
                        ->update('{{%module}}', [
                            'name' => $module->moduleName,
                            'desc' => $module->moduleDescription,
                        ], [
                            'module_id' => $module->id,
                        ])
                        ->execute();
                } else {
                    Yii::trace("New module $file found", __METHOD__);
                    $db->createCommand()
                        ->insert('{{%module}}', [
                            'module_id' => $module->id,
                            'name' => $module->moduleName,
                            'installed' => false,
                            'desc' => $module->moduleDescription,
                            'data' => serialize([
                                'class' => $moduleClass,
                            ])
                        ])
                        ->execute();
                }
                $modules[$file] = $moduleClass;
            }
        }
        closedir($dh);
        
        return $modules;
    }
    
    /**
     * Install a module.
     * @param string $moduleId
     * @throws \yii\base\Exception when module already installed or cannot be found.
     * @throws ModuleMigrateException when migrations apply failed.
     */
    public function installModule($moduleId)
    {
        Yii::trace("Module $moduleId installation", __METHOD__);
        if (!($module = $this->getModuleById($moduleId))) {
            throw new \yii\base\Exception('Module not found.');
        }
        if ($module['installed']) {
            throw new \yii\base\Exception('Module already installed.');
        }
        
        /** @var $migrateController \app\commands\MigrateController */
        $migrateController = Yii::createObject([
            'class' => \app\commands\MigrateController::className(),
            'db' => Yii::$app->db,
        ], ['migrate', $this]);
        
        $migrations = $migrateController->getModuleNewMigrations($moduleId);
        if ($migrations) {
            $migrateController->moduleMigrateUp($moduleId);
        }
        
        $module['installed'] = true;
        $module['data']['migrations'] = $migrations;
        
        if (!$this->updateModule($module)) {
            throw new \yii\base\Exception('Cannot install module.');
        }
        
        // Invoke install hook of module.
        $moduleObject = Yii::createObject($module['data']['class'], [$moduleId, $this]);
        if ($moduleObject instanceof Module) {
            $moduleObject->install();
        }
    }
    
    /**
     * Uninstall a module.
     * @param string $moduleId
     * @throws \yii\base\Exception when module already uninstalled or cannot be found.
     */
    public function uninstallModule($moduleId)
    {
        Yii::trace("Module $moduleId uninstallation", __METHOD__);
        if (!($module = $this->getModuleById($moduleId))) {
            throw new \yii\base\Exception('Module not found.');
        }
        if (!$module['installed']) {
            throw new \yii\base\Exception('Module already not installed.');
        }
        
        if (isset($module['data']['migrations'])) {
            /** @var $migrateController \app\commands\MigrateController */
            $migrateController = Yii::createObject([
                'class' => \app\commands\MigrateController::className(),
                'db' => Yii::$app->db,
            ], ['migrate', $this]);
            $migrateController->moduleMigrateDown($moduleId, $module['data']['migrations']);
        }
        
        $module['installed'] = false;

        if (!$this->updateModule($module)) {
            throw new \yii\base\Exception('Cannot uninstall module.');
        }
        
        // Invoke uninstall hook of module.
        $moduleObject = Yii::createObject($module['data']['class'], [$moduleId, $this]);
        if ($moduleObject instanceof Module) {
            $moduleObject->uninstall();
        }
    }
}
