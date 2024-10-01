What is modules
---------------

Modules extend your site functionality beyond core.

Place custom modules in this directory. It's better to use modules to 
extend your backend than direct change core files (files in `app` directory).

How to develop modules
----------------------

Use console gii command:

```
./bin/yii gii/module --moduleID=pages
```

This command will create `pages` directory under your project root `modules`
directory.
Inside `pages` directory is base module skeleton. Main module class
is PagesModule. Please take attention to following properties in that class:

```
public $moduleName = '';
public $moduleDescription = '';
```

These properties give information for others users/developers what module do.

To create migration for `pages` module execute following command:

```
./bin/yii migrate/create --migrationPath=@modules/pages/migrations schema
```

Module management
-----------------

Yii's command line tool provide `module` command with followin subcommands:
- index (by default)
- install
- uninstall
- info

Command `module/index` or just `module` will show list of all available
modules (installed and not installed).

`module/install` will install a module. This command requires module id
argument. You can get module id from output from the `module` command.
`module/uninstall` will uninstall a module and requires module id also.

`info` command provides module information such: readable name, description,
module status (installed/not installed) and contained migrations.
