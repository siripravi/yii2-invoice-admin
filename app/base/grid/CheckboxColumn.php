<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @since 0.3
 */

namespace app\base\grid;

use app\widgets\Check;
use yii\helpers\Json;

/**
 * Checkbox column with iChecks.
 *
 * @author skoro
 */
class CheckboxColumn extends \yii\grid\CheckboxColumn
{
    
    /**
     * @var string control style.
     */
    public $style = Check::STYLE_FLAT;
    
    /**
     * @var string style color.
     */
    public $color = Check::COLOR_GREEN;
    
    /**
     * @inheritdoc
     */
    public function registerClientScript()
    {
        $id = $this->grid->options['id'];
        $options = Json::encode([
            'name' => $this->name,
            'class' => $this->cssClass,
            'multiple' => $this->multiple,
            'checkAll' => $this->grid->showHeader ? $this->getHeaderCheckBoxName() : null,
            'checkboxClass' => Check::createStyleName(Check::TYPE_CHECKBOX, $this->style, $this->color),
        ]);
        //$this->grid->getView()->registerJs("jQuery('#$id input[type=checkbox').iCheck($options);");
        $this->grid->getView()->registerJs("Admin.Grid.initSelectionColumn('#$id', $options);");
    }
}
