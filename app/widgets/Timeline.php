<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @version $Id$
 * @since 0.2
 */

namespace app\widgets;

use app\helpers\Icon;
use Closure;
use yii\helpers\Html;
use yii\widgets\ListView;

/**
 * AdminLTE timeline widget.
 *
 * @author skoro
 */
class Timeline extends ListView
{
    
    /**
     * @var Closure
     */
    public $dateValue;
    
    /**
     * @var Closure
     */
    public $iconView;
    
    /**
     * @var Closure
     */
    public $timeView;
    
    /**
     * @var Closure
     */
    public $itemHeaderView;
    
    /**
     * @var Closure
     */
    public $itemFooterView;
    
    /**
     * @var 
     */
    protected $_date;
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->_date = null;
        $this->layout = "{items}\n{pager}";
    }
    
    /**
     * @inheritdoc
     */
    public function renderItems()
    {
        $models = $this->dataProvider->getModels();
        $keys = $this->dataProvider->getKeys();
        $rows = [];
        foreach (array_values($models) as $index => $model) {
            $rows[] = $this->renderItem($model, $keys[$index], $index);
        }
        $rows[] = Html::tag('li', Icon::icon('fa fa-clock-o bg-gray'));
        
        return Html::tag('ul', implode($this->separator, $rows), ['class' => 'timeline']);
    }
    
    /**
     * Renders a single data model.
     * @param mixed $model the data model to be rendered
     * @param mixed $key the key value associated with the data model
     * @param integer $index the zero-based index of the data model in the model array returned by [[dataProvider]].
     * @return string the rendering result
     */
    public function renderItem($model, $key, $index)
    {
        $dateLabel = $this->renderDateLabel($model);
        $icon = $this->renderIcon($model);
        
        $time = $this->renderTime($model);
        $itemBody = Html::tag('div', parent::renderItem($model, $key, $index), [
            'class' => 'timeline-body',
        ]);
        $itemHeader = $this->renderItemHeader($model);
        $itemFooter = $this->renderItemFooter($model);
        $item = Html::tag('div', $time . $itemHeader . $itemBody . $itemFooter, [
            'class' => 'timeline-item',
        ]);
        
        return $dateLabel . Html::tag('li', $icon . $item);
    }
    
    protected function renderDateLabel($model)
    {
        $date = call_user_func($this->dateValue, $model);
        if ($this->_date == $date) {
            return '';
        }
        
        $this->_date = $date;
        $content = Html::tag('span', $date, [
            'class' => 'bg-green',
        ]);
        return Html::tag('li', $content, [
            'class' => 'time-label',
        ]);
    }
    
    protected function renderIcon($model)
    {
        if ($this->iconView instanceof Closure) {
            return call_user_func($this->iconView, $model);
        } elseif (is_string($this->iconView)) {
            return $this->iconView;
        }
        return Icon::icon('fa fa-user bg-aqua');
    }
    
    protected function renderTime($model)
    {
        if ($this->timeView instanceof Closure) {
            $time = call_user_func($this->timeView, $model);
        } elseif (is_string($this->timeView)) {
            $time = $this->timeView;
        } else {
            return '';
        }
        $time = Icon::icon('fa fa-clock-o') . ' ' . $time;
        return Html::tag('span', $time, ['class' => 'time']);
    }
    
    protected function renderItemHeader($model)
    {
        if ($this->itemHeaderView instanceof Closure) {
            $header = call_user_func($this->itemHeaderView, $model);
        } elseif (is_string($this->itemHeaderView)) {
            $header = $this->itemHeaderView;
        } else {
            return '';
        }
        return Html::tag('h3', $header, ['class' => 'timeline-header']);
    }
    
    protected function renderItemFooter($model)
    {
        if ($this->itemFooterView instanceof Closure) {
            $footer = call_user_func($this->itemFooterView, $model);
        } elseif (is_string($this->itemFooterView)) {
            $footer = $this->itemFooterView;
        } else {
            return '';
        }
        return Html::tag('div', $footer, ['class' => 'timeline-footer']);
    }
}
