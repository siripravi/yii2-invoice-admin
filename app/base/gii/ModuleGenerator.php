<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @since 0.2
 */

namespace app\base\gii;

/**
 * Application module generator.
 *
 * Parameter moduleID is required only. Template 'app-module' used by default.
 * moduleClass parameter generated automatically from moduleID property.
 *
 * Use on command line:
 * ```
 * ./bin/yii gii/module --moduleID=my-orders
 * ```
 *
 * Will create module `MyOrdersModule` in `modules\\my_orders` folder.
 *
 * @author skoro
 */
class ModuleGenerator extends \yii\gii\generators\module\Generator
{
    /**
     * @var string the name of the code template that the user has selected.
     * The value of this property is internally managed by this class.
     */
    public $template = 'app-module';
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['moduleID', 'required'],
            ['moduleID', 'filter', 'filter' => 'trim'],
            ['moduleID', 'match', 'pattern' => '/^[a-zA-Z]+[a-zA-Z-_]+$/', 'message' => 'Only word characters and dashes are allowed.'],
            ['moduleID', 'validateModuleID'],
            
            ['template', 'required', 'message' => 'A code template must be selected.'],
            ['template', 'validateTemplate'],
        ];
    }
    
    /**
     * Not actual validation but class generator for moduleClass property.
     */
    public function validateModuleID()
    {
        $id = str_replace('-', '_', strtolower($this->moduleID));
        $class = ucwords(str_replace('_', ' ', $id)) . 'Module';
        $class = str_replace(' ', '', $class);
        $this->moduleClass = 'modules\\' . $id . '\\' . $class;
    }
}
