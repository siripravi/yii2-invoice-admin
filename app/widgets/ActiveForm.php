<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @version $Id$
 */

namespace app\widgets;

use Yii;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/**
 * ActiveForm
 *
 * @author skoro
 */
class ActiveForm extends \yii\bootstrap5\ActiveForm
{
    
    /**
     * @var boolean on/off form's data-pjax attribute.
     */
    public $pjax = false;
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        if ($this->pjax) {
            $this->options['data-pjax'] = 1;
        }
        parent::init();
    }
    
    /**
     * End form with action buttons: submit and cancel.
     * 
     * @todo allow to add more than two internal buttons.
     * @param array $options button options.
     */
    public static function endWithActions(array $options = [])
    {
        $defaults = [
            'options' => ['class' => 'actions'],
            'save' => [
                'label' => Yii::t('app', 'Save'),
                'options' => ['class' => 'btn btn-primary btn-flat'],
            ],
            'cancel' => [
                'label' => Yii::t('app', 'Cancel'),
                'options' => ['class' => 'btn btn-warning btn-flat pull-right'],
            ],
        ];
        $options = ArrayHelper::merge($defaults, $options);
        $buttons = '';
        if (!empty($options['save'])) {
            $buttons .= Html::submitButton(
                    ArrayHelper::getValue($options, 'save.label'),
                    ArrayHelper::getValue($options, 'save.options', [])
            );
        }
        if (!empty($options['cancel'])) {
            if (!empty($options['cancel']['url'])) {
                $buttons .= Html::a(
                    ArrayHelper::getValue($options, 'cancel.label'),
                    $options['cancel']['url'],
                    ArrayHelper::getValue($options, 'cancel.options', [])
                );
            } else {
                $buttons .= Html::button(
                        ArrayHelper::getValue($options, 'cancel.label'),
                        ArrayHelper::getValue($options, 'cancel.options', [])
                );
            }
        }
        echo Html::tag('div', $buttons, $options['options']);
        static::end();
    }
    
}
