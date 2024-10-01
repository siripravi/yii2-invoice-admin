<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@soft-industry.com>
 * @copyright 2016
 * @since 0.1
 */

namespace app\base\grid;

use app\helpers\Icon;
use Yii;
use yii\grid\Column;
use yii\helpers\Html;
use app\base\ModelUrlTrait;

/**
 * EditColumn
 *
 * @author skoro
 */
class EditColumn extends Column
{
    
    use ModelUrlTrait;
    
    /**
     * @var array url options.
     */
    public $options = [];
    
    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        $url = $this->getModelUrl($model, ['update']);
        return Html::a(Icon::EDIT, $url, $this->options);
    }
    
    /**
     * @inheritdoc
     */
    protected function getHeaderCellLabel()
    {
        return Yii::t('app', 'Edit');
    }
}
