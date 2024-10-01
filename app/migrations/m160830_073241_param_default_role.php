<?php

use app\base\Migration;

class m160830_073241_param_default_role extends Migration
{

    public $table = '{{%config}}';
    
    public function up()
    {
        $roles = yii\helpers\ArrayHelper::map(Yii::$app->authManager->getRoles(), 'name', 'name');
        $columns = [
            'section' => 'User',
            'name' => 'defaultRole',
            'title' => 'Default user role',
            'value' => serialize('Registered'),
            'value_type' => 'select',
            'options' => serialize($roles),
            'desc' => 'Assign newly registered users to specified role.',
        ];
        Yii::$app->db->createCommand()
                ->insert($this->table, $columns)
                ->execute();
    }

    public function down()
    {
        Yii::$app->db->createCommand()
                ->delete($this->table, [
                    'section' => 'User',
                    'name' => 'defaultRole'
                ])
                ->execute();
    }

}
