<?php

use app\base\Migration;
use app\models\Config;

class m180130_201810_param_site_name extends Migration
{

    public function up()
    {
        $config = new Config();
        $config->name = 'siteName';
        $config->title = 'Site name';
        $config->value = 'Admin template';
        $config->section = 'Site';
        $config->value_type = Config::TYPE_TEXT;
        $config->required = true;
        $config->save();
    }

    public function down()
    {
        Config::deleteAll(['name' => 'siteName']);
    }
}
