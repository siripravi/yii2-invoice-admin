<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @since 0.1
 */

namespace app\base;

use Composer\Script\Event;
use yii\helpers\Console;

/**
 * Composer install hooks.
 *
 * @author skoro
 */
class Composer
{
    
    /**
     * Post create project composer hook.
     * @param \Composer\Script\Event $event
     */
    public static function postCreateProjectCmd(Event $event)
    {
        $params = $event->getComposer()->getPackage()->getExtra();
        if (isset($params[__METHOD__]) && is_array($params[__METHOD__])) {
            foreach ($params[__METHOD__] as $method => $args) {
                if (is_array($args)) {
                    call_user_func_array([__CLASS__, $method], $args);
                } else {
                    call_user_func([__CLASS__, $method], $args);
                }
            }
        }
    }
    
    /**
     * Create local configuration file.
     * @param string $from sample config filename
     * @param string $to local config filename
     * @throws \RuntimeException
     */
    public static function createLocalConfig($from, $to)
    {
        if (file_exists($to)) {
            echo "Config '$to' already exists, skipping...\n";
            return;
        }
        echo "Copy '$from' to '$to'...\n";
        if (!@copy($from, $to)) {
            throw new \RuntimeException("Cannot copy '$from' to '$to'.");
        }
    }
    
    /**
     * Generate unique cookie validation key.
     * @param string $config local configuration file name
     */
    public static function generateCookieValidationKey($config)
    {
        echo "Cookie validation key...\n";
        if (is_file($config)) {
            $key = self::generateRandomString();
            $content = preg_replace('/(("|\')cookieValidationKey("|\')\s*=>\s*)(""|\'\')/', "\\1'$key'", file_get_contents($config));
            file_put_contents($config, $content);
        } else {
            echo "Configuration file '$config' not exist.\n";
        }
    }
    
    /**
     * Sets the correct permission for the files and directories listed in the extra section.
     * @param array $paths the paths (keys) and the corresponding permission octal strings (values)
     */
    public static function setPermissions(array $paths)
    {
        foreach ($paths as $path => $permission) {
            echo "chmod('$path', $permission)...";
            if (is_dir($path) || is_file($path)) {
                try {
                    if (chmod($path, octdec($permission))) {
                        echo "done.\n";
                    };
                } catch (\Exception $e) {
                    echo $e->getMessage() . "\n";
                }
            } else {
                echo "file not found.\n";
            }
        }
        
    }
    
    /**
     * Create database configuration.
     * @param string $config local configuration file name
     */
    public static function setDatabaseConfiguration($config)
    {
        echo "Database configuration...\n";
        if (!file_exists($config)) {
            echo "Config '$to' not exists, skipping...\n";
            return;
        }
        
        $contents = file_get_contents($config);
        $pattern = '/("|\')db("|\')\s*=>\s*(\[.*?\])/ms';
        
        top:
        $type = Console::prompt('Database type: mysql, sqlite:', ['default' => 'mysql']);
        switch ($type) {
            case 'mysql':
                $host = Console::prompt('Host:', ['default' => 'localhost']);
                $db = Console::prompt('Database:', ['required' => true]);
                $user = Console::prompt('Username:', ['required' => true]);
                $pass = Console::prompt('Password:', ['required' => true]);
                $contents = preg_replace_callback($pattern, function ($match) use ($host, $db, $user, $pass) {
                    $dsn = "mysql:host=$host;dbname=$db";
                    return "'db' => [\n\t\t'class' => 'yii\db\Connection',\n\t\t'dsn' => '$dsn',\n\t\t'username' => '$user',\n\t\t'password' => '$pass',\n\t\t'charset' => 'utf8',\n\t]";
                }, $contents);
                break;
            
            case 'sqlite':
                $db = Console::prompt('Database file:', ['required' => true, 'default' => '@runtime/db.sq3']);
                $contents = preg_replace_callback($pattern, function ($match) use ($db) {
                    return "'db' => [\n\t\t'class' => 'yii\db\Connection',\n\t\t'dsn' => 'sqlite:$db',\n\t]";
                }, $contents);
                break;
            
            default:
                goto top;
        }
        
        file_put_contents($config, $contents);
        
        echo "\nDone. Now you must import database migrate data by execute following command:\n";
        echo "\n\t\tcd <to-your-project-dir>";
        echo "\n\t\t./bin/yii migrate\n\n";
        
        if ($type === 'sqlite') {
            echo "\n\nNote for SQLite users: after migrating data don't forget to set write permissions to your database file: chmod 777 $db\n\n";
        }
    }
    
    protected static function generateRandomString()
    {
        if (!extension_loaded('openssl')) {
            throw new \Exception('The OpenSSL PHP extension is required by Yii2.');
        }
        $length = 32;
        $bytes = openssl_random_pseudo_bytes($length);
        return strtr(substr(base64_encode($bytes), 0, $length), '+/=', '_-.');
    }
}
