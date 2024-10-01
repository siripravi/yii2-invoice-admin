<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @since 0.2
 */

namespace app\base\actions;

use app\base\Action;
use app\base\Controller;
use app\components\Param;
use app\models\Config;
use app\widgets\ActiveForm;
use app\widgets\Check;
use app\widgets\Pjax;
use app\widgets\Select2;
use app\widgets\Tabs;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\ForbiddenHttpException;

/**
 * Site settings action.
 *
 * Build a page with site settings.
 * Include in your controller actions following:
 * ```php
 * public function actions() {
 *    return [
 *        ....
 *        'settings' => [
 *            'class' => 'app\base\actions\Settings',
 *        ],
 *        ....
 *    ];
 * }
 * ```
 *
 * @author skoro
 */
class Settings extends Action
{
    
    /**
     * @var string view title.
     */
    public $title;
    
    /**
     * @var array
     */
    public $tabsOptions = [];
    
    /**
     * @var boolean use pjax for update settings tabs.
     */
    public $pjax = true;
    
    /**
     * @var array
     */
    public $pjaxOptions = [];
    
    /**
     * @var string current active tab.
     */
    protected $_tab;
    
    /**
     * @var array
     */
    protected $_configs = [];
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if (!$this->title) {
            $this->title = Yii::t('app', 'Site settings');
        }
    }
    
    /**
     * @inheritdoc
     */
    public function run($tab = '')
    {
        if (!$this->checkSectionAccess($tab)) {
            throw new ForbiddenHttpException();
        }
        
        $this->controller->getView()->title = $this->title;
        $this->_tab = $tab;
        
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $section = ArrayHelper::getValue($post, 'section', Param::DEFAULT_SECTION);
            
            /** @var $configs Config[] */
            $configs = Param::getConfigsBySection($section);
            if (Config::loadMultiple($configs, $post)) {
                Config::validateMultiple($configs);
                
                /** @var $config Config */
                foreach ($configs as $config) {
                    $isDirty = $config->getDirtyAttributes(['value']);
                    if (!$config->getErrors() && $isDirty && Param::isAccess($config)
                            && $config->save(false, ['value'])) {
                        $this->controller->addFlash(
                            Controller::FLASH_SUCCESS,
                            Yii::t('app', '<b>{title}</b> updated.', [
                                'title' => $config->title,
                            ])
                        );
                    }
                }
                
                $this->_configs[$section] = $configs;
            }
        }
        
        $this->tabsOptions['items'] = $this->renderTabs();
        return $this->controller->renderContent(Tabs::widget($this->tabsOptions));
    }
    
    /**
     * Renders items for Tabs widget.
     * @return string
     */
    protected function renderTabs()
    {
        $tabs = [];
        $sections = Param::getSections();
        foreach ($sections as $section) {
            if (!$this->checkSectionAccess($section)) {
                continue;
            }
            $tabs[] = [
                'label' => $section,
                'content' => $this->renderSection($section),
                'active' => $this->_tab == $section,
            ];
        }
        return $tabs;
    }
    
    /**
     * Renders tab section content.
     * @param string $section
     * @return string
     */
    protected function renderSection($section)
    {
        $configs = isset($this->_configs[$section]) ?
                $this->_configs[$section] : Param::getConfigsBySection($section);
        
        ob_start();
        ob_implicit_flush(false);
        
        if ($this->pjax) {
            Pjax::begin($this->pjaxOptions);
        }
        
        $form = ActiveForm::begin([
            'action' => [$this->id, 'tab' => $section],
            'pjax' => $this->pjax,
        ]);
        
        echo Html::hiddenInput('section', $section);
        
        foreach ($configs as $config) {
            
            if (!Param::isAccess($config)) {
                continue;
            }
            
            $title = Yii::t('app', $config->title);
            $field = $form->field($config, "[{$config->id}]value")->hint(Yii::t('app', $config->desc));
            
            switch ($config->value_type) {
                case Config::TYPE_TEXT:
                case Config::TYPE_URL:
                case Config::TYPE_EMAIL:
                case Config::TYPE_INT:
                case Config::TYPE_NUM:
                    echo $field->textInput()->label($title);
                    break;
                
                case Config::TYPE_EDITOR:
                    echo $field->textArea()->label($title);
                    break;
                
                case Config::TYPE_SWITCH:
                    echo $field->widget(Check::className(), [
                        'label' => $title,
                    ])->label(false);
                    break;
                
                case Config::TYPE_SELECT:
                    echo $field->widget(Select2::className(), [
                        'items' => $config->options,
                    ])->label($title);
                    break;
                
                case Config::TYPE_PASSWORD:
                    echo $field->passwordInput()->label($title);
                    break;
            }
        }
        
        ActiveForm::endWithActions([
            'cancel' => false,
        ]);
        
        if ($this->pjax) {
            Pjax::end();
        }
        
        return ob_get_clean();
    }
    
    /**
     * Check current user's permission for section.
     * @param string $section
     * @return boolean
     */
    protected function checkSectionAccess($section = '')
    {
        $permissions = Param::getSectionPermissions($section);
        foreach ($permissions as $permName) {
            if (Yii::$app->user->can($permName)) {
                return true;
            }
        }
        return false;
    }
}
