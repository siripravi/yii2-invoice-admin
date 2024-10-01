<?php

use app\base\Migration;

class m160831_094735_module extends Migration
{

    public $table = '{{%module}}';
    
    public function up()
    {
        $this->createTable($this->table, [
            'module_id' => $this->string(32)->notNull()->unique(),
            'name' => $this->string(255)->notNull()->defaultValue(''),
            'installed' => $this->boolean()->notNull()->defaultValue(false),
            'desc' => $this->text(),
            'data' => $this->text(),
        ]);
        $this->createIndex('idx_module_status', $this->table, ['installed']);
    }

    public function down()
    {
        $this->dropTable($this->table);
        echo "\n\nWARNING!\n";
        echo "Module is essential part of application and application cannot work without module table.\n";
    }

}
