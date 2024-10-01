<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @version $Id$
 */

namespace app\widgets;

use yii\bootstrap5\Widget;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/**
 * AdminLTE items list widget.
 *
 * @author skoro
 */
class ItemList extends Widget
{
    
    /**
     * @var array
     */
    public $items = [];
    
    /**
     * @inheritdoc
     */
    public function run()
    {
        $items = array_map(function ($item) {
            $options = ArrayHelper::getValue($item, 'options', []);
            Html::addCssClass($options, 'list-group-item');
            $valueOptions = ArrayHelper::getValue($item, 'valueOptions', []);
            Html::addCssClass($valueOptions, 'pull-right');
            $content = $item['title'] . Html::tag('span', $item['value'], $valueOptions);
            return Html::tag('li', $content, $options);
        }, $this->items);
        
        $options = $this->options;
        Html::addCssClass($options, 'list-group list-group-unbordered');
        
        return Html::tag('ul', implode("\n", $items), $options);
    }
}
