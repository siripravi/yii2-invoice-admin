<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @since 0.2
 */

namespace app\components;

use app\models\Config;
use ErrorException;
use Yii;
use yii\base\Component;
use yii\base\InvalidValueException;
use yii\db\Query;

/**
 * Site parameters management.
 *
 * Example:
 * ```php
 * Param::value('Site.param');
 * ```
 * will request parameter Site.param in config.web `params` array and
 * then in `config` database table.
 * 
 * @author skoro
 */
class Param extends Component
{
    
    const DEFAULT_SECTION = 'Site';
    
    /**
     * @var Config[] models cache.
     */
    protected static $cache = [];
    
    /**
     * Get parameter value.
     * 
     * First checks config/web.php parameters then checks cached parameters and
     * finally queries config data from database.
     *
     * @param string $param parameter name including section.
     * @param mixed $default default value if parameter not found.
     * @return mixed
     */
    public static function value($param, $default = null)
    {
        if (($value = static::getOverrides($param)) !== null) {
            return $value;
        }
        
        if (isset(static::$cache[$param])) {
            return static::$cache[$param]->value;
        }
        
        if (!($config = static::getConfig($param))) {
            return $default;
        }
        
        static::$cache[$param] = $config;
        return $config->value;
    }
    
    /**
     * Update parameter.
     * Parameter must be exists before updating.
     * @param string $param parameter name including section.
     * @param mixed $value
     * @return mixed
     * @throws InvalidValueException when parameter not found.
     * @throws ErrorException when parameter failed to save.
     * @throws ForbiddenHttpException when user has not permissions to update parameter.
     */
    public static function update($param, $value)
    {
        if (isset(static::$cache[$param])) {
            $config = static::$cache[$param];
        } else {
            $config = static::getConfig($param);
        }
        
        if (!$config) {
            throw new InvalidValueException('Cannot find config for parameter: ' . $param);
        }
        
        if (!static::isAccess($config)) {
            throw new \yii\web\ForbiddenHttpException();
        }
        
        $config->value = $value;
        if (!$config->save()) {
            throw new ErrorException('Cannot save config model for parameter: ' . $param);
        }
        
        static::$cache[$param] = $config;
        
        return $value;
    }
    
    /**
     * Get parameter config model.
     * @param string $param parameter name including section.
     * @return Config
     */
    public static function getConfig($param)
    {
        list ($section, $name) = static::parseParamName($param);
        
        return Config::findOne([
            'name' => $name,
            'section' => $section,
        ]);
    }
    
    /**
     * Get config overrides from config/web.php
     * 
     * Checked in following order:
     * Site.param => $params['Site.param']
     * Site.param => $params['Site']['param']
     * 
     * @param string $param parameter name including section.
     * @return mixed
     */
    public static function getOverrides($param)
    {
        if (isset(Yii::$app->params[$param])) {
            return Yii::$app->params[$param];
        }
        
        list ($section, $name) = static::parseParamName($param);
        if (isset(Yii::$app->params[$section][$name])) {
            return Yii::$app->params[$section][$name];
        }
        
        return null;
    }
    
    /**
     * Get models from specified section.
     * @param string $section
     * @return Config[]
     */
    public static function getConfigsBySection($section)
    {
        return Config::find()
                ->where([
                    'section' => $section,
                ])
                ->indexBy('id')
                ->all();
    }
    
    /**
     * Returns list of section names.
     * @return array
     */
    public static function getSections()
    {
        $rows = (new Query)
                ->from(Config::tableName())
                ->select('section')
                ->distinct()
                ->all();
        
        return array_map(function ($row) {
            return $row['section'];
        }, $rows);
    }
    
    /**
     * Parse parameter name for section name and parameter.
     * @param string $param
     * @return array list of two: section, parameter.
     */
    public static function parseParamName($param)
    {
        $section = self::DEFAULT_SECTION;
        if (($pos = strpos($param, '.')) !== false) {
            $section = trim(substr($param, 0, $pos));
            $name = trim(substr($param, $pos + 1));
            if (!$section) {
                $section = self::DEFAULT_SECTION;
            }
        } else {
            $name = $param;
        }
        return [$section, $name];
    }
    
    /**
     * Get section permissions.
     * @param string $section
     * @return array list of permission names
     */
    public static function getSectionPermissions($section = null)
    {
        $permissions = [];
        if (empty($section)) {
            $sections = static::getSections();
        } else {
            $sections = [$section];
        }
        
        foreach ($sections as $section) {
            $configs = static::getConfigsBySection($section);
            foreach ($configs as $config) {
                foreach ($config->perms as $perm) {
                    if (!in_array($perm, $permissions)) {
                        $permissions[] = $perm;
                    }
                }
            }
        }
        
        return $permissions;
    }
    
    /**
     * Check has user config access permissions.
     * @param Config $config
     * @return boolean
     */
    public static function isAccess(Config $config)
    {
        foreach ($config->perms as $permissionName) {
            if (Yii::$app->user->can($permissionName)) {
                return true;
            }
        }
        return false;
    }
}
