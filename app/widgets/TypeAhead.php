<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @version $Id$
 */

namespace app\widgets;

use yii\bootstrap5\InputWidget;
use yii\gii\TypeAheadAsset;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;

/**
 * Bootstrap TypeAhead widget.
 * 
 * In view:
 * ```php
 * echo TypeAhead::widget([
 *  'model' => $model,
 *  'attribute' => $name,
 *  'remote' => ['autocomplete', 'add' => 'that is additional parameter passed to action'],
 * ]);
 * ```
 * 
 * In controller:
 * ```php
 * public function actionAutocomplete($add)
 * {
 *      Yii::$app->response->format = Response::FORMAT_JSON;
 *      return [
 *          ['value' => 'Apple'],
 *          ['value' => 'Orange'],
 *          ['value' => 'Cherry'],
 *      ];
 * }
 * ```
 *
 * @author skoro
 */
class TypeAhead extends InputWidget
{
    
    /**
     * @var string|array fetch item from the remote source.
     */
    public $remote;
    
    /**
     * @var array
     */
    public $options = ['class' => 'form-control'];

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->registerPlugin('typeahead');
        if ($this->hasModel()) {
            return Html::activeTextInput($this->model, $this->attribute, $this->options);
        } else {
            return Html::textInput($this->name, $this->value, $this->options);
        }
    }
    
    protected function getDataSource()
    {
        $name = 'ds-' . $this->options['id'];
        $source = 'ds_' . str_replace('-', '_', $this->options['id']);
        $this->remote['q'] = '__QUERY';
        $url = Url::to($this->remote);
        
        $js = <<<JS
            var $source = new Bloodhound({
                datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
                queryTokenizer: Bloodhound.tokenizers.whitespace,
                remote: {
                    url: '$url',
                    wildcard: '__QUERY'
                }
            });
JS;
        $this->getView()->registerJs($js, View::POS_READY);
        
        return [
            'name' => $name,
            'display' => 'value',
            'source' => new JsExpression($source),
        ];
    }

    /**
     * @inheritdoc
     */
    protected function registerPlugin($name)
    {
        $view = $this->getView();

        TypeAheadAsset::register($view);

        $id = $this->options['id'];

        if ($this->clientOptions !== false) {
            $options = empty($this->clientOptions) ? 'null' : Json::htmlEncode($this->clientOptions);
            $dataSource = Json::htmlEncode($this->getDataSource());
            $js = "jQuery('#$id').$name($options, $dataSource);";
            $view->registerJs($js);
        }

        $this->registerClientEvents();
    }
}
