<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @since 0.1
 */

namespace app\base\grid;

use app\helpers\Icon;
use Closure;
use Yii;
use yii\grid\Column;
use yii\helpers\Html;
use app\base\ModelUrlTrait;

/**
 * DeleteColumn
 *
 * @author skoro
 */
class DeleteColumn extends Column
{
    
    use ModelUrlTrait;
    
    /**
     * @var string|Closure
     */
    public $confirm;
    
    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        if ($this->confirm instanceof Closure) {
            $confirm = call_user_func($this->confirm, $model);
        }
        elseif (is_string($this->confirm)) {
            $confirm = $this->confirm;
        }
        else {
            $confirm = Yii::t('app', 'Are you sure ?');
        }
        $confirm = Html::encode($confirm);
        
        $url = $this->getModelUrl($model, ['delete']);
        
        return Html::a(Icon::TRASH, $url, ['class' => 'confirm', 'data-confirm' => $confirm]);
    }
    
    /**
     * @inheritdoc
     */
    protected function getHeaderCellLabel()
    {
        return Yii::t('app', 'Delete');
    }
}
