<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @version $Id$
 */

namespace app\widgets;

use Yii;
use yii\bootstrap5\Widget;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\helpers\Icon;

/**
 * AdminLTE box widget.
 * 
 * ```php
 * echo Box::begin([
 *  'label' => 'User profile',
 *  'box' => BOX_SUCCESS,
 *  'solid' => true,
 * ]);
 *      echo 'Inside the box!';
 * echo Box::end();
 * ```
 * 
 * Box contents can be fetched by url. In the next example, box initialized
 * with loading spinner and send ajax get request to 'page/content' action:
 * ```php
 * echo Box::begin([
 *  'loading' => ['page/content'],
 * ]);
 *      echo 'Please wait, content is loading...';
 * echo Box::end();
 * ```
 *
 * @author skoro
 * @todo remember box state: collapsed or expanded via cookie.
 */
class Box extends Widget
{
    
    /**
     * Box styles.
     */
    const BOX_PRIMARY = 'box-primary';
    const BOX_SUCCESS = 'box-success';
    const BOX_WARNING = 'box-warning';
    const BOX_DANGER = 'box-danger';
    const BOX_DEFAULT = 'box-default';
    
    /**
     * @var string box style, see BOX_* constants.
     */
    public $box = self::BOX_DEFAULT;
    
    /**
     * @var string box label.
     */
    public $label;
    
    /**
     * @var boolean draw bottom line under label.
     */
    public $withBorder = true;
    
    /**
     * @var boolean fill header by selected box style (BOX_*).
     */
    public $solid = false;
    
    /**
     * @var array
     */
    public $labelOptions = [];
    
    /**
     * @var boolean
     */
    public $encodeLabel = true;
    
    /**
     * @var array tool container options.
     */
    public $toolOptions = [];
    
    /**
     * @var array
     */
    public $toolButtonOptions = ['class' => 'btn btn-box-tool'];
    
    /**
     * @var boolean initial box state is collapsed.
     */
    public $expandable = false;
    
    /**
     * @var boolean can box collapsed or not ?
     */
    public $collapsable = false;
    
    /**
     * @var boolean add remove tool button.
     */
    public $removable = false;
    
    /**
     * @var array header options.
     */
    public $headerOptions = [];
    
    /**
     * @var array box body options.
     */
    public $bodyOptions = [];
    
    /**
     * @var boolean|array if true box initialized with loading spinner only,
     * if array then additionaly to spinner that array treated as Url::to() 
     * parameter and sends request to that url and box renders by fetched 
     * content.
     */
    public $loading = false;
    
    /**
     * @var string error message when fetch content failed.
     */
    public $loadingError = 'Couldn\'t load content.';
    
    /**
     * @var array list of box actions. Each action accepts following keys:
     * - 'label' tooltip label
     * - 'icon' tool icon
     * - 'visible' toggle tool visibility
     * - 'options'
     * or 'value' key which renders action item.
     */
    public $actions = [];
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->initCssClasses();
        $this->renderBox();
    }
    
    protected function initCssClasses()
    {
        Html::addCssClass($this->options, 'box');
        Html::addCssClass($this->labelOptions, 'box-title');
        Html::addCssClass($this->bodyOptions, 'box-body');
        Html::addCssClass($this->headerOptions, 'box-header');
    }
    
    /**
     * Render box.
     */
    protected function renderBox()
    {
        if ($this->solid) {
            Html::addCssClass($this->options, 'box-solid');
        }
        if ($this->box) {
            Html::addCssClass($this->options, $this->box);
        }
        $header = $this->renderHeader();
        echo Html::beginTag('div', $this->options);
        echo $header;
        echo Html::beginTag('div', $this->bodyOptions);
    }
    
    /**
     * Renders the widget.
     */
    public function run()
    {
        echo Html::endTag('div'); // box-body
        $this->renderLoading();
        echo Html::endTag('div'); // box
    }
    
    /**
     * Renders box header.
     * @return string
     */
    protected function renderHeader()
    {
        $header = '';
        if (!empty($this->label)) {
            $label = $this->encodeLabel ? Html::encode($this->label) : $this->label;
            if ($this->withBorder) {
                Html::addCssClass($this->headerOptions, 'with-border');
            }
            $header .= Html::tag('h3', $label, $this->labelOptions);
        }
        $header .= $this->renderTools();
        return $header ? Html::tag('div', $header, $this->headerOptions) : '';
    }
    
    /**
     * @return string
     */
    protected function renderTools()
    {
        $tools = $this->renderActions();
        $options = $this->toolButtonOptions;
        if ($this->expandable) {
            Html::addCssClass($this->options, 'collapsed-box');
            $options['data-widget'] = 'collapse';
            $tools .= Html::button(Icon::icon('fa fa-plus'), $options);
        }
        elseif ($this->collapsable) {
            $options['data-widget'] = 'collapse';
            $tools .= Html::button(Icon::icon('fa fa-minus'), $options);
        }
        if ($this->removable) {
            $options['data-widget'] = 'remove';
            $tools .= Html::button(Icon::icon('fa fa-times'), $options);
        }
        if ($tools) {
            Html::addCssClass($this->toolOptions, ['box-tools', 'pull-right']);
            $tools = Html::tag('div', $tools, $this->toolOptions);
        }
        return $tools;
    }
    
    /**
     * Render box action buttons.
     * @return string
     */
    protected function renderActions()
    {
        $actions = '';
        foreach ($this->actions as $action) {
            if (isset($action['visible']) && !$action['visible']) {
                continue;
            }
            if (isset($action['value'])) {
                if ($action['value'] instanceof \Closure) {
                    $actions .= call_user_func($action['value']);
                } else {
                    $actions .= $action['value'];
                }
            } else {
                $options = $this->toolButtonOptions;
                if (isset($action['label'])) {
                    $options['data-original-title'] = $action['label'];
                    $options['data-toggle'] = 'tooltip';
                }
                if (isset($action['options']['class'])) {
                    Html::addCssClass($options, $action['options']['class']);
                    ArrayHelper::remove($action['options'], 'class');
                }
                $options = ArrayHelper::merge($options, ArrayHelper::getValue($action, 'options', []));
                $actions .= Html::button(
                    Icon::icon(ArrayHelper::getValue($action, 'icon', 'fa fa-cog')),
                    $options
                );
            }
        }
        return $actions;
    }
    
    /**
     * Render loading spinner and fetch content.
     */
    protected function renderLoading()
    {
        if ($this->loading) {
            echo Html::tag('div', Icon::icon('fa fa-refresh fa-spin'), ['class' => 'overlay']);
        }
        if (is_array($this->loading)) {
            $id = $this->getId();
            $url = Url::to($this->loading);
            $error = addcslashes($this->loadingError, "'");
            $js = "jQuery('#$id .box-body').load('$url', function (response, status) {
                if (status === 'error') { $(this).html('$error'); }
                jQuery('#$id .overlay').remove();
            });";
            $this->getView()->registerJs($js);
        }
    }
    
}
