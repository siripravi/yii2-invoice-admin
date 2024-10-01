<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @version $Id$
 */

namespace app\widgets;

use Yii;
use yii\widgets\InputWidget;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;
use app\assets\Select2Asset;

/**
 * Select2 widget.
 *
 * @author skoro
 */
class Select2 extends InputWidget
{
    
    /**
     * @var array
     */
    public $items = [];
    
    /**
     * @var array
     */
    public $clientOptions = [];
    
    /**
     * @var array
     */
    public $clientEvents = [];
    
    /**
     * @var string
     */
    public $language;
    
    /**
     * @var array remote data url.
     */
    public $remote;
    
    /**
     * @var array
     */
    public $remoteOptions = [];
    
    /**
     * @var boolean adds 'style=width: 100%' to select element.
     */
    public $fullWidth = true;
    
    /**
     * @var boolean hides search box.
     */
    public $hideSearch = true;
    
    /**
     * @var boolean|string prepend items with empty element.
     */
    public $empty = false;
    
    /**
     * @var boolean allow multiple values selection.
     */
    public $multiple = false;
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        // Manual setting element ID.
        if ($this->id) {
            $this->options['id'] = $this->id;
        }
        parent::init();
    }
    
    /**
     * @inheritdoc
     */
    public function run()
    {
        if ($this->empty !== false) {
            // Empty item must be first.
            $items = $this->items;
            $this->items = [
                '' => is_string($this->empty) ? $this->empty : Yii::t('app', 'None'),
            ] + $items;
        }
        
        if ($this->fullWidth) {
            Html::addCssStyle($this->options, 'width: 100%');
        }
        if ($this->hideSearch) {
            $this->clientOptions['minimumResultsForSearch'] = 'Infinity';
        }
        if ($this->multiple) {
            $this->options['multiple'] = true;
        }
        
        $this->registerClientScript();
        
        if ($this->hasModel()) {
            return Html::activeDropDownList($this->model, $this->attribute, $this->items, $this->options);
        } else {
            return Html::dropDownList($this->name, $this->value, $this->items, $this->options);
        }
    }
    
    /**
     * Register Select2 scripts.
     */
    protected function registerClientScript()
    {
        $clientOptions = array_merge($this->clientOptions, $this->remoteClientOptions());
        $clientOptions = Json::encode($clientOptions);
        $view = $this->getView();
        $id = isset($this->options['id']) ? $this->options['id'] : $this->getId();

        $view->registerJs("jQuery('#$id').select2($clientOptions);");
        
        if ($this->clientEvents) {
            $js = [];
            foreach ($this->clientEvents as $event => $callback) {
                if (!$callback instanceof JsExpression) {
                    $callback = new JsExpression($callback);
                }
                $js[] = "jQuery('#$id').on('$event', $callback);";
            }
            if (!empty($js)) {
                $js = implode("\n", $js);
                $view->registerJs($js);
            }
        }
        
        $asset = Select2Asset::register($view);
        if ($this->language) {
            $asset->language = $this->language;
        }
    }
    
    /**
     * Default client options for remote data.
     * @return array
     */
    protected function remoteClientOptions()
    {
        if ($this->remote) {
            $options = [
                'url' => Url::to($this->remote),
                'delay' => 500,
                'dataType' => 'json',
                'data' => new JsExpression('function (params) {
                    return {
                        q: params.term,
                    };
                }'),
                'processResults' => new JsExpression('function (data, params) {
                    return { results: data };
                }'),
                'cache' => true,
            ];

            return ['ajax' => array_merge($options, $this->remoteOptions)];
        }
        return [];
    }
    
}
