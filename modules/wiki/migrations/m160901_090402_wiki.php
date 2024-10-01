<?php

use app\base\Migration;

class m160901_090402_wiki extends Migration
{

    public $table = '{{%wiki}}';
    
    public function up()
    {
        $this->createTable($this->table, [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'title' => $this->string(255)->notNull(),
            'slug' => $this->string(255)->notNull()->defaultValue(''),
            'parent_id' => $this->integer(),
            'created_at' => $this->integer(),
        ]);
        $this->createIndex('idx_wiki_slug', $this->table, ['slug']);
        $this->createIndex('idx_wiki_parent', $this->table, ['parent_id']);
    }

    public function down()
    {
        $this->dropTable($this->table);
    }

}
