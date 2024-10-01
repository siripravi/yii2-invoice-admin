<?php

use app\base\Migration;
use app\models\User;

class m160314_212231_user extends Migration
{
    
    public function up()
    {
        $this->createTable(User::tableName(), [
            'id' => $this->primaryKey(),
            'name' => $this->string(64)->notNull()->unique(),
            'email' => $this->string(64)->unique(),
            'password_hash' => $this->string()->notNull(),
            'reset_token' => $this->string()->notNull()->defaultValue(''),
            'activate_token' => $this->string()->notNull()->defaultValue(''),
            'auth_key' => $this->string()->notNull()->unique(),
            'status' => $this->smallInteger()->unsigned()->defaultValue(0),
            'created_at' => $this->integer()->unsigned(),
            'logged_at' => $this->integer()->unsigned(),
        ]);
        
        $this->createIndex('idx_user_reset_token', User::tableName(), 'reset_token');
        $this->createIndex('idx_user_activate_token', User::tableName(), 'activate_token');
    }

    public function down()
    {
        $this->dropTable(User::tableName());
    }

}
