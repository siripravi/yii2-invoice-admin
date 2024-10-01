<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @since 0.2
 */

namespace app\commands;

use app\base\console\Controller;
use Yii;
use yii\helpers\Console;

/**
 * SqlController
 *
 * @author skoro
 */
class SqlController extends Controller
{
    
    /**
     * Launch database SQL client on application's database.
     */
    public function actionIndex()
    {
        $db = Yii::$app->db;
        switch ($driver = $db->getDriverName()) {
            case 'sqlite':
                $file = preg_replace('/^sqlite:/', '', $db->dsn);
                $cmd = 'sqlite3 ' . Yii::getAlias($file);
                break;
            
            case 'mysql':
                if (!($dsn = $this->parseMysqlDsn())) {
                    $this->stderr('Cannot parse DSN: ' . $dsn, Console::FG_RED);
                    return 1;
                }
                $cmd = sprintf('mysql -u %s -p%s -h %s %s',
                    $dsn['user'],
                    $dsn['password'],
                    $dsn['host'],
                    $dsn['db']
                );
                break;
            
            default:
                $this->stderr('Not implemented for driver: ' . $driver, Console::FG_RED);
                return 1;
        }
        
        $this->procOpen($cmd);
    }
    
    /**
     * Generate dump of application database to stdout.
     */
    public function actionDump()
    {
        $db = Yii::$app->db;
        if ($db->getDriverName() !== 'mysql') {
            $this->stderr('Dump generation implemented only for MySQL yet.', Console::FG_RED);
            return 1;
        }
        
        if (!($dsn = $this->parseMysqlDsn())) {
            $this->stderr('Cannot parse DSN: ' . $dsn, Console::FG_RED);
            return 1;
        }
        
        $cmd = sprintf('mysqldump -u %s -p%s -h %s %s',
            $dsn['user'],
            $dsn['password'],
            $dsn['host'],
            $dsn['db']
        );
        
        $this->procOpen($cmd);
    }
    
    /**
     * Open process.
     * @param string $cmd command line
     * @return integer command exit status
     */
    protected function procOpen($cmd)
    {
        $process = proc_open($cmd, [0 => STDIN, 1 => STDOUT, 2 => STDERR], $pipes);
        $proc_status = proc_get_status($process);
        $exit_code = proc_close($process);
        return ($proc_status['running'] ? $exit_code : $proc_status['exitcode']);
    }
    
    /**
     * Parse MySQL dsn string.
     * @return array parsed elements: host, db, user, password.
     */
    protected function parseMysqlDsn()
    {
        $dsn = Yii::$app->db->dsn;
        if (!preg_match('/^mysql:host=(.*?);dbname=(.*)/', $dsn, $matches)) {
            return false;
        }
        return [
            'host' => $matches[1],
            'db' => $matches[2],
            'user' => Yii::$app->db->username,
            'password' => Yii::$app->db->password,
        ];
    }
}
