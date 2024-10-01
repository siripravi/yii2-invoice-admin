<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @since 0.2
 */

namespace app\base;

/**
 * Exception occurs when module migrations failed (up/down).
 * @author skoro
 */
class ModuleMigrateException extends \Exception
{
    /**
     * @var string
     */
    public $moduleId;
    
    /**
     * @var array
     */
    public $migrations;
    
    /**
     * @var string migration command output.
     */
    public $output = '';
    
    /**
     * @param string $moduleId
     * @param array $migrations list of failed migrations.
     * @param string $output migration command output
     */
    public function __construct($moduleId, array $migrations, $output = '') {
        $this->moduleId = $moduleId;
        $this->migrations = $migrations;
        $this->output = $output;
        parent::__construct('Cannot apply module migrations.');
    }
}
