<?php
/**
 * @author Skorobogatko Oleksii <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @since 0.1
 */

namespace app\base;

/**
 * Migration
 *
 * @author skoro
 */
class Migration extends \yii\db\Migration
{
    /**
     * @var string default database table options.
     */
    protected $tableOptions = null;
    
    /**
     * Is MS SQL Server database driver used ?
     * @return bool
     */
    protected function isMSSQL()
    {
        return $this->db->driverName === 'mssql' || $this->db->driverName === 'sqlsrv' || $this->db->driverName === 'dblib';
    }
    
    /**
     * Is SQLite database driver used ?
     * @return bool
     */
    protected function isSqlite()
    {
        return $this->db->driverName === 'sqlite';
    }

    /**
     * Get specific table options for database driver.
     * @return string
     */
    protected function getTableOptions()
    {
        if ($this->tableOptions === null) {
            switch ($this->db->driverName) {
                case 'mysql':
                    // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
                    $this->tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci';
                    break;
                
                default:
                    $this->tableOptions = '';
            }
        }
        
        return $this->tableOptions;
    }
    
    /**
     * @inheritdoc
     */
    public function createTable($table, $columns, $options = null)
    {
        parent::createTable($table, $columns, $options === null ? $this->getTableOptions() : $options);
    }
}
