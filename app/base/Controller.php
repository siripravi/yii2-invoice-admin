<?php
/**
 * @author Skorobogatko Oleksii <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @since 0.1
 */

namespace app\base;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * Controller
 *
 * @author skoro
 */
class Controller extends \yii\web\Controller
{

    /**
     * Flash keys used by Controller::addFlash().
     */
    const FLASH_ERROR = 'error';
    const FLASH_SUCCESS = 'success';
    const FLASH_DANGER = 'danger';
    const FLASH_INFO = 'info';
    const FLASH_WARNING = 'warning';
    
    /**
     * Confirmation background css.
     */
    const CONFIRM_INFO = 'bg-aqua';
    const CONFIRM_SUCCESS = 'bg-green';
    const CONFIRM_WARNING = 'bg-yellow';
    const CONFIRM_DANGER = 'bg-red';
    
    /**
     * @var string when application sidebar is collapsed this contains collapsed css class.
     */
    protected $_sidebarCollapsed;
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->_sidebarCollapsed = $this->getSidebarState();
    }
    
    /**
     * @see Session::addFlash()
     */
    public function addFlash($key, $value, $removeAfterAccess = true)
    {
        Yii::$app->getSession()->addFlash($key, $value, $removeAfterAccess);
    }
    
    /**
     * @inheritdoc
     */
    public function renderContent($content)
    {
        $_view = $this->getView();
        $_view->params['sidebarCollapsed'] = $this->_sidebarCollapsed;
        return parent::renderContent($content);
    }
    
    /**
     * Gets sidebar collapsed state.
     * If sidebar is collapsed it returns collapsed css class.
     * @return string
     */
    protected function getSidebarState()
    {
        // Yii loads only crypted cookies on request, so we must use global COOKIE.  
        if (isset($_COOKIE['SidebarPushMenu']) && $_COOKIE['SidebarPushMenu'] === 'collapsed') {
            return 'sidebar-collapse';
        }
        return '';
    }
    
    /**
     * Finds a model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $class model class name with namespace.
     * @param integer $id
     * @return ActiveRecord
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function findModel($class, $id)
    {
        $model = call_user_func([$class, 'findOne'], $id);
        if (!$model) {
            $this->raise404();
        }
        return $model;
    }
    
    /**
     * Raise "Page not found" exception.
     * @param string $message
     * @throws NotFoundHttpException
     */
    public function raise404($message = 'The requested page does not exist.')
    {
        throw new NotFoundHttpException($message);
    }
    
    /**
     * Update a model based on POST request.
     * @param Model $model
     * @return boolean returns true when model validated and saved.
     */
    public function updateModel($model)
    {
        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }
        if ($this->isBulkAction()) {
            $bulkAction = Yii::$app->request->post('bulk');
            $method = 'bulk' . ucfirst($action->actionMethod);
            if (method_exists($this, $method)) {
                if ($this->{$method}($bulkAction) === false) {
                    return false;
                }
            }
        }
        return true;
    }
    
    /**
     * Is bulk request ?
     * @return boolean
     */
    public function isBulkAction()
    {
        $request = Yii::$app->request;
        return $request->isPost && $request->post('bulk') && $request->post('selection');
    }
    
    /**
     * Load bulk models.
     * @param string $className model class name.
     * @param array $ids Optional. Model IDs or if missed IDs get from bulk request.
     * @return Model[]
     */
    public function getBulkModels($className, array $ids = [])
    {
        if (empty($ids) && $this->isBulkAction()) {
            $ids = Yii::$app->request->post('selection');
        }
        return $className::find()->where(['id' => $ids])->all();
    }
    
    /**
     * Create a confirmation view.
     * @param array $options accepts following options:
     * title - confirmation page title
     * message - confirmation text
     * encodeMessage - whether to encode confirmation text
     * actionUrl - confirmation post url (by default, current action)
     * button - confirmation action button
     * cancelUrl - cancel url (by default, controller's index)
     * params - additional form params
     * icon - css class(es) for icon
     * background - confirmation background css class (see CONFIRM_ consts)
     * @return string
     */
    public function createConfirmation(array $options)
    {
        $defaults = [
            'actionUrl' => [$this->action->id],
            'cancelUrl' => ['index'],
            'encodeMessage' => true,
            'params' => [],
            'icon' => '',
            'background' => static::CONFIRM_DANGER,
        ];
        
        return $this->render('//templates/confirmation', ArrayHelper::merge($defaults, $options));
    }
}
