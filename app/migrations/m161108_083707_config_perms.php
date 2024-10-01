<?php

use app\base\Migration;
use app\models\Config;

class m161108_083707_config_perms extends Migration
{

    public $table = '{{%config}}';
    
    public function up()
    {
        $this->addColumn(Config::tableName(), 'perms', $this->binary()->defaultValue(
            serialize([
                'updateSettings',
            ])
        ));
    }

    public function down()
    {
        if ($this->isSqlite()) {
            echo '!!! SQLite does not support drop columns.'.PHP_EOL;
            return;
        }
        $this->dropColumn(Config::tableName(), 'perms');
    }

}
