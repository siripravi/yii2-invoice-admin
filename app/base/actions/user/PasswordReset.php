<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @since 0.1
 */

namespace app\base\actions\user;

use Yii;
use app\base\Action;
use app\base\Controller;

/**
 * User password reset.
 *
 * @author skoro
 */
class PasswordReset extends Action
{
    /**
     * @var string class name for Login form.
     */
    public $modelClass = 'app\forms\user\PasswordReset';
    
    /**
     * @var string
     */
    public $view = 'passwordReset';
    
    /**
     * @inheritdoc
     */
    public function run($token)
    {
        try {
            $model = new $this->modelClass($token);
        }
        catch(\yii\base\InvalidParamException $e) {
            $this->controller->addFlash(Controller::FLASH_ERROR, $e->getMessage());
            return $this->controller->goHome();
        }
        
        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                if ($model->resetPassword()) {
                    $this->controller->addFlash(Controller::FLASH_SUCCESS, Yii::t('app', 'Password has been changed. Now you may login.'));
                    return $this->controller->redirect(['user/login']);
                }
                else {
                    $this->controller->addFlash(Controller::FLASH_ERROR, Yii::t('app', 'Unable to change password.'));
                }
            }
        }
        
        return $this->render([
            'model' => $model,
        ]);
    }
    
}
