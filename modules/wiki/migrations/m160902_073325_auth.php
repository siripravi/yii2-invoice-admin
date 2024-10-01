<?php


class m160902_073325_auth extends \app\base\RbacMigration
{
    
    public $rbac = [
        'permissions' => [
            'createWiki' => 'Create a wiki',
            'updateOwnWiki' => [
                'description' => 'Update only own wiki',
                'rule' => 'modules\wiki\RuleOwnWiki',
                'child' => 'updateWiki',
            ],
            'updateWiki' => 'Update any wiki page',
            'viewWiki' => 'View wiki',
            'viewWikiHistory' => 'View wiki changes history',
            'deleteOwnWiki' => [
                'description' => 'Delete only own wiki',
                'rule' => 'modules\wiki\RuleOwnWiki',
                'child' => 'deleteWiki',
            ],
            'deleteWiki' => 'Delete any wiki page',
        ],
        'roles' => [
            'WikiEditor' => ['createWiki', 'viewWiki', 'viewWikiHistory', 'updateOwnWiki', 'deleteOwnWiki'],
            'WikiAdmin' => ['WikiEditor', 'updateWiki', 'deleteWiki'],
        ],
    ];
}
